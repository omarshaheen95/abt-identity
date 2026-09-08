<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class LoginSessionPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['show login sessions', 'export login sessions'] as $name) {
            $permission = Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'manager'],
                ['group' => 'login_sessions']
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
