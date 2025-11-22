@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 400px; margin: 50px auto; background-color: #fff; padding: 30px; border-radius: 8px;">

    <h2 style="text-align: center; color: #6a0dad; margin-bottom: 25px;">Login</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email" style="color:#000;">E-mail</label>
        <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email') }}"
            required
            autofocus
            style="width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 10px; border-radius: 5px; border:1px solid #ccc; color:#000;"
        >
        @error('email')
            <span style="color: #dc3545; font-size:0.85em;">{{ $message }}</span>
        @enderror

        <label for="password" style="color:#000;">Password</label>
        <input
            id="password"
            name="password"
            type="password"
            required
            style="width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 10px; border-radius: 5px; border:1px solid #ccc; color:#000;"
        >
        @error('password')
            <span style="color: #dc3545; font-size:0.85em;">{{ $message }}</span>
        @enderror

        <label style="color:#000;">
            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
            Remember me
        </label>

        <button type="submit" class="view-event-btn" style="width:100%; margin-top:15px;">
            Login
        </button>

        <a href="{{ route('register') }}" class="view-event-btn" style="width:100%; margin-top:10px; display:block; text-align:center;">
            Register
        </a>

        @if (session('status'))
            <p class="success" style="color: #28a745; text-align:center; margin-top:10px;">{{ session('status') }}</p>
        @endif
    </form>
</div>
@endsection
