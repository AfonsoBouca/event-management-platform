@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 400px; margin: 50px auto; background-color: #fff; padding: 30px; border-radius: 8px;">

    <h2 style="text-align: center; color: #6a0dad; margin-bottom: 25px;">Register</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label for="username" style="color:#000;">Username</label>
        <input
            id="username"
            type="text"
            name="username"
            value="{{ old('username') }}"
            required
            autofocus
            autocomplete="username"
            style="width:100%; padding:10px; margin-top:5px; margin-bottom:10px; border-radius:5px; border:1px solid #ccc; color:#000;"
        >
        @error('username')
            <span style="color: #dc3545; font-size:0.85em;">{{ $message }}</span>
        @enderror

        <label for="email" style="color:#000;">E-Mail Address</label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
            autocomplete="email"
            style="width:100%; padding:10px; margin-top:5px; margin-bottom:10px; border-radius:5px; border:1px solid #ccc; color:#000;"
        >
        @error('email')
            <span style="color: #dc3545; font-size:0.85em;">{{ $message }}</span>
        @enderror

        <label for="password" style="color:#000;">Password</label>
        <input
            id="password"
            type="password"
            name="password"
            required
            autocomplete="new-password"
            style="width:100%; padding:10px; margin-top:5px; margin-bottom:10px; border-radius:5px; border:1px solid #ccc; color:#000;"
        >
        @error('password')
            <span style="color: #dc3545; font-size:0.85em;">{{ $message }}</span>
        @enderror

        <label for="password-confirm" style="color:#000;">Confirm Password</label>
        <input
            id="password-confirm"
            type="password"
            name="password_confirmation"
            required
            autocomplete="new-password"
            style="width:100%; padding:10px; margin-top:5px; margin-bottom:10px; border-radius:5px; border:1px solid #ccc; color:#000;"
        >

        <button type="submit" class="view-event-btn" style="width:100%; margin-top:15px;">
            Register
        </button>

        <a href="{{ route('login') }}" class="view-event-btn" style="width:100%; margin-top:10px; display:block; text-align:center;">
            Login
        </a>
    </form>
</div>
@endsection
