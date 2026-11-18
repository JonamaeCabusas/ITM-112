<?php

use App\Models\Post;
use App\Models\User;
use App\Models\UserPhone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('authenticated users can add phone contacts, posts, and comments', function () {
    $user = User::factory()->create();
    $phoneSubmission = [
        'owner_name' => 'Taylor Contact',
        'phone_number' => '+1-202-555-0199',
        '_submission_token' => (string) Str::uuid(),
    ];

    $this->actingAs($user)
        ->post(route('phone-directory.store'), $phoneSubmission)
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('status', 'Phone contact added.');
    $this->post(route('phone-directory.store'), $phoneSubmission);

    $userPhone = UserPhone::query()->where('name', 'Taylor Contact')->firstOrFail();
    $this->assertDatabaseCount('user_phones', 1);
    $this->assertDatabaseCount('phones', 1);
    $this->assertDatabaseHas('phones', [
        'user_phone_id' => $userPhone->id,
        'phone_number' => '+1-202-555-0199',
    ]);

    $postSubmission = [
        'title' => 'A Test Post',
        'body' => 'A post created from the dashboard.',
        '_submission_token' => (string) Str::uuid(),
    ];
    $this->post(route('posts.store'), $postSubmission)
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('status', 'Post published.');
    $this->post(route('posts.store'), $postSubmission);

    $post = Post::query()->where('title', 'A Test Post')->firstOrFail();
    expect($post->user_id)->toBe($user->id);
    $this->assertDatabaseCount('posts', 1);

    $commentSubmission = [
        'comment_body' => 'A test comment.',
        'comment_post_id' => $post->id,
        '_submission_token' => (string) Str::uuid(),
    ];
    $this->post(route('posts.comments.store', $post), $commentSubmission)
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('status', 'Comment added.');
    $this->post(route('posts.comments.store', $post), $commentSubmission);

    $this->assertDatabaseCount('comments', 1);
    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'body' => 'A test comment.',
    ]);
});

test('guests cannot add dashboard content', function () {
    $this->post(route('posts.store'), [
        'title' => 'Guest Post',
        'body' => 'This should not be saved.',
        '_submission_token' => (string) Str::uuid(),
    ])
        ->assertRedirect(route('login'));

    $this->assertDatabaseCount('posts', 0);
});
