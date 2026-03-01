<?php
include "../../includes/auth.php";
// Check if admin
if ($_SESSION["role"] !== 'admin') {
    header("Location: ../user/user_dashboard.php");
    exit;
}

include "../../includes/db.php";
include "../../includes/functions.php";

if (isset($_GET["id"])) {
    $user_id = $_GET["id"];

    // Don't allow deleting self
    if ($user_id == $_SESSION["user_id"]) {
        header("Location: ../../pages/admin/admin_dashboard.php?error=You cannot delete your own account");
        exit;
    }

    // Verify user exists
    $user = getUserById($conn, $user_id);
    if (!$user) {
        header("Location: ../../pages/admin/admin_dashboard.php?error=User not found");
        exit;
    }

    // Delete user
    $result = deleteUser($conn, $user_id);

    if ($result['success']) {
        header("Location: ../../pages/admin/admin_dashboard.php?message=User deleted successfully");
    } else {
        header("Location: ../../pages/admin/admin_dashboard.php?error=Failed to delete user");
    }
    exit;
}

header("Location: ../../pages/admin/admin_dashboard.php");
exit;
?>
