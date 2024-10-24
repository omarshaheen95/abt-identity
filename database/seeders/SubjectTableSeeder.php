<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subjects = [
            ['name'=>'Culture','mark'=>40],
            ['name'=>'Values','mark'=>30],
            ['name'=>'Citizenship','mark'=>30],
        ];
       foreach($subjects as $subject){
           Subject::query()->updateOrCreate(['name'=>$subject['name']],$subject);
       }
    }
}
