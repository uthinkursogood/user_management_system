<?php
//============================================
// PART 7: REQUIRED FUNCTIONS
//============================================

function validateEmail($email) {
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Check if password strong
 * @param string $password - Password to validate
 * @return boolean - True if password >= 8 characters
 */
function validatePassword($password) {
    return strlen($password) >= 8;
}

/**
 * @param string $password
 * @param string $confirm
 * @return boolean
 */

function passwordsMatch($password, $confirm) {
    return $password === $confirm;
}

/**
 * Validate required field
 * @param string $value - Value to check
 * @return boolean - True if not empty
 */
function isNotEmpty($value) {
    return !empty(trim($value));
}

/**
 * Validate name (letters, spaces, hyphens only)
 * @param string $name - Name to validate
 * @return boolean - True if valid
 */
function validateName($name) {
    return preg_match("/^[a-zA-Z\s'-]{2,100}$/", trim($name));
}

// ============================================
// PART 9: SECURITY REQUIREMENTS
// ============================================

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}


function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function safeEcho($data) {
    echo htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function safeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// ============================================
// DATABASE QUERY FUNCTIONS
// ============================================

function getUserByEmail($conn, $email) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result;
}

// Get user by ID

function getUserById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result;
}

function getAllUsers($conn, $limit = 100, $offset = 0) {
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, gender, role, status, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
    if (!$stmt) {
        return array();
    }
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $result;
}

// Get user total count
function getTotalUsers($conn) {
    $result = $conn->query("SELECT COUNT(*) as total FROM users");
    if (!$result) {
        return 0;
    }
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Search user by name or email

function searchUsers($conn, $search) {
    $search_term = "%" . trim($search) . "%";
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, gender, role, status, created_at FROM users WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? ORDER BY created_at DESC");
    if (!$stmt) {
        return array();
    }
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $result;
}

// ============================================
// USER MANAGEMENT FUNCTIONS
// ============================================


// Register a new user
function registerUser($conn, $userData) {
    $first_name = trim($userData['first_name']);
    $last_name = trim($userData['last_name']);
    $email = trim($userData['email']);
    $password = $userData['password'];
    $gender = $userData['gender'];

    $hashed_password = hashPassword($password);

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, gender, role, status) VALUES (?, ?, ?, ?, ?, 'user', 'active')");
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error'];
    }

    $stmt->bind_param("sssss", $first_name, $last_name, $email, $hashed_password, $gender);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Registration successful'];
    } else {
        if ($conn->errno == 1062) {
            $stmt->close();
            return ['success' => false, 'message' => 'Email already registered'];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
}

//  Edit user information (UPDATE operation)
function updateUser($conn, $id, $userData) {
    $first_name = trim($userData['first_name']);
    $last_name = trim($userData['last_name']);
    $gender = $userData['gender'];

    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, gender = ? WHERE id = ?");
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error'];
    }

    $stmt->bind_param("sssi", $first_name, $last_name, $gender, $id);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'User updated successfully'];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Update failed'];
    }
}

//  Change user password
function changePassword($conn, $id, $newPassword) {
    if (!validatePassword($newPassword)) {
        return ['success' => false, 'message' => 'Password must be at least 8 characters'];
    }

    $hashed_password = hashPassword($newPassword);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error'];
    }

    $stmt->bind_param("si", $hashed_password, $id);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Password changed successfully'];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Password change failed'];
    }
}

//  Delete user (DELETE operation)
function deleteUser($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error'];
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'User deleted successfully'];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Deletion failed'];
    }
}

//  Activate / Deactivate user
function updateUserStatus($conn, $id, $status) {
    if (!in_array($status, ['active', 'inactive'])) {
        return ['success' => false, 'message' => 'Invalid status'];
    }

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error'];
    }

    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Status updated successfully'];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Status update failed'];
    }
}

// ============================================
// LOGIN & AUTHENTICATION FUNCTIONS
// ============================================

function authenticateUser($conn, $email, $password) {
    $user = getUserByEmail($conn, $email);

    if (!$user) {
        return ['success' => false, 'message' => 'Email not found'];
    }

    if (!verifyPassword($password, $user['password'])) {
        // Increment login attempts
        incrementLoginAttempts($conn, $user['id']);
        return ['success' => false, 'message' => 'Invalid password'];
    }

    if ($user['status'] === 'inactive') {
        return ['success' => false, 'message' => 'Your account is inactive. Please contact administrator.'];
    }

    // Reset login attempts on successful login
    resetLoginAttempts($conn, $user['id']);

    return ['success' => true, 'message' => 'Login successful', 'user' => $user];
}

//  Increase login_attempts by 1.
function incrementLoginAttempts($conn, $id) {
    $stmt = $conn->prepare("UPDATE users SET login_attempts = login_attempts + 1 WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Check if attempts reached 3
        $user = getUserById($conn, $id);
        if ($user && $user['login_attempts'] >= 3) {
            updateUserStatus($conn, $id, 'inactive');
        }
    }
}

//   If login successful:
//   Reset login_attempts to 0.
function resetLoginAttempts($conn, $id) {
    $stmt = $conn->prepare("UPDATE users SET login_attempts = 0 WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// ============================================
// ARRAY & LOOP DEMONSTRATION FUNCTIONS
// ============================================

//  Validate form
function validateRegistrationForm($formData) {
    $errors = array();

    // DValidate all user input
    $rules = array(
        'first_name' => 'First name is required',
        'last_name' => 'Last name is required',
        'email' => 'Email is required',
        'password' => 'Password is required',
        'confirm_password' => 'Please confirm your password',
        'gender' => 'Gender is required'
    );

    // Check fro empty fields
    foreach ($rules as $field => $message) {
        if (empty(trim($formData[$field] ?? ''))) {
            $errors[$field] = $message;
        }
    }

    // Email must follow proper format
    if (!empty($formData['email']) && !validateEmail($formData['email'])) {
        $errors['email'] = 'Email format is invalid';
    }

    if (!empty($formData['password']) && !validatePassword($formData['password'])) {
        $errors['password'] = 'Password must be at least 8 characters';
    }

    if (!empty($formData['password']) && !empty($formData['confirm_password'])) {
        if (!passwordsMatch($formData['password'], $formData['confirm_password'])) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
    }

    return $errors;
}

//  Error Handling
function displayErrors($errors) {
    if (!empty($errors)) {
        echo '<div class="alert alert-danger"><ul>';
        // Demonstrate foreach loop
        foreach ($errors as $field => $message) {
            echo '<li>' . safeOutput($message) . '</li>';
        }
        echo '</ul></div>';
    }
}

//   Display session records
function getUserStatistics($conn) {
    $stats = array(
        'total_users' => 0,
        'active_users' => 0,
        'inactive_users' => 0,
        'admin_count' => 0,
        'user_count' => 0
    );

    // Use LOOP to display records dynamically
    $result = $conn->query("SELECT status, role FROM users");
    while ($row = $result->fetch_assoc()) {
        $stats['total_users']++;
        if ($row['status'] === 'active') {
            $stats['active_users']++;
        } else {
            $stats['inactive_users']++;
        }
        if ($row['role'] === 'admin') {
            $stats['admin_count']++;
        } else {
            $stats['user_count']++;
        }
    }

    return $stats;
}

//  View all users (READ operation)
function displayUserTable($users) {
    if (empty($users)) {
        echo '<tr><td colspan="8" class="text-center">No users found</td></tr>';
        return;
    }

    //  Use LOOP to display records dynamically
    foreach ($users as $user) {
        echo '<tr>';
        echo '<td>' . safeOutput($user['id']) . '</td>';
        echo '<td>' . safeOutput($user['first_name'] . ' ' . $user['last_name']) . '</td>';
        echo '<td>' . safeOutput($user['email']) . '</td>';
        echo '<td>' . safeOutput($user['gender']) . '</td>';
        echo '<td><span class="badge bg-' . ($user['role'] === 'admin' ? 'danger' : 'primary') . '">' . safeOutput($user['role']) . '</span></td>';
        echo '<td><span class="badge bg-' . ($user['status'] === 'active' ? 'success' : 'secondary') . '">' . safeOutput($user['status']) . '</span></td>';
        echo '<td>' . safeOutput($user['created_at']) . '</td>';
        echo '<td>';
        echo '<a href="edit.php?id=' . safeOutput($user['id']) . '" class="btn btn-sm btn-primary">Edit</a> ';
        echo '<a href="user_detail.php?id=' . safeOutput($user['id']) . '" class="btn btn-sm btn-info">View</a>';
        echo '</td>';
        echo '</tr>';
    }
}

?>
