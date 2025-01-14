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
            ['name' => 'Culture', 'mark' => 30, 'marks_range' => [
                'below' => [
                    'from' => 0,
                    'to' => 15,
                ],
                'inline' => [
                    'from' => 16,
                    'to' => 21,
                ],
                'above' => [
                    'from' => 22,
                    'to' => 30,
                ],
            ]],
            ['name' => 'Values', 'mark' => 30, 'marks_range' => [
                'below' => [
                    'from' => 0,
                    'to' => 15,
                ],
                'inline' => [
                    'from' => 16,
                    'to' => 21,
                ],
                'above' => [
                    'from' => 22,
                    'to' => 30,
                ],
            ]],
            ['name' => 'Citizenship', 'mark' => 40, 'marks_range' => [
                'below' => [
                    'from' => 0,
                    'to' => 19,
                ],
                'inline' => [
                    'from' => 20,
                    'to' => 28,
                ],
                'above' => [
                    'from' => 29,
                    'to' => 40,
                ],
            ]],
        ];
        foreach ($subjects as $subject) {
            Subject::query()->updateOrCreate(['name' => $subject['name']], $subject);
        }
    }
}
