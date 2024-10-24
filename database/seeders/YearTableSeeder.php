<?php

namespace Database\Seeders;

use App\Models\Year;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class YearTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $years = [
          ['name'=>['ar' => '2023/2024', 'en' => '2023/2024'],'default'=>0],
          ['name'=>['ar' => '2024/2025', 'en' => '2024/2025'],'default'=>1],
        ];
        foreach($years as $year)
        {
            Year::query()->updateOrCreate([
                'name' => $year['name'],
            ],$year);
        }
    }
}
