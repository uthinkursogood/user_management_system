<?php
//===========================
//  SECURITY REQUIREMENTS
//=========================


//  Use session authentication
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
$admin_pages = ['admin_dashboard.php', 'user_detail.php', 'edit_user.php', 'delete_user.php'];

if (in_array($current_page, $admin_pages) && $_SESSION["role"] !== 'admin') {
    header("Location: ../pages/user/user_dashboard.php");
    exit;
}
?>