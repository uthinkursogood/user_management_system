<?php
session_start();

// If user is already logged in, redirect them
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

// form variables
$first_name = $last_name = $email = $gender = '';
$errors = array();
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize and Collect inputs
    $first_name = trim($_POST["first_name"] ?? '');
    $last_name = trim($_POST["last_name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirm_password = $_POST["confirm_password"] ?? '';
    $gender = $_POST["gender"] ?? '';
    $role = $_POST["role"] ?? '';
    $address = $_POST["address"] ?? '';

    // Validate all inputs - required fields muts not be empty
    $errors = validateRegistrationForm([
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'password' => $password,
        'confirm_password' => $confirm_password,
        'gender' => $gender,
        'role' => $role,
        'address' => $address
    ]);

    // If no validation errors, proceed with registration
    if (empty($errors)) {
        // Check if email already exists
        if (getUserByEmail($conn, $email)) {
            $errors['email'] = 'Email already registered. Please use a different email.';
        } else {
            // Register the user
            $result = registerUser($conn, [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'password' => $password,
                'gender' => $gender,
                'role' => $role,
                'address' => $address
            ]);

            if ($result['success']) {
                $success_message = 'Registration successful! Redirecting to login...';
                // Redirect after 2 seconds
                header("refresh:2;url=login.php");
            } else {
                $errors['general'] = $result['message'];
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <h1>Create Account</h1>
            <p class="subtitle">Create an account</p>

            <!-- Display errors using FOREACH -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <h5 class="alert-heading">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        Registration Error
                    </h5>
                    <ul class="mb-0">
                        <?php foreach ($errors as $field => $error_message): ?>
                            <li><?= safeOutput($error_message) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <?= safeOutput($success_message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input 
                            type="text" 
                            class="form-control form-control-lg <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" 
                            id="first_name"
                            name="first_name" 
                            placeholder="Juan"
                            value="<?= safeOutput($first_name) ?>"
                            required
                        >
                        <?php if (isset($errors['first_name'])): ?>
                            <div class="invalid-feedback">
                                <?= safeOutput($errors['first_name']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input 
                            type="text" 
                            class="form-control form-control-lg <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" 
                            id="last_name"
                            name="last_name" 
                            placeholder="Dela Cruz"
                            value="<?= safeOutput($last_name) ?>"
                            required
                        >
                        <?php if (isset($errors['last_name'])): ?>
                            <div class="invalid-feedback">
                                <?= safeOutput($errors['last_name']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input 
                        type="email" 
                        class="form-control form-control-lg <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                        id="email"
                        name="email" 
                        placeholder="juan@example.com"
                        value="<?= safeOutput($email) ?>"
                        required
                        autocomplete="email"
                    >
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback">
                            <?= safeOutput($errors['email']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select 
                        class="form-select form-select-lg <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" 
                        id="gender"
                        name="gender"
                        required
                    >
                        <option value="">Select Gender</option>
                        <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
                        <option value="Other" <?= $gender === 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                    <?php if (isset($errors['gender'])): ?>
                        <div class="invalid-feedback">
                            <?= safeOutput($errors['gender']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select
                            class="form-select form-select-lg <?= isset($errors['role']) ? 'is-invalid' : '' ?>"
                            id="role"
                            name="role"
                            required
                    >
                        <option value="">Select Role</option>
                        <option value="Male" <?= $gender === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="Female" <?= $gender === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                    <?php if (isset($errors['role'])): ?>
                        <div class="invalid-feedback">
                            <?= safeOutput($errors['role']) ?>
                        </div>

                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        class="form-control form-control-lg <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                        id="password"
                        name="password" 
                        placeholder="Enter password (min. 8 characters)"
                        required
                        autocomplete="new-password"
                    >
                    <small class="form-text">Minimum 8 characters required</small>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback d-block">
                            <?= safeOutput($errors['password']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input 
                        type="password" 
                        class="form-control form-control-lg <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                        id="confirm_password"
                        name="confirm_password" 
                        placeholder="Re-enter password"
                        required
                        autocomplete="new-password"
                    >
                    <?php if (isset($errors['confirm_password'])): ?>
                        <div class="invalid-feedback d-block">
                            <?= safeOutput($errors['confirm_password']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary btn-register w-100">Create Account</button>
            </form>

            <div class="login-link">
                Already have an account? <a href="login.php">Sign in</a>
            </div>
        </div>
    </div>
</body>
</html>
