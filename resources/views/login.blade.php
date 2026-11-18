<x-layout>
    <h2>Welcome Back</h2>
    <p>Please enter your details to sign in.</p>

    {{-- Send the username and password to the login controller with a CSRF token. --}}
    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf
         <x-form-errors/>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Sign In</button>
    </form>

    <div class="form-footer">
        Don't have an account?
        <a href="{{ route('register') }}">Register</a>
    </div>
</x-layout>