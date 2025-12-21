@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Forgot your password?</h2>


    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <label for="email">Email</label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
            class="form-control mb-3
                   @error('email') is-invalid @enderror"
        >

        <button type="submit" class="btn btn-purple">
            Send reset link
        </button>
    </form>
</div>
@endsection
