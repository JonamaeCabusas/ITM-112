<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Seed courses first because student records are enrolled in them.
        $this->call([
            CourseSeeder::class,
            StudentSeeder::class,
        ]);

        // Create the user before seeding posts that reference a user.
        User::firstOrCreate(
            ['email' => 'cabusasjonamae@gmail.com'],
            [
                'name' => 'Jona Mae Cabusas',
                'username' => 'jona',
                'password' => 'cabusas',
            ],
        );

        // Seed phone, post, and comment examples after their required owner/user records exist.
        $this->call(UserPhonePostCommentsSeeder::class);
    }
}