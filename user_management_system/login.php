<?php
session_start();

// If user already logged in, redirect to its dashboard
if (isset($_SESSION["user_id"])) {
    if ($_SESSION["role"] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit;
}

include "includes/db.php";
include "includes/functions.php";

$email = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    // Validation
    if (empty($email) || empty($password)) {
        $error = "Email and password are required";
    } else {
        // session authentication
        $auth_result = authenticateUser($conn, $email, $password);

        if ($auth_result['success']) {
            $user = $auth_result['user'];

            $_SESSION["user_id"] = $user['id'];
            $_SESSION["user_name"] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION["role"] = $user['role'];
            $_SESSION["email"] = $user['email'];

            //  Redirect users based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            $error = $auth_result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h1>User Management System</h1>
            <p class="subtitle">Sign in to your account</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?= safeOutput($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input 
                        type="email" 
                        class="form-control form-control-lg" 
                        id="email"
                        name="email" 
                        placeholder="Enter your email"
                        value="<?= safeOutput($email) ?>"
                        required
                        autocomplete="email"
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control form-control-lg" 
                            id="password"
                            name="password" 
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >
                        <button 
                            type="button" 
                            class="input-group-text" 
                            id="togglePassword"
                            title="Toggle password visibility"
                        >
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-login w-100">Sign In</button>
            </form>

            <div class="register-link">
                Don't have an account? <a href="register.php">Create one</a>
            </div>
        </div>
    </div>

    <script>
        // pang hide or show ng password na tinatype
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordField.type = 'password';

                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        });
    </script>
</body>
</html>
