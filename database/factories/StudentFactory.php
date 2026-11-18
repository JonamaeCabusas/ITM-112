<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        // Generate a sample student name for seed data and feature tests.
        return [
            'name' => fake()->name(),
        ];
    }
}
