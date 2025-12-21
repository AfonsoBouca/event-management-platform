@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow">
            <div class="card-body p-4">
                <h2 class="text-center text-purple mb-4">Login</h2>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            required
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember" value="1" @checked(old('remember'))>
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    <div class="mb-3 text-end">
                        <a href="{{ route('password.request') }}" class="text-decoration-none text-purple">
                            Forgot your password?
                        </a>
                    </div>


                    <button type="submit" class="btn btn-purple w-100 mb-2">
                        Login
                    </button>

                    <a href="{{ route('register') }}" class="btn btn-outline-purple w-100">
                        Register
                    </a>

                    @if (session('status'))
                        <p class="text-success text-center mt-3">{{ session('status') }}</p>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
