<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hash')";

    if ($conn->query($sql) === TRUE) {
        // $_SESSION['loggedin'] = true;
        // $_SESSION['username'] = $username;
        // $_SESSION['user_id'] = $row['id'];
        header("location: signin.php");

    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Voltway</title>
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

    .signup-container {
        width: 100%;
        max-width: 400px;
        padding: 15px;
        margin: auto;
        background-color: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .form-signup {
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
    <div class="signup-container">
        <div class="text-center">
            <img class="logo" src="img/fav-icon.png" alt="Voltway Logo">
        </div>
        <h2 class="text-center mb-4">Inscription</h2>
        <form class="form-signup" action="" method="post">
            <div class="form-group mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" class="form-control" required autofocus>
            </div>
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label for="confirm-password" class="form-label">Confirmer le mot de passe</label>
                <input type="password" id="confirm-password" name="confirm-password" class="form-control" required>
            </div>
            <button class="btn btn-primary btn-block" type="submit">S'inscrire</button>
        </form>
        <div class="text-center mt-3">
            <a href="signin.php">Déjà inscrit ? Se connecter</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>