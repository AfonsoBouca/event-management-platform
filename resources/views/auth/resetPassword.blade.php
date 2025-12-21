@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Reset Password</h2>

    {{-- Erros --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" value="{{ $email }}" disabled>
        </div>

        <div class="mb-3">
            <label>New password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Confirm password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button class="btn btn-purple">
            Reset password
        </button>
    </form>
</div>
@endsection
