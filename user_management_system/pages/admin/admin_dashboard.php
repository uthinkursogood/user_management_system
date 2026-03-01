<?php

// Check if admin
if ($_SESSION["role"] !== 'admin') {
    header("Location: user_dashboard.php");
    exit;
}

include "../../includes/auth.php";
include "../../includes/db.php";
include "../../includes/functions.php";

// Search users by name or email
$search = trim($_GET['search'] ?? '');
$message = $_GET['message'] ?? '';

if (!empty($search)) {
    $users = searchUsers($conn, $search);
    $page_title = "Search Results";
} else {
    $users = getAllUsers($conn, 100);
    $page_title = "All Users";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h1><i class="bi bi-shield-check"></i> Admin</h1>
        <ul class="nav">
            <li><a href="admin_dashboard.php" class="nav-link active"><i class="bi bi-people me-2"></i>Users</a></li>
            <li><a href="../user/user_dashboard.php" class="nav-link"><i class="bi bi-person-circle me-2"></i>My Profile</a></li>
            <li><a href="../../logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="navbar">
            <div class="container-fluid px-4 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>User Management</h2>
                    <div class="d-flex align-items-center gap-3">
                        <span><i class="bi bi-person-circle"></i> <?= safeOutput($_SESSION["user_name"]) ?></span>
                        <a href="../../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <?= safeOutput($message) ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <h4 class="mb-4"><?= safeOutput($page_title) ?></h4>

            <!-- Search Box -->
            <div class="search-box">
                <form class="d-flex gap-2 flex-grow-1" method="GET" action="">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control" 
                        placeholder="Search by name or email..."
                        value="<?= safeOutput($search) ?>"
                    >
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="admin_dashboard.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!--View all users (READ operation)-->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Use LOOP to display records dynamically
                        if (empty($users)) {
                            echo '<tr><td colspan="8" class="text-center text-muted">No users found</td></tr>';
                        } else {
                            foreach ($users as $user) {
                                $role_badge = $user['role'] === 'admin' ? 'danger' : 'primary';
                                $status_badge = $user['status'] === 'active' ? 'success' : 'secondary';
                                ?>
                                <tr>
                                    <td><?= safeOutput($user['id']) ?></td>
                                    <td><?= safeOutput($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                    <td><?= safeOutput($user['email']) ?></td>
                                    <td><?= safeOutput($user['gender']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $role_badge ?>">
                                            <?= safeOutput($user['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $status_badge ?>">
                                            <?= safeOutput($user['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= safeOutput(date('M d, Y', strtotime($user['created_at']))) ?></td>
                                    <td>
                                        <a href="user_detail.php?id=<?= safeOutput($user['id']) ?>" class="btn btn-sm btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit_user.php?id=<?= safeOutput($user['id']) ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete_user.php?id=<?= safeOutput($user['id']) ?>"
                                           class="btn btn-sm btn-danger" 
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this user?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        // END OF FOREACH LOOP
                        ?>
                    </tbody>
                </table>
            </div>

            <small class="text-muted">
                Total users: <?= count($users) ?>
            </small>
        </div>
    </div>
</body>
</html>
