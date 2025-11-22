

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
</head>
<body>

<h1>Edit User Information</h1>

<form method="POST" action="{{ route('user.update') }}" enctype="multipart/form-data">
    @csrf
    
    <h2>Edit Profile</h2>

    <!-- Email -->
    <label>Email</label>
    <input type="email" name="email" value="{{ $user->email }}" required>
    <br><br>

    <!-- Password -->
    <label>New Password <small>(leave blank to keep current)</small></label>
    <input type="password" name="password">
    <br><br>

    <!-- Profile Image -->
    <label>Profile Image</label>
    <input type="file" name="profileimage" accept="image/*">
    <br><br>



    <button type="submit">Save changes</button>
</form>

<br>

<a href="{{ route('user.profile', $user->username) }}">Back to Profile</a>




</body>
</html>
