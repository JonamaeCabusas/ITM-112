<?php

use App\Http\Controllers\DashboardContentController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StudentController;
use App\Http\Middleware\PreventDuplicateSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return auth()->check()
        ? to_route('dashboard')
        : to_route('login');
});

Route::middleware('guest')->group(function () {
    Route::view('/login', 'login')->name('login');
    Route::view('/register', 'register')->name('register');

    Route::post('/login', LoginController::class)->name('login.attempt');

    Route::post('/register', RegisterController::class)
        ->middleware(PreventDuplicateSubmission::class)
        ->name('register.store');
});

Route::get('/dashboard', [StudentController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentController::class, 'store'])
        ->middleware(PreventDuplicateSubmission::class)
        ->name('students.store');
    Route::post('/phone-directory', [DashboardContentController::class, 'storePhone'])
        ->middleware(PreventDuplicateSubmission::class)
        ->name('phone-directory.store');
    Route::post('/posts', [DashboardContentController::class, 'storePost'])
        ->middleware(PreventDuplicateSubmission::class)
        ->name('posts.store');
    Route::post('/posts/{post}/comments', [DashboardContentController::class, 'storeComment'])
        ->middleware(PreventDuplicateSubmission::class)
        ->name('posts.comments.store');
});

// End the authenticated session and invalidate its session data 
Route::post('/logout', function () {
    Auth::guard('web')->logout();

    Session::invalidate();
    Session::regenerateToken();

    return redirect('/');
})->name('logout');

