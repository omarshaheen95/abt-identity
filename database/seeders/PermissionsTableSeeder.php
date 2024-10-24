<?php

namespace Database\Seeders;

use App\Models\Manager;
use App\Models\Setting;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * php artisan cache:forget spatie.permission.cache
     *
     * php artisan db:seed --class=PermissionsTableSeeder
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Factory $cache)
    {
        $permissions = [
            ['name' => 'show students','guard_name'=>'manager','group'=>'students'],
            ['name' => 'add students','guard_name'=>'manager','group'=>'students'],
            ['name' => 'edit students','guard_name'=>'manager','group'=>'students'],
            ['name' => 'delete students','guard_name'=>'manager','group'=>'students'],
            ['name' => 'export students','guard_name'=>'manager','group'=>'students'],
            ['name' => 'export students marks','guard_name'=>'manager','group'=>'students'],
            ['name' => 'export students cards','guard_name'=>'manager','group'=>'students'],
            ['name' => 'student login','guard_name'=>'manager','group'=>'students'],
            ['name' => 'show deleted students','guard_name'=>'manager','group'=>'students'],
            ['name' => 'restore deleted students','guard_name'=>'manager','group'=>'students'],
            ['name' => 'show students import','guard_name'=>'manager','group'=>'students'],
            ['name' => 'import students','guard_name'=>'manager','group'=>'students'],
            ['name' => 'delete students import','guard_name'=>'manager','group'=>'students'],

            ['name' => 'show inspections','guard_name'=>'manager','group'=>'inspections'],
            ['name' => 'add inspections','guard_name'=>'manager','group'=>'inspections'],
            ['name' => 'edit inspections','guard_name'=>'manager','group'=>'inspections'],
            ['name' => 'delete inspections','guard_name'=>'manager','group'=>'inspections'],
            ['name' => 'export inspections','guard_name'=>'manager','group'=>'inspections'],
            ['name' => 'inspection login','guard_name'=>'manager','group'=>'inspections'],

            ['name' => 'show managers','guard_name'=>'manager','group'=>'managers'],
            ['name' => 'add managers','guard_name'=>'manager','group'=>'managers'],
            ['name' => 'edit managers','guard_name'=>'manager','group'=>'managers'],
            ['name' => 'delete managers','guard_name'=>'manager','group'=>'managers'],
            ['name' => 'export managers','guard_name'=>'manager','group'=>'managers'],
            ['name' => 'edit managers permissions','guard_name'=>'manager','group'=>'managers'],

            ['name' => 'show years','guard_name'=>'manager','group'=>'years'],
            ['name' => 'add years','guard_name'=>'manager','group'=>'years'],
            ['name' => 'edit years','guard_name'=>'manager','group'=>'years'],
            ['name' => 'delete years','guard_name'=>'manager','group'=>'years'],


            ['name' => 'show levels','guard_name'=>'manager','group'=>'levels'],
            ['name' => 'add levels','guard_name'=>'manager','group'=>'levels'],
            ['name' => 'edit levels','guard_name'=>'manager','group'=>'levels'],
            ['name' => 'export levels','guard_name'=>'manager','group'=>'levels'],
            ['name' => 'delete levels','guard_name'=>'manager','group'=>'levels'],
            ['name' => 'levels activation','guard_name'=>'manager','group'=>'levels'],

            ['name' => 'show schools','guard_name'=>'manager','group'=>'schools'],
            ['name' => 'add schools','guard_name'=>'manager','group'=>'schools'],
            ['name' => 'edit schools','guard_name'=>'manager','group'=>'schools'],
            ['name' => 'delete schools','guard_name'=>'manager','group'=>'schools'],
            ['name' => 'export schools','guard_name'=>'manager','group'=>'schools'],
            ['name' => 'school terms scheduling','guard_name'=>'manager','group'=>'schools'],
            ['name' => 'schools general scheduling','guard_name'=>'manager','group'=>'schools'],
            ['name' => 'school login','guard_name'=>'manager','group'=>'schools'],

            ['name' => 'show terms','guard_name'=>'manager','group'=>'terms'],
            ['name' => 'add terms','guard_name'=>'manager','group'=>'terms'],
            ['name' => 'edit terms','guard_name'=>'manager','group'=>'terms'],
            ['name' => 'delete terms','guard_name'=>'manager','group'=>'terms'],
            ['name' => 'export terms','guard_name'=>'manager','group'=>'terms'],
            ['name' => 'terms activation','guard_name'=>'manager','group'=>'terms'],
            ['name' => 'show questions structure','guard_name'=>'manager','group'=>'terms'],
            ['name' => 'add edit questions structure','guard_name'=>'manager','group'=>'terms'],
            ['name' => 'show questions content','guard_name'=>'manager','group'=>'terms'],
            ['name' => 'edit questions content','guard_name'=>'manager','group'=>'terms'],

            ['name' => 'show students not submitted term','guard_name'=>'manager','group'=>'terms'],
            ['name' => 'export students not submitted term','guard_name'=>'manager','group'=>'terms'],

            ['name' => 'show terms questions','guard_name'=>'manager','group'=>'terms'],
            ['name' => 'export terms questions','guard_name'=>'manager','group'=>'terms'],

            ['name' => 'show questions standards','guard_name'=>'manager','group'=>'terms'],
            ['name' => 'edit questions standards','guard_name'=>'manager','group'=>'terms'],
            ['name' => 'export questions standards','guard_name'=>'manager','group'=>'terms'],

            ['name' => 'show marking requests','guard_name'=>'manager','group'=>'marking_requests'],
            ['name' => 'add marking requests','guard_name'=>'manager','group'=>'marking_requests'],
            ['name' => 'edit marking requests','guard_name'=>'manager','group'=>'marking_requests'],
            ['name' => 'delete marking requests','guard_name'=>'manager','group'=>'marking_requests'],

            ['name' => 'show students terms','guard_name'=>'manager','group'=>'students_terms'],
            ['name' => 'edit students terms','guard_name'=>'manager','group'=>'students_terms'],
            ['name' => 'export students terms','guard_name'=>'manager','group'=>'students_terms'],
            ['name' => 'delete students terms','guard_name'=>'manager','group'=>'students_terms'],
            ['name' => 'show deleted students terms','guard_name'=>'manager','group'=>'students_terms'],
            ['name' => 'restore deleted students terms','guard_name'=>'manager','group'=>'students_terms'],
            ['name' => 'auto correct students terms','guard_name'=>'manager','group'=>'students_terms'],

            ['name' => 'show activity logs','guard_name'=>'manager','group'=>'activity_logs'],
            ['name' => 'delete activity logs','guard_name'=>'manager','group'=>'activity_logs'],

            ['name' => 'show settings','guard_name'=>'manager','group'=>'settings'],
            ['name' => 'edit settings','guard_name'=>'manager','group'=>'settings'],

            ['name' => 'show translation','guard_name'=>'manager','group'=>'translation'],
            ['name' => 'edit translation','guard_name'=>'manager','group'=>'translation'],

            ['name' => 'show statistics','guard_name'=>'manager','group'=>'dashboard'],

            ['name' => 'show login sessions','guard_name'=>'manager','group'=>'login_sessions'],

            ['name' => 'copy terms','guard_name'=>'manager','group'=>'terms'],


        ];

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        //insert permissions
        foreach ($permissions as $permission)
        {
            Permission::query()->updateOrCreate($permission, $permission);
        }





    }

}
