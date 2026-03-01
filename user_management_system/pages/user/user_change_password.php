<?php
include "../../includes/auth.php";
include "../../includes/db.php";
include "../../includes/functions.php";
// Get current user data
$user = getUserById($conn, $_SESSION["user_id"]);

if (!$user) {
    header("Location: logout.php");
    exit;
}

$errors = array();
$success = false;

//  Change password
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST["current_password"] ?? '';
    $new_password = $_POST["new_password"] ?? '';
    $confirm_password = $_POST["confirm_password"] ?? '';

    if (empty($current_password)) {
        $errors['current_password'] = "Current password is required";
    } elseif (!verifyPassword($current_password, $user['password'])) {
        $errors['current_password'] = "Current password is incorrect";
    }

    if (empty($new_password)) {
        $errors['new_password'] = "New password is required";
    } elseif (!validatePassword($new_password)) {
        $errors['new_password'] = "Password must be at least 8 characters";
    }

    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Please confirm your new password";
    } elseif ($new_password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    // Update if no errors
    if (empty($errors)) {
        $result = changePassword($conn, $_SESSION["user_id"], $new_password);

        if ($result['success']) {
            $success = true;
            // clear form
            $current_password = $new_password = $confirm_password = '';
        } else {
            $errors['general'] = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">

</head>
<body>
    <div class="form-container">
        <div class="form-card">
            <h2><i class="bi bi-key-fill"></i> Change Password</h2>

            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    Password changed successfully!
                </div>
            <?php endif; ?>

            <!-- REQUIRED USE OF LOOPS AND ARRAYS) -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <h5>Please fix the following errors:</h5>
                    <ul class="mb-0">
                        <?php
                        foreach ($errors as $field => $error_message):
                            ?>
                            <li><?= safeOutput($error_message) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control form-control-lg" 
                            id="current_password"
                            name="current_password"
                            placeholder="Enter current password"
                            required
                        >
                        <button type="button" class="input-group-text" onclick="togglePassword('current_password')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control form-control-lg" 
                            id="new_password"
                            name="new_password"
                            placeholder="Enter new password (min. 8 characters)"
                            required
                        >
                        <button type="button" class="input-group-text" onclick="togglePassword('new_password')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small class="form-text">Minimum 8 characters required</small>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control form-control-lg" 
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="Re-enter new password"
                            required
                        >
                        <button type="button" class="input-group-text" onclick="togglePassword('confirm_password')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary btn-update">
                        <i class="bi bi-check-circle me-2"></i>Change Password
                    </button>
                    <a href="user_dashboard.php" class="btn btn-outline-secondary" style="padding: 12px;">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = event.target.closest('button');
            const icon = button.querySelector('i');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
