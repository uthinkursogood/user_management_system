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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST["password"] ?? '';

    // Validation
    if (empty($password)) {
        $errors[] = "Password is required to delete your account";
    } elseif (!verifyPassword($password, $user['password'])) {
        $errors[] = "Password is incorrect. Account not deleted.";
    }

    //  Delete their account if no error
    if (empty($errors)) {
        $result = deleteUser($conn, $_SESSION["user_id"]);

        if ($result['success']) {
            session_destroy();
            header("Location: login.php?msg=account_deleted");
            exit;
        } else {
            $errors[] = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account - User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">

</head>
<body>
    <div class="form-container">
        <div class="form-card">
            <h2><i class="bi bi-exclamation-triangle"></i> Delete Account</h2>

            <!-- Warning Message -->
            <div class="warning-box">
                <h5><i class="bi bi-exclamation-circle me-2"></i>This action cannot be undone!</h5>
                <p>
                    Deleting your account will permanently remove all your data from our system. 
                    This includes your profile information and cannot be recovered.
                </p>
            </div>

            <!-- Error message -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <strong>Error:</strong> <?= safeOutput($errors[0]) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="deleteForm">
                <div class="mb-3">
                    <label for="password" class="form-label">Confirm Password</label>
                    <p class="text-muted small">
                        Enter your password to confirm account deletion
                    </p>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control form-control-lg" 
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >
                        <button type="button" class="input-group-text" onclick="togglePassword('password')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input 
                        type="checkbox" 
                        class="form-check-input" 
                        id="confirm_delete"
                        name="confirm_delete"
                        required
                    >
                    <label class="form-check-label checkbox-label" for="confirm_delete">
                        I understand that this action is permanent and all my data will be deleted
                    </label>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-delete" onclick="return confirm('Are you absolutely sure? This cannot be undone!');">
                        <i class="bi bi-trash me-2"></i>Delete My Account
                    </button>
                    <a href="pages/user/user_dashboard.php" class="btn btn-outline-secondary" style="padding: 12px;">
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
