<?php
session_start();
include '../db.php'; // Ensure you have a file to connect to your database

// Function to check if the admin table is empty and insert default credentials
function initializeAdmin()
{
    global $conn; // Use the database connection from the included db.php

    // Check if the table is empty
    $sql = "SELECT COUNT(*) AS cnt FROM admin";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($row['cnt'] == 0) { // If no admin exists
        // Insert default admin credentials
        $defaultEmail = "admin@voltway.dz";
        $defaultPassword = password_hash("admin123", PASSWORD_DEFAULT); // Hash the password for security

        $insertSql = "INSERT INTO admin (email, password) VALUES ('$defaultEmail', '$defaultPassword')";
        $conn->query($insertSql);
    }
}

// Call the initialization function
initializeAdmin();

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT email, password FROM admin WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id']; // Set session variable
            $_SESSION['admin_email'] = $row['email']; // Set session variable
            $_SESSION['admin_logged_in'] = true; // Set session variable
            header("Location: index.php"); // Redirect to an admin dashboard page
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that email address.";
    }
}

$conn->close();
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
            <img class="logo" src="../img/fav-icon.png" alt="Voltway Logo">
        </div>
        <h2 class="text-center mb-4">Admin login</h2>
        <?php if (!empty($error)) : ?>
        <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form class="form-signin" method="POST" action="">
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required autofocus><br>
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required><br>
                <button class="btn btn-primary btn-block" type="submit">Login</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>