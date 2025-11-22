<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        /* --- Reset básico --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            background-color: #fff;
            padding: 40px 35px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: #17a2b8;
            margin-bottom: 25px;
            font-size: 1.8em;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #333;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.95em;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }

        button {
            margin-top: 20px;
            padding: 10px 15px;
            width: 100%;
            background-color: #17a2b8;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #138496;
        }

        .button-outline {
            display: block;
            text-align: center;
            margin-top: 10px;
            padding: 10px 15px;
            border: 1px solid #17a2b8;
            color: #17a2b8;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .button-outline:hover {
            background-color: #17a2b8;
            color: #fff;
        }

        .error {
            color: #dc3545;
            font-size: 0.85em;
            margin-top: 3px;
            display: block;
        }

        .success {
            color: #28a745;
            font-size: 0.9em;
            margin-top: 15px;
            text-align: center;
        }

        label input[type="checkbox"] {
            width: auto;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <label for="email">E-mail</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autofocus
                inputmode="email"
                autocomplete="email"
            >
            @error('email')
                <span id="email-error" class="error" role="alert">
                    {{ $message }}
                </span>
            @enderror

            <label for="password">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                required
                autocomplete="current-password"
            >
            @error('password')
                <span id="password-error" class="error" role="alert">
                    {{ $message }}
                </span>
            @enderror

            <label>
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                Remember me
            </label>

            <button type="submit">Login</button>
            <a class="button-outline" href="{{ route('register') }}">Register</a>

            @if (session('status'))
                <p class="success" role="status">{{ session('status') }}</p>
            @endif
        </form>
    </div>
</body>
</html>
