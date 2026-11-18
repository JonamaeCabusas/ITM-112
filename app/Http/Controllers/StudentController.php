<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Post;
use App\Models\Student;
use App\Models\UserPhone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        // Load courses with each student to display their many-to-many enrollments without extra queries.
        $data = [
            'students' => Student::query()->with('courses')->latest()->get(),
            'courses' => Course::query()->orderBy('title')->get(),
            'userPhones' => collect(),
            'posts' => collect(),
        ];

        if (auth()->check()) {
            // Only signed-in users can see the phone directory and posts with their related records.
            $data['userPhones'] = UserPhone::query()->with('phone')->orderBy('name')->get();
            $data['posts'] = Post::query()->with(['user', 'comments'])->latest()->get();
        }

        return view('students.index', $data);
    }

    public function store(Request $request): RedirectResponse
    {
        // Require a valid student name and at least one existing course to enroll in.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'course_ids' => ['required', 'array', 'min:1'],
            'course_ids.*' => ['required', 'integer', 'distinct', 'exists:courses,id'],
        ]);

        // Save the student and pivot-table enrollments together so neither can be left incomplete.
        DB::transaction(function () use ($validated): void {
            $student = Student::create(['name' => $validated['name']]);
            $student->courses()->attach($validated['course_ids']);
        });

        return to_route('students.index')->with('status', 'Student added with selected courses.');
    }
}
