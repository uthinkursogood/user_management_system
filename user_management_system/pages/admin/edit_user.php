<?php
include "auth.php";

// check if admin
if ($_SESSION["role"] !== 'admin') {
    header("Location: user_dashboard.php");
    exit;
}

include "db.php";
include "functions.php";

// get user ID from URL
$user_id = $_GET['id'] ?? 0;
$user = getUserById($conn, $user_id);

if (!$user) {
    header("Location: admin_dashboard.php?error=User not found");
    exit;
}

$errors = array();
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"] ?? '');
    $last_name = trim($_POST["last_name"] ?? '');
    $gender = $_POST["gender"] ?? '';
    $role = $_POST["role"] ?? '';
    $status = $_POST["status"] ?? '';

    // Validation
    if (empty($first_name)) {
        $errors[] = "First name is required";
    }
    if (empty($last_name)) {
        $errors[] = "Last name is required";
    }
    if (empty($gender)) {
        $errors[] = "Gender is required";
    }
    if (empty($role) || !in_array($role, ['admin', 'user'])) {
        $errors[] = "Invalid role selected";
    }
    if (empty($status) || !in_array($status, ['active', 'inactive'])) {
        $errors[] = "Invalid status selected";
    }

    // update if no errors
    if (empty($errors)) {
        // update user info
        $result = updateUser($conn, $user_id, [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'gender' => $gender
        ]);

        if ($result['success']) {
            // update role and status
            $stmt = $conn->prepare("UPDATE users SET role = ?, status = ? WHERE id = ?");
            $stmt->bind_param("ssi", $role, $status, $user_id);
            $stmt->execute();
            $stmt->close();

            $success = true;
            // refresh user data
            $user = getUserById($conn, $user_id);
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
    <title>Edit User - User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">

</head>
<body>
    <div class="form-container">
        <div class="form-card">
            <h2><i class="bi bi-pencil-square"></i> Edit User</h2>

            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    User updated successfully!
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <h5>Please fix the following errors:</h5>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= safeOutput($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- View their own profile -->
                <h5 style="color: #667eea; margin-bottom: 20px;">Personal Information</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input 
                            type="text" 
                            class="form-control form-control-lg" 
                            id="first_name"
                            name="first_name" 
                            value="<?= safeOutput($user['first_name']) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input 
                            type="text" 
                            class="form-control form-control-lg" 
                            id="last_name"
                            name="last_name" 
                            value="<?= safeOutput($user['last_name']) ?>"
                            required
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email (Cannot be changed)</label>
                    <input 
                        type="email" 
                        class="form-control form-control-lg" 
                        id="email"
                        value="<?= safeOutput($user['email']) ?>"
                        disabled
                    >
                </div>

                <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select 
                        class="form-select form-select-lg" 
                        id="gender"
                        name="gender"
                        required
                    >
                        <option value="">Select Gender</option>
                        <option value="Male" <?= $user['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $user['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                        <option value="Other" <?= $user['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>

                <!-- Account Management Section -->
                <div class="section-divider">
                    <h5>Account Settings</h5>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select 
                            class="form-select form-select-lg" 
                            id="role"
                            name="role"
                            required
                        >
                            <option value="">Select Role</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Regular User</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select 
                            class="form-select form-select-lg" 
                            id="status"
                            name="status"
                            required
                        >
                            <option value="">Select Status</option>
                            <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary btn-update">
                        <i class="bi bi-check-circle me-2"></i>Update User
                    </button>
                    <a href="admin_dashboard.php" class="btn btn-outline-secondary" style="padding: 12px;">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
