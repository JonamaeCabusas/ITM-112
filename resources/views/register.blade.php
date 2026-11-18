<x-layout>
    {{-- Collect account details and submit them to the registration controller. --}}
    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <input type="hidden" name="_submission_token" value="{{ \Illuminate\Support\Str::uuid() }}">
        <x-form-errors/>
        <input type="text" name="name" placeholder="Name"/>
         <input type="text" name="username" placeholder="Username"/>
        <input type="email" name="email" placeholder="Email"/>
        <input type="password" name="password" placeholder="Password"/>

        <button type="submit">Register</button>
    </form>

    <div class="form-footer">
        Already have an account?
        <a href="{{ route('login') }}">Log In</a>
    </div>
</x-layout>