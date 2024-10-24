<?php

namespace Database\Seeders;

use App\Models\Manager;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manager = Manager::query()->firstOrCreate(['email' => 'support@abt-assessments.com'],[
            'name' => 'General Manager',
            'password' => bcrypt(123456),
            'email' => 'support@abt-assessments.com',
            'approved' => 1,
        ]);

        $all_manager_permission = Permission::query()
            ->where('guard_name','manager')->get()->pluck('name')->toArray();
        $manager->givePermissionTo($all_manager_permission);

    }
}
