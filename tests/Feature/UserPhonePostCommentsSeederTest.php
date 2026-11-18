<?php

use App\Models\User;
use Database\Seeders\UserPhonePostCommentsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('phone post and comment seeding populates related records without duplicates', function () {
    User::factory()->create();

    $seeder = app(UserPhonePostCommentsSeeder::class);
    $seeder->run();
    $seeder->run();

    $this->assertDatabaseCount('user_phones', 5);
    $this->assertDatabaseCount('phones', 5);
    $this->assertDatabaseCount('posts', 2);
    $this->assertDatabaseCount('comments', 4);
    $this->assertDatabaseHas('phones', [
        'phone_number' => '09120522719',
    ]);
    $this->assertDatabaseHas('posts', [
        'title' => 'Welcome to the Community',
    ]);
});
