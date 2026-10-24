<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columns spatie/laravel-activitylog v4 writes on every entry.
 *
 * Added at the end of the table (no ->after()) so MySQL does not have to
 * rebuild the whole activity_log table to place them.
 */
return new class extends Migration
{
    public function up()
    {
        $schema = Schema::connection(config('activitylog.database_connection'));
        $table = config('activitylog.table_name');

        $schema->table($table, function (Blueprint $blueprint) use ($schema, $table) {
            if (! $schema->hasColumn($table, 'event')) {
                $blueprint->string('event')->nullable();
            }
            if (! $schema->hasColumn($table, 'batch_uuid')) {
                $blueprint->uuid('batch_uuid')->nullable();
            }
        });
    }

    public function down()
    {
        $schema = Schema::connection(config('activitylog.database_connection'));
        $table = config('activitylog.table_name');

        $schema->table($table, function (Blueprint $blueprint) use ($schema, $table) {
            foreach (['event', 'batch_uuid'] as $column) {
                if ($schema->hasColumn($table, $column)) {
                    $blueprint->dropColumn($column);
                }
            }
        });
    }
};
