<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use resources\views\login;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
       // Validate account details before creating a new user.
       $userData = $request->validate([
            'name' => ['required', 'string'],
            'username' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Hash the password before saving it to the users table.
        $userData['password'] = bcrypt($userData['password']);

        $user = User::create($userData);

        // Sign in the newly registered user and send them to the dashboard.
        Auth::login($user);

        return redirect()->route('dashboard');
    }

}