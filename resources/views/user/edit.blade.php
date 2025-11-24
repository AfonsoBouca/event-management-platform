<style>
    /* --- Container --- */
    body {
        font-family: Arial, sans-serif;
        background-color: #faf4ff;
        margin: 0;
        padding: 20px;
        display: flex;
        justify-content: center;
    }

    .edit-container {
        max-width: 600px;
        width: 100%;
        background: linear-gradient(135deg, #fff, #f0d4ff);
        padding: 30px;
        border-radius: 12px;
        border: 1px solid #444;
        box-shadow: 0 6px 18px rgba(0,0,0,0.3);
    }

    h1 {
        color: #6a0dad;
        text-align: center;
        margin-bottom: 25px;
        font-size: 2rem;
    }

    h2 {
        color: #6a0dad;
        border-bottom: 2px solid #6a0dad;
        padding-bottom: 5px;
        margin-bottom: 20px;
    }

    /* --- Formulário --- */
    form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    label {
        font-weight: bold;
        color: #000;
        font-size: 1.1em;
    }

    input[type="email"],
    input[type="password"],
    input[type="file"] {
        width: 100%;
        padding: 10px 12px;
        border-radius: 6px;
        border: 1px solid #444;
        background: #fff;
        font-size: 1em;
        color: #000;
        transition: box-shadow 0.2s ease;
    }

    input:focus {
        border-color: #6a0dad;
        box-shadow: 0 0 6px rgba(106, 13, 173, 0.6);
    }

    /* --- Botão Submit --- */
    button[type="submit"] {
        background: linear-gradient(45deg, #6a0dad, #7d1dcf);
        color: #fff;
        padding: 12px 20px;
        border-radius: 6px;
        border: none;
        font-size: 1.1em;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 10px;
    }

    button[type="submit"]:hover {
        background: linear-gradient(45deg, #7d1dcf, #9a33e0);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    }

    /* --- Link Voltar --- */
    .back-link {
        display: inline-block;
        margin-top: 20px;
        color: #6a0dad;
        font-weight: bold;
        text-decoration: none;
        transition: color 0.3s ease;
        text-align: center;
        width: 100%;
    }

    .back-link:hover {
        color: #9a33e0;
    }

    /* --- Responsivo --- */
    @media (max-width: 600px) {
        .edit-container {
            padding: 20px;
        }
    }

    .modal-background {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    justify-content: center;
    align-items: center;
}

.modal-box {
    background: white;
    padding: 25px;
    border-radius: 10px;
    width: 350px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.modal-box h2 {
    margin-top: 0;
    color: red;
}

.modal-box p {
    color: #333;
}

</style>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
</head>

<body>

<div class="edit-container">

    <h1>Edit User Information</h1>

    <form method="POST" action="{{ route('user.update') }}" enctype="multipart/form-data">
        @csrf
        
        <h2>Edit Profile</h2>

        <!-- Email -->
        <label>Email</label>
        <input type="email" name="email" value="{{ $user->email }}" required>

        <!-- Password -->
        <label>New Password <small>(leave blank to keep current, mininum length = 6)</small></label>
        <input type="password" name="password">

        <!-- Profile Image -->
        <label>Profile Image</label>
        <input type="file" name="profileimage" accept="image/*">

        <button type="submit">Save changes</button>
    </form>

    <button type="button" id="openDeleteModal" style="background-color:red; color:white; padding:10px 15px; border:none; border-radius:6px; margin-top:20px; cursor:pointer;">
        Delete Account
    </button>



    <a class="back-link" href="{{ route('user.profile', $user->username) }}">Back to Profile</a>

</div>


<div id="deleteModal" class="modal-background">
    <div class="modal-box">
        <h2>Confirm Account Deletion</h2>
        <p>Are you sure you want to delete your account? This action cannot be undone.</p>

        <div style="margin-top:20px; display:flex; gap:10px; justify-content:flex-end;">
            <form action="{{ route('user.delete') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" style="background-color:red; color:white; border:none; padding:10px 15px; border-radius:6px; cursor:pointer;">
                    Yes, delete my account
                </button>
            </form>

            <button id="closeDeleteModal" style="background-color:#ccc; border:none; padding:10px 15px; border-radius:6px; cursor:pointer;">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById("deleteModal");
    const openBtn = document.getElementById("openDeleteModal");
    const closeBtn = document.getElementById("closeDeleteModal");

    openBtn.onclick = () => modal.style.display = "flex";
    closeBtn.onclick = () => modal.style.display = "none";

    // Fechar se clicar fora da caixa
    window.onclick = (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    };
</script>

</body>
</html>
