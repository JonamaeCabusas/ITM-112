<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Course;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Avoid adding another batch of demo students when the seeder is run again.
        if (Student::query()->exists()) {
            return;
        }

        $course = Course::all();

        // Create sample students and connect each to one or two courses 
        Student::factory()->count(5)->create()->each(function ($student) {
            $courses = \App\Models\Course::inRandomOrder()->take(rand(1, 2))->pluck('id');
            $student->courses()->attach($courses);
        });
    }
}
