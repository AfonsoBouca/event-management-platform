

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
</head>
<body>

<h1>Edit User Information</h1>

<form method="POST" action="{{ route('user.update') }}">
    @csrf

    <label>Email</label>
    <input type="email" name="email" value="{{ $user->email }}" required>

    <br><br>

    <label>Password (leave blank to keep current)</label>
    <input type="password" name="password">

    <br><br>

    <button type="submit">Save</button>
</form>

<br>
<a href="{{ route('user.show') }}">Back</a>

</body>
</html>
