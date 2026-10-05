<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Permission of the "all students assessments" page (corrected and uncorrected together).
 *
 * Added here rather than in PermissionsTableSeeder: the seeder truncates the
 * permissions table and would drop every existing grant.
 *
 * Not granted to anyone: it is given from the roles / permissions screen. The
 * migration has to run with the deployment, the sidebar checks the permission
 * with hasAnyDirectPermission, which throws when it does not exist.
 */
class AddShowAllStudentsTermsPermission extends Migration
{
    const NAME = 'show all students terms';
    const GUARD = 'manager';
    const GROUP = 'students_terms';

    public function up()
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        $exists = DB::table('permissions')
            ->where('name', self::NAME)->where('guard_name', self::GUARD)->exists();

        if (!$exists) {
            $row = [
                'name' => self::NAME,
                'guard_name' => self::GUARD,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('permissions', 'group')) {
                $row['group'] = self::GROUP;
            }
            DB::table('permissions')->insert($row);
        }

        $this->forgetCache();
    }

    public function down()
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        $id = DB::table('permissions')
            ->where('name', self::NAME)->where('guard_name', self::GUARD)->value('id');

        if (!$id) {
            return;
        }

        DB::table('role_has_permissions')->where('permission_id', $id)->delete();
        DB::table('model_has_permissions')->where('permission_id', $id)->delete();
        DB::table('permissions')->where('id', $id)->delete();

        $this->forgetCache();
    }

    /** Inserting through DB does not reach the Spatie cache, so it is cleared here. */
    private function forgetCache()
    {
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }
}
