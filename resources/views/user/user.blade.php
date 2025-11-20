<form method='GET' action="/user">
    @csrf 



</form>    

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Utilizador</title>
    <!-- <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .profile-container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            padding: 24px 32px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }

        .profile-header img {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }

        .profile-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .profile-header span {
            color: #666;
            font-size: 14px;
        }

        .profile-info {
            margin-top: 10px;
        }

        .profile-info-row {
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
    </style> -->
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <!-- Imagem de perfil (podes trocar o src por outra imagem qualquer) -->
            <img src="{{$user->profileImage ?? '/images/default-profile.png'}}" alt="Foto de perfil">
            <div>
            
                <span>User Profile</span>
            </div>
        </div>

        <div class="profile-info">
            <div class="profile-info-row">
                <span class="label">Username:</span>
                <span>{{ $user->username }}</span>
            </div>

            <div class="profile-info-row">
                <span class="label">Email:</span>
                <span>{{ $user->email }}</span>
            </div>

            <div class="profile-info-row">
                <span class="label">Password:</span>
               
                <span>************</span>
            </div>
        </div>
    </div>
</body>
</html>
