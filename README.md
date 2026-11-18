# Laravel Student, Phone, Post, and Authentication Dashboard

This Laravel 12 project includes username authentication and a dashboard for students, courses, phone contacts, posts, and comments. Its Eloquent relationships are documented below in this order: one-to-one, one-to-many, and many-to-many.

## Relationship files

### One-to-one: phone-directory owners and phones

| File | Purpose |
|---|---|
| `app/Models/UserPhone.php` | Defines the phone-directory owner and its `phone()` one-to-one relationship. |
| `app/Models/Phone.php` | Defines the phone number and its inverse `userPhone()` relationship. |
| `database/migrations/2026_10_29_062547_create_user_phones_table.php` | Creates phone-directory owner names. |
| `database/migrations/2026_10_29_063211_create_phones_table.php` | Creates phone numbers linked to an owner. |

### One-to-many: users, posts, and comments

| File | Purpose |
|---|---|
| `app/Models/User.php` | Defines the authenticated user who owns posts. |
| `app/Models/Post.php` | Defines a post, its author, and its `comments()` relationship. |
| `app/Models/Comment.php` | Defines a comment and its parent post. |
| `database/migrations/2026_10_29_141202_create_posts_table.php` | Creates posts with a foreign key to their author in `users`. |
| `database/migrations/2026_10_29_141221_create_comments_table.php` | Creates comments with a foreign key to their post. |

### Many-to-many: students and courses

| File | Purpose |
|---|---|
| `app/Models/Student.php` | Defines the `Student` model, allows `name` to be mass assigned, enables model factories, and defines the `courses()` relationship. |
| `app/Models/Course.php` | Defines the `Course` model, allows `title` to be mass assigned, and defines the inverse `students()` relationship. |
| `database/migrations/2026_10_29_095007_create_students_table.php` | Creates the `students` table with a name and timestamps. |
| `database/migrations/2026_10_29_095026_create_courses_table.php` | Creates the `courses` table with a title and timestamps. |
| `database/migrations/2026_10_29_095935_create_course_student_table.php` | Creates the pivot table that links students and courses. |
| `database/factories/StudentFactory.php` | Generates fake student names for tests or seed data. |
| `database/seeders/CourseSeeder.php` | Adds the sample courses `BSIT` and `HM`. |
| `database/seeders/StudentSeeder.php` | Creates five students and assigns each one or two randomly selected courses. |
| `database/seeders/UserPhonePostCommentsSeeder.php` | Adds repeat-safe sample phone owners, phones, posts, and comments. |
| `database/seeders/DatabaseSeeder.php` | Runs the course, student, user, and phone/post/comment seeders in dependency order. |

## How the relationship works

```text
students                     course_student                     courses
---------                    --------------                     -------
id                           student_id -> students.id          id
name                         course_id  -> courses.id           title
                             created_at
                             updated_at
```

The `course_student` table is a **pivot table**. Each row represents one student-course enrollment. Its foreign keys cascade on delete, so deleting a student or course also deletes the related pivot rows.

In `Student.php`, `courses()` returns a `BelongsToMany` relationship to `Course`. In `Course.php`, `students()` defines the inverse relationship to `Student`. Laravel infers the pivot table name `course_student` and its `student_id` and `course_id` keys from these model names.

The student-side relationship uses `withTimestamps()`, which tells Eloquent to maintain the pivot table's `created_at` and `updated_at` values when attaching courses through a student.

## Example usage

```php
use App\Models\Course;
use App\Models\Student;

$student = Student::find(1);
$courses = $student->courses;

$course = Course::find(1);
$students = $course->students;

// Enroll a student in a course.
$student->courses()->attach($course->id);
```

`attach()` adds a link in the pivot table. To remove an enrollment, use:

```php
$student->courses()->detach($course->id);
```

## Run the project and seed sample data

After installing dependencies and configuring the database in `.env`, run:

```bash
php artisan migrate --seed
```

`UserPhonePostCommentsSeeder` inserts sample records for the phone and post tables. It uses `firstOrCreate`, so running `php artisan db:seed` again will not duplicate those records.

## Student and course web page

The root route (`/`) redirects to `/login`. After login, `/dashboard` displays students and their enrolled courses through the existing many-to-many relationship. The **Add student** form lets you enter a name and select one or more existing courses. Submitting the form creates the student and inserts the selected student-course links in `course_student`.

### Files added or updated for the web page

| File | Change |
|---|---|
| `app/Http/Controllers/StudentController.php` | Loads students with their courses and validates, creates, and enrolls a new student. |
| `app/Models/Course.php` | Allows course titles to be mass assigned when seeding courses. |
| `resources/views/students/index.blade.php` | Displays students and course tags, with a form for adding a student and selecting courses. |
| `routes/web.php` | Redirects `/` to `/login` and registers the student list and add-student routes. |
| `tests/Feature/StudentCoursePageTest.php` | Tests listing relationships, adding a student with courses, and rejecting a missing course selection. |
| `tests/Feature/ExampleTest.php` | Contains the starter root-route smoke test. |
| `README.md` | Documents the page and the code flow below. |

### How adding a student works

The controller validates the entered name and selected course IDs, creates the student, then attaches the selected courses through the existing `courses()` relationship. The database transaction ensures both the student and its enrollments are saved together:

```php
DB::transaction(function () use ($validated): void {
    $student = Student::create(['name' => $validated['name']]);
    $student->courses()->attach($validated['course_ids']);
});
```

The page uses eager loading (`with('courses')`) to show each student's courses. Its form includes Laravel's `@csrf` token and renders course checkboxes using the courses already in the database.

Authenticated users can also add phone contacts, publish posts, and comment on posts from the dashboard. Each form validates its input, and new posts are attributed to the signed-in user.

The current routes protect `/dashboard` and the phone/post/comment creation actions with authentication. The `/students` list and add-student routes are not behind the `auth` middleware.

Open the page after configuring the database and applying migrations and seed data:

```bash
php artisan migrate --seed
php artisan serve
```

Then visit `http://127.0.0.1:8000`.

To run the test suite:

```bash
php artisan test
```

## Login-to-dashboard file guide

This section follows the request from the login screen to the authenticated dashboard and describes the files that make that flow work.

### Routes, controllers, and views

| File | What it does |
|---|---|
| `routes/web.php` | Maps URLs to views and controllers. `/` redirects to `/login`; `GET /login` displays the form; `POST /login` checks credentials; `GET /dashboard` requires authentication. It also registers registration, logout, student, phone, post, and comment routes. |
| `app/Http/Controllers/LoginController.php` | Validates the submitted username and password. `Auth::attempt()` checks them against a stored user. On success it regenerates the session ID and redirects to the intended dashboard. Invalid credentials are returned as form errors. |
| `app/Http/Controllers/RegisterController.php` | Validates registration fields, hashes the password, creates a `User`, logs that user in, and redirects to the dashboard. |
| `app/Http/Controllers/StudentController.php` | Loads dashboard data and returns `students.index`. It eager-loads students with courses, and for signed-in users it also loads phone owners with phones and posts with authors and comments. Its `store()` method validates and saves a student and course links in a database transaction. |
| `app/Http/Controllers/DashboardContentController.php` | Handles the dashboard forms. It validates and saves phone contacts, creates posts attributed to the signed-in user, and creates comments for the post in the URL. |
| `resources/views/login.blade.php` | Login form. Its input names (`username` and `password`) match the fields validated by `LoginController`. |
| `resources/views/register.blade.php` | Registration form for a name, username, email, and password. |
| `resources/views/components/layout.blade.php` | Shared HTML/Bootstrap layout for the login and registration pages. The `<x-layout>` component supplies the page content through its `$slot`. |
| `resources/views/components/form-errors.blade.php` | Reusable Blade component that displays validation and login errors stored in the session. |
| `resources/views/students/index.blade.php` | The dashboard page. It renders student/course rows, the phone directory, posts, authors, and comments. `@auth` shows the phone and post sections only to signed-in users; `@forelse` displays rows or an empty-state message. Bootstrap forms submit the add actions. |
| `resources/views/dashboard.blade.php` | A separate dashboard template currently not used by the `/dashboard` route. The route uses `StudentController@index`, which returns `students.index`. |

`@csrf` adds a CSRF token to each form. `@error` displays validation feedback, and `old()` repopulates submitted values after validation fails. The Bootstrap collapse controls open and close the phone-contact and post forms.

### Database migrations

Migrations define tables and constraints; they do not insert sample rows. `up()` applies the schema change and `down()` reverses it.

| Migration file | Tables and purpose |
|---|---|
| `database/migrations/0001_01_01_000000_create_users_table.php` | Creates `users`, password-reset tokens, and session storage. |
| `database/migrations/2026_11_15_084324_add_username_to_users_table.php` | Adds a nullable, unique `username` column used by login and registration. |
| `database/migrations/2026_10_29_062547_create_user_phones_table.php` | Creates phone-directory owner names. |
| `database/migrations/2026_10_29_063211_create_phones_table.php` | Creates phone numbers and links each number to a `user_phones` row. |
| `database/migrations/2026_10_29_141202_create_posts_table.php` | Creates post title/body and a foreign key to the author in `users`. |
| `database/migrations/2026_10_29_141221_create_comments_table.php` | Creates comment bodies and a foreign key to the related post. |
| `database/migrations/2026_10_29_095007_create_students_table.php` | Creates student names. |
| `database/migrations/2026_10_29_095026_create_courses_table.php` | Creates course titles. |
| `database/migrations/2026_10_29_095935_create_course_student_table.php` | Creates the many-to-many enrollment table. Its foreign keys cascade when a linked student or course is deleted. |

### Models and relationships

| Model | Relationship or purpose |
|---|---|
| `app/Models/User.php` | Laravel's authenticatable user. Defines mass-assignable account fields and hides the password and remember token. |
| `app/Models/UserPhone.php` | Phone-directory owner name; `phone()` is a `hasOne` relationship. |
| `app/Models/Phone.php` | Phone number; `userPhone()` is the inverse `belongsTo` relationship. |
| `app/Models/Post.php` | Post title/body; `user()` points to its author and `comments()` returns its comments. |
| `app/Models/Comment.php` | Comment body; `post()` points to the post it belongs to. |
| `app/Models/Student.php` | Student record; `courses()` is a `belongsToMany` relationship. |
| `app/Models/Course.php` | Course record; `students()` is the inverse many-to-many relationship. |

The phone directory is currently separate from login accounts: `user_phones` stores a name but does not have a `user_id` foreign key. Posts, in contrast, are linked to the signed-in account through `posts.user_id`.

### Seeders, factories, and tests

| File | What it does |
|---|---|
| `database/seeders/DatabaseSeeder.php` | Runs the course and student seeders, creates the configured demonstration user if needed, then runs the phone/post/comment seeder. |
| `database/seeders/CourseSeeder.php` | Uses `firstOrCreate()` to add the `BSIT` and `HM` courses without duplicating them on later runs. |
| `database/seeders/StudentSeeder.php` | Creates five factory students only when the students table is empty, then attaches one or two random courses to each. |
| `database/seeders/UserPhonePostCommentsSeeder.php` | Creates sample phone owners/numbers, posts, and comments. `firstOrCreate()` makes repeated seeding safe from duplicate demo rows. |
| `database/factories/StudentFactory.php` | Uses Faker to make student names for seeding and tests. |
| `database/factories/UserFactory.php` | Makes test users with generated account details and a hashed test password. |
| `tests/Feature/StudentCoursePageTest.php` | Checks login page rendering, student/course display, adding students, and registration. |
| `tests/Feature/DashboardContentTest.php` | Checks authenticated phone, post, and comment creation and verifies guests cannot create dashboard content. |
| `tests/Feature/UserPhonePostCommentsSeederTest.php` | Checks the sample seeder creates related data and does not duplicate it when run twice. |

### Request and data flow

1. A browser request to `/` is redirected to `/login`. The login Blade view sends a CSRF-protected `POST /login` request.
2. `LoginController` validates the fields and calls `Auth::attempt()`. Laravel verifies the credentials; regenerating the session ID after success helps protect the authenticated session.
3. The `auth` middleware on `GET /dashboard` blocks guests. For a signed-in user, `StudentController@index` queries the related records and returns `resources/views/students/index.blade.php`.
4. Eloquent `with(...)` eager-loads related records in fewer queries: `courses`, `phone`, and each post's `user` and `comments`.
5. The dashboard's phone, post, and comment forms submit to authenticated POST routes. `Request::validate()` rejects missing or invalid fields and returns errors to the form.
6. `DashboardContentController` saves a phone owner and number together in a transaction, assigns new posts to the authenticated user, and saves each comment through its post relationship.
7. Successful writes redirect to the dashboard with a status message. The view then queries and displays the newly saved records.

### Data relationship summary

```text
user_phones 1 ---- zero or one phone

users 1 ---- many posts 1 ---- many comments

students 1 ---- many course_student rows ---- 1 courses
```

### Run the application

```bash
php artisan migrate --seed
php artisan serve
```

Open `http://127.0.0.1:8000/login`, then sign in or register. `php artisan migrate` creates tables; `php artisan db:seed` inserts the sample records. Run `php artisan test` to execute the automated tests.
