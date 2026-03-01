<?php
session_start();

// If logged in, redirect if admin or user
if (isset($_SESSION["user_id"])) {
    if ($_SESSION["role"] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit;
}

// Redirect to login page
header("Location: login.php");
exit;
?>
