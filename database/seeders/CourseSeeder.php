<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the courses exist before students are seeded and enrolled.
        Course::firstOrCreate([
            'title' => 'BSIT',
        ]);
        Course::firstOrCreate([
            'title' => 'HM',
        ]);
    }
}
