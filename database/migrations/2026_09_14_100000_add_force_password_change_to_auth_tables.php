<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForcePasswordChangeToAuthTables extends Migration
{
    /**
     * Tables that take part in the forced password change policy.
     */
    private $tables = ['managers', 'schools', 'inspections'];

    public function up()
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (!Schema::hasColumn($table, 'force_password_change')) {
                    $blueprint->boolean('force_password_change')->default(0)->after('password');
                }
                if (!Schema::hasColumn($table, 'password_changed_at')) {
                    $blueprint->timestamp('password_changed_at')->nullable()->after('force_password_change');
                }
            });
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (Schema::hasColumn($table, 'force_password_change')) {
                    $blueprint->dropColumn('force_password_change');
                }
                if (Schema::hasColumn($table, 'password_changed_at')) {
                    $blueprint->dropColumn('password_changed_at');
                }
            });
        }
    }
}
