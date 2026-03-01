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
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">

</head>
<body>
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="bi bi-person"></i>
                </div>
                <h2><?= safeOutput($user['first_name'] . ' ' . $user['last_name']) ?></h2>
                <p><?= safeOutput($user['email']) ?></p>
                <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                    <?= safeOutput(ucfirst($user['role'])) ?>
                </span>
            </div>

            <!-- View their own profile -->
            <div class="profile-info">
                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value"><?= safeOutput($user['email']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Gender</div>
                    <div class="info-value"><?= safeOutput($user['gender']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Account Status</div>
                    <div class="info-value">
                        <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>">
                            <?= safeOutput(ucfirst($user['status'])) ?>
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Member Since</div>
                    <div class="info-value"><?= safeOutput(date('M d, Y', strtotime($user['created_at']))) ?></div>
                </div>
            </div>


            <div class="action-buttons">
                <a href="user_edit.php" class="btn btn-custom btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit Profile
                </a>
                <!-- Change password -->
                <a href="user_change_password.php" class="btn btn-custom btn-warning">
                    <i class="bi bi-key me-2"></i>Change Password
                </a>
                <!--  Delete their account -->
                <a href="user_delete_account.php" class="btn btn-custom btn-danger">
                    <i class="bi bi-trash me-2"></i>Delete Account
                </a>
                <a href="../../logout.php" class="btn btn-custom btn-secondary">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="profile-card">
            <h5>Account Information</h5>
            <p class="text-muted">
                <?php
                if ($user['role'] === 'admin') {
                    echo "You have admin access. You can manage all users from the admin dashboard.";
                } else {
                    echo "You have regular user access. You can only modify your own account information.";
                }
                ?>
            </p>
        </div>
    </div>
</body>
</html>
