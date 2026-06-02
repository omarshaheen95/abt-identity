<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Promote every JSON key inside `student_terms.dates_at` to an indexable VIRTUAL
 * generated column so date/corrector/emergency filters can use a B-tree index
 * instead of full-scanning every row and parsing JSON.
 *
 * VIRTUAL (not STORED): no row growth / no extra table storage; only the indexes
 * materialise the value; adding the column is INSTANT/online on MySQL 8; and
 * `dates_at` stays the single source of truth so nothing can drift.
 *
 * The REGEXP gate is essential: CAST/STR_TO_DATE THROW (not warn) while MySQL
 * materialises a generated column during CREATE INDEX under strict mode, so a
 * single dirty value (e.g. a bare '2023') would abort the migration. Gating to a
 * full 'Y-m-d H:i:s' shape makes every malformed/null/''/'null' value resolve to
 * NULL before conversion. Built in strict mode so index and reads stay consistent.
 */
class AddGeneratedDateColumnsToStudentTermsTable extends Migration
{
    private  $table = 'student_terms';

    /** Generated DATETIME column => source JSON key. */
    private  $dateColumns = [
        'v_started_at'   => 'started_at',
        'v_submitted_at' => 'submitted_at',
        'v_corrected_at' => 'corrected_at',
    ];

    /** Generated integer column => [json key, sql type]. */
    private  $intColumns = [
        'v_corrected_by'   => ['corrected_by', 'BIGINT UNSIGNED'],
        'v_emergency_save' => ['emergency_save', 'TINYINT UNSIGNED'],
    ];

    private  $indexes = [
        // Corrector stats are admin-scoped (filter by corrected_by, no school join):
        'st_v_corrected_idx'      => '(`v_corrected_by`, `v_corrected_at`)',
        'st_v_corrected_at_idx'   => '(`v_corrected_at`)',
        // Started/submitted stats are school-scoped: student_id leads so a
        // "WHERE student_id IN (school) AND date BETWEEN" query is index-served.
        'st_v_started_idx'        => '(`student_id`, `v_started_at`)',
        'st_v_submitted_idx'      => '(`student_id`, `v_submitted_at`)',
        'st_v_emergency_save_idx' => '(`v_emergency_save`)',
    ];

    public function up()
    {
        foreach (array_keys($this->indexes) as $name) {
            $this->dropIndexIfExists($name);
        }
        foreach ($this->allColumns() as $column) {
            $this->dropColumnIfExists($column);
        }

        foreach ($this->dateColumns as $column => $key) {
            DB::statement(
                "ALTER TABLE `{$this->table}`
                 ADD COLUMN `{$column}` DATETIME
                 GENERATED ALWAYS AS (
                     CASE
                         WHEN JSON_UNQUOTE(JSON_EXTRACT(dates_at, '$.{$key}'))
                              REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$'
                         THEN STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(dates_at, '$.{$key}')), '%Y-%m-%d %H:%i:%s')
                         ELSE NULL
                     END
                 ) VIRTUAL"
            );
        }

        foreach ($this->intColumns as $column => $def) {
            list($key, $type) = $def;
            DB::statement(
                "ALTER TABLE `{$this->table}`
                 ADD COLUMN `{$column}` {$type}
                 GENERATED ALWAYS AS (
                     CASE
                         WHEN JSON_UNQUOTE(JSON_EXTRACT(dates_at, '$.{$key}')) REGEXP '^[0-9]+$'
                         THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(dates_at, '$.{$key}')) AS UNSIGNED)
                         ELSE NULL
                     END
                 ) VIRTUAL"
            );
        }

        foreach ($this->indexes as $name => $columns) {
            $this->addIndexIfMissing($name, $columns);
        }
    }

    public function down()
    {
        foreach (array_keys($this->indexes) as $name) {
            $this->dropIndexIfExists($name);
        }
        foreach ($this->allColumns() as $column) {
            $this->dropColumnIfExists($column);
        }
    }

    private function allColumns(): array
    {
        return array_merge(array_keys($this->dateColumns), array_keys($this->intColumns));
    }

    private function dropColumnIfExists(string $column): void
    {
        if (Schema::hasColumn($this->table, $column)) {
            DB::statement("ALTER TABLE `{$this->table}` DROP COLUMN `{$column}`");
        }
    }

    private function addIndexIfMissing(string $name, string $columns): void
    {
        if (! $this->indexExists($name)) {
            DB::statement("CREATE INDEX `{$name}` ON `{$this->table}` {$columns}");
        }
    }

    private function dropIndexIfExists(string $name): void
    {
        if ($this->indexExists($name)) {
            DB::statement("DROP INDEX `{$name}` ON `{$this->table}`");
        }
    }

    private function indexExists(string $name): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $this->table)
            ->where('index_name', $name)
            ->exists();
    }
}
