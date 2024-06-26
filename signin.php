<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, password FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['id'];
            header("location: index.php");
        } else {
            echo "Invalid password";
        }
    } else {
        echo "No user found";
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Voltway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f5f5f5;
    }

    .signin-container {
        width: 100%;
        max-width: 400px;
        padding: 15px;
        margin: auto;
        background-color: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .form-signin {
        width: 100%;
    }

    .logo {
        width: 80px;
        height: 80px;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <div class="signin-container">
        <div class="text-center">
            <img class="logo" src="img/fav-icon.png" alt="Voltway Logo">
        </div>
        <h2 class="text-center mb-4">Connexion</h2>
        <form class="form-signin" action="" method="post">
            <div class="form-group mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" class="form-control" required autofocus>
            </div>
            <div class="form-group mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Se connecter</button>
        </form>
        <div class="text-center mt-3">
            <a href="signup.php">Créer un compte</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>