<?php
include "../../includes/auth.php";

include "../../includes/db.php";
include "../../includes/functions.php";

// Check if admin
if ($_SESSION["role"] !== 'admin') {
    header("Location: ../user/user_dashboard.php");
    exit;
}

// Get user ID from URL
$user_id = $_GET['id'] ?? 0;
$user = getUserById($conn, $user_id);

if (!$user) {
    header("Location: admin_dashboard.php?error=User not found");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details - User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">

</head>
<body>
    <div class="details-container">
        <div class="details-card">
            <div class="details-header">
                <div class="user-avatar">
                    <i class="bi bi-person"></i>
                </div>
                <h2><?= safeOutput($user['first_name'] . ' ' . $user['last_name']) ?></h2>
                <p class="text-muted"><?= safeOutput($user['email']) ?></p>
            </div>

            <!-- View user details -->
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">First Name</div>
                    <div class="detail-value"><?= safeOutput($user['first_name']) ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Last Name</div>
                    <div class="detail-value"><?= safeOutput($user['last_name']) ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value"><?= safeOutput($user['email']) ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Gender</div>
                    <div class="detail-value"><?= safeOutput($user['gender']) ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Role</div>
                    <div class="detail-value">
                        <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                            <?= safeOutput(ucfirst($user['role'])) ?>
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>">
                            <?= safeOutput(ucfirst($user['status'])) ?>
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Member Since</div>
                    <div class="detail-value"><?= safeOutput(date('M d, Y', strtotime($user['created_at']))) ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Login Attempts</div>
                    <div class="detail-value"><?= safeOutput($user['login_attempts']) ?>/3</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="edit_user.php?id=<?= safeOutput($user['id']) ?>" class="btn btn-custom btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="delete_user.php?id=<?= safeOutput($user['id']) ?>"
                   class="btn btn-custom btn-danger"
                   onclick="return confirm('Are you sure you want to delete this user?');">
                    <i class="bi bi-trash me-2"></i>Delete
                </a>
                <a href="admin_dashboard.php" class="btn btn-custom btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>
