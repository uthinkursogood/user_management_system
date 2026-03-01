<?php
include "../../includes/auth.php";
include "../../includes/db.php";
include "../../includes/functions.php";
// get user data
$user = getUserById($conn, $_SESSION["user_id"]);

if (!$user) {
    header("Location: logout.php");
    exit;
}

$errors = array();
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"] ?? '');
    $last_name = trim($_POST["last_name"] ?? '');
    $gender = $_POST["gender"] ?? '';

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

    // Update if no errors
    if (empty($errors)) {
        $result = updateUser($conn, $_SESSION["user_id"], [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'gender' => $gender
        ]);

        if ($result['success']) {
            // Update session
            $_SESSION["user_name"] = $first_name . ' ' . $last_name;
            $success = true;
            // Refresh user data
            $user = getUserById($conn, $_SESSION["user_id"]);
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
    <title>Edit Profile - User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            padding: 40px;
        }
        .form-card h2 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        .btn-update {
            flex: 1;
            padding: 12px;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-update:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-card">
            <h2><i class="bi bi-pencil-square"></i> Edit Profile</h2>

            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    Profile updated successfully!
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
                <div class="mb-3">
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

                <div class="mb-3">
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

                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary btn-update">
                        <i class="bi bi-check-circle me-2"></i>Update Profile
                    </button>
                    <a href="user_dashboard.php" class="btn btn-outline-secondary" style="padding: 12px;">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
