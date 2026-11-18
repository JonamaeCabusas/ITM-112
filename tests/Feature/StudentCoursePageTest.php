<?php

use App\Models\Course;
use App\Models\Post;
use App\Models\Student;
use App\Models\User;
use App\Models\UserPhone;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the login page renders', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSeeText('Sign In');
});

test('guests cannot access the student page or dashboard', function () {
    $this->get(route('students.index'))->assertRedirect(route('login'));
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('the student page displays students and their courses to authenticated users', function () {
    $student = Student::factory()->create(['name' => 'Taylor Student']);
    $course = Course::create(['title' => 'BSIT']);
    $student->courses()->attach($course);

    $this->actingAs(User::factory()->create())
        ->get(route('students.index'))
        ->assertOk()
        ->assertSeeText('Taylor Student')
        ->assertSeeText('BSIT')
        ->assertSee('Add student');
});

test('authenticated users are redirected away from login and registration pages', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('login'))
        ->assertRedirect(route('dashboard'));

    $this->get(route('register'))
        ->assertRedirect(route('dashboard'));
});

test('the home page sends authenticated users to the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertRedirect(route('dashboard'));
});

test('the authenticated dashboard displays the student management page', function () {
    $student = Student::factory()->create(['name' => 'Taylor Student']);
    $course = Course::create(['title' => 'BSIT']);
    $student->courses()->attach($course);

    $this->actingAs(User::factory()->create(['username' => 'dashboard-user']))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeText('Taylor Student')
        ->assertSeeText('BSIT')
        ->assertSee('Add student')
        ->assertSee('Log out');
});

test('the authenticated dashboard displays phone owners, posts, and comments', function () {
    $user = User::factory()->create(['name' => 'Post Author']);
    $userPhone = UserPhone::create(['name' => 'Phone Owner']);
    $userPhone->phone()->create(['phone_number' => '555-0101']);

    $post = Post::create([
        'user_id' => $user->id,
        'title' => 'Community Update',
        'body' => 'A sample post body.',
    ]);
    $post->comments()->create(['body' => 'A sample comment.']);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeText('Phone Directory')
        ->assertSeeText('Phone Owner')
        ->assertSeeText('555-0101')
        ->assertSeeText('Posts & Comments')
        ->assertSeeText('Add Contact')
        ->assertSeeText('New Post')
        ->assertSeeText('Community Update')
        ->assertSeeText('Post Author')
        ->assertSeeText('A sample comment.')
        ->assertSeeText('Add Comment');
});

test('adding a student attaches selected courses', function () {
    $courses = collect([
        Course::create(['title' => 'BSIT']),
        Course::create(['title' => 'HM']),
    ]);

    $this->actingAs(User::factory()->create())
        ->post('/students', [
            'name' => 'Jordan Student',
            'course_ids' => $courses->pluck('id')->all(),
            '_submission_token' => (string) \Illuminate\Support\Str::uuid(),
        ])
        ->assertRedirect(route('students.index'))
        ->assertSessionHas('status', 'Student added with selected courses.');

    $student = Student::query()->where('name', 'Jordan Student')->firstOrFail();
    expect($student->courses)->toHaveCount(2);
    $this->assertDatabaseCount('course_student', 2);
});

test('adding a student requires at least one valid course', function () {
    $this->actingAs(User::factory()->create())
        ->post('/students', [
            'name' => 'Jordan Student',
            '_submission_token' => (string) \Illuminate\Support\Str::uuid(),
        ])
        ->assertSessionHasErrors('course_ids');

    $this->assertDatabaseCount('students', 0);
});

test('replaying a student form submission does not create another student', function () {
    $user = User::factory()->create();
    $course = Course::create(['title' => 'BSIT']);
    $token = (string) \Illuminate\Support\Str::uuid();
    $submission = [
        'name' => 'Jordan Student',
        'course_ids' => [$course->id],
        '_submission_token' => $token,
    ];

    $this->actingAs($user)->post(route('students.store'), $submission);
    $this->post(route('students.store'), $submission);

    $this->assertDatabaseCount('students', 1);
    $this->assertDatabaseCount('course_student', 1);
});

test('registering creates a user account', function () {
    $this->post(route('register.store'), [
        'name' => 'New User',
        'username' => 'new-user',
        'email' => 'new-user@example.test',
        'password' => 'password123',
        '_submission_token' => (string) \Illuminate\Support\Str::uuid(),
    ])
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('users', [
        'username' => 'new-user',
        'email' => 'new-user@example.test',
    ]);
});

test('five failed login attempts block further guesses for one minute', function () {
    $user = User::factory()->create([
        'username' => 'login-user',
        'password' => 'correct-password',
    ]);

    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post(route('login.attempt'), [
            'username' => $user->username,
            'password' => 'wrong-password',
        ]);
    }

    $this->post(route('login.attempt'), [
        'username' => $user->username,
        'password' => 'correct-password',
    ])->assertSessionHasErrors('username');

    $this->assertGuest();
});
