<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        // Validate the submitted login fields before checking the user's credentials.
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $key = Str::lower($credentials['username']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors([
                'username' => 'Too many login attempts. Please try again in '.RateLimiter::availableIn($key).' seconds.',
            ])->onlyInput('username');
        }

        // Authenticate the user, then refresh the session ID to protect the signed-in session.
        if (Auth::attempt($credentials)) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        RateLimiter::hit($key, 60);

        // Return a validation-style error when the supplied credentials do not match.
        return back()->withErrors([
            'username' => RateLimiter::tooManyAttempts($key, 5)
                ? 'Too many login attempts. Please try again in '.RateLimiter::availableIn($key).' seconds.'
                : 'The provided username and password do not match our records.',
        ])->onlyInput('username');
    }
}
