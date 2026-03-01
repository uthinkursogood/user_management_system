# REMAINING FILES TO CREATE

## Critical Files (Must Create Before Submission)

---

## 1. auth.php - Session Authentication Guard

```php
<?php
session_start();

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Get current page
$current_page = basename($_SERVER['PHP_SELF']);

// Block unauthorized access to admin pages
$admin_pages = ['admin_dashboard.php', 'user_detail.php'];
if (in_array($current_page, $admin_pages) && $_SESSION["role"] !== 'admin') {
    header("Location: user_dashboard.php");
    exit;
}
?>
```

---

## 2. logout.php - Session Destruction

```php
<?php
session_start();
session_destroy();
header("Location: login.php");
exit;
?>
```

---

## 3. admin_dashboard.php - Admin User Management

**Requirements:**
- View all users in table
- Search users by name/email (using LIKE query)
- Edit button for each user
- Delete button for each user (with confirmation)
- Activate/Deactivate status toggle
- Demonstrate: foreach loop for table display

**Key Features:**
```php
<?php include "auth.php"; ?>
<?php 
// Check if admin
if ($_SESSION["role"] !== 'admin') {
    header("Location: user_dashboard.php");
    exit;
}

include "db.php";
include "functions.php";

// Handle search
$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $users = searchUsers($conn, $search);  // Uses LIKE query
} else {
    $users = getAllUsers($conn, 100);
}

// Display users using foreach loop (demonstrates requirement)
foreach ($users as $user) {
    // Display table row with edit/delete buttons
}
?>
```

---

## 4. user_dashboard.php - User Profile Page

**Requirements:**
- Display user's own profile information
- Show profile details
- Buttons to:
  - Edit profile
  - Change password
  - Delete account
- No access to other users' data

**Key Features:**
```php
<?php include "auth.php"; ?>
<?php
include "db.php";
include "functions.php";

// Get current user data
$user = getUserById($conn, $_SESSION["user_id"]);
if (!$user) {
    header("Location: logout.php");
    exit;
}

// Display user info
echo "Name: " . safeOutput($user['first_name'] . ' ' . $user['last_name']);
echo "Email: " . safeOutput($user['email']);
echo "Gender: " . safeOutput($user['gender']);
echo "Member Since: " . safeOutput($user['created_at']);
?>
```

---

## 5. user_edit.php - Edit Own Profile

**Requirements:**
- User can edit their own first_name, last_name, gender
- User CANNOT edit email (optional: allow but check for duplicates)
- User CANNOT edit role or status
- Form with validation
- Security check (own data only)

**Key Features:**
```php
<?php include "auth.php"; ?>
<?php
include "db.php";
include "functions.php";

// Get user data
$user = getUserById($conn, $_SESSION["user_id"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = updateUser($conn, $_SESSION["user_id"], [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'gender' => $_POST['gender']
    ]);
    
    if ($result['success']) {
        $_SESSION["user_name"] = $_POST['first_name'] . ' ' . $_POST['last_name'];
        header("Location: user_dashboard.php");
    }
}
?>
```

---

## 6. user_change_password.php - Change Password

**Requirements:**
- Current password verification
- New password (8+ chars)
- Confirm new password (must match)
- Validation with error array

**Key Features:**
```php
<?php include "auth.php"; ?>
<?php
include "db.php";
include "functions.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = getUserById($conn, $_SESSION["user_id"]);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Verify current password
    if (!verifyPassword($current_password, $user['password'])) {
        $errors[] = "Current password is incorrect";
    }
    
    // Validate new password
    if (!validatePassword($new_password)) {
        $errors[] = "New password must be 8+ characters";
    }
    
    // Check match
    if ($new_password !== $confirm_password) {
        $errors[] = "New passwords do not match";
    }
    
    if (empty($errors)) {
        $result = changePassword($conn, $_SESSION["user_id"], $new_password);
        if ($result['success']) {
            header("Location: user_dashboard.php?msg=changed");
        }
    }
}
?>
```

---

## 7. user_delete_account.php - Delete Own Account

**Requirements:**
- Ask for password confirmation
- Warn user this is permanent
- Delete user record from database
- Redirect to login page
- Show confirmation dialog

**Key Features:**
```php
<?php include "auth.php"; ?>
<?php
include "db.php";
include "functions.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $user = getUserById($conn, $_SESSION["user_id"]);
    
    // Verify password before deletion
    if (verifyPassword($password, $user['password'])) {
        deleteUser($conn, $_SESSION["user_id"]);
        session_destroy();
        header("Location: login.php?msg=deleted");
    } else {
        $error = "Password incorrect. Account not deleted.";
    }
}
?>
```

---

## 8. user_detail.php - View User Details (Admin Only)

**Requirements:**
- Admin only page
- Show detailed info about specific user
- Show edit and delete buttons
- Security check (verify admin access)

**Key Features:**
```php
<?php include "auth.php"; ?>
<?php
// Check admin
if ($_SESSION["role"] !== 'admin') {
    header("Location: user_dashboard.php");
    exit;
}

include "db.php";
include "functions.php";

$user_id = $_GET['id'] ?? 0;
$user = getUserById($conn, $user_id);

if (!$user) {
    header("Location: admin_dashboard.php");
    exit;
}

// Display user details
?>
```

---

## 9. edit_user.php - Edit User (Admin Only)

**Requirements:**
- Admin only
- Can edit any user's data
- Can change status (active/inactive)
- Can change role (admin/user)
- Proper validation

**Key Features:**
```php
<?php include "auth.php"; ?>
<?php
if ($_SESSION["role"] !== 'admin') {
    header("Location: user_dashboard.php");
    exit;
}

include "db.php";
include "functions.php";

$user_id = $_GET['id'] ?? 0;
$user = getUserById($conn, $user_id);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = updateUser($conn, $user_id, [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'gender' => $_POST['gender']
    ]);
    
    // Also update status if admin changed it
    if (isset($_POST['status'])) {
        updateUserStatus($conn, $user_id, $_POST['status']);
    }
}
?>
```

---

## 10. delete_user.php - Delete User (Admin Only)

```php
<?php
include "auth.php";

if ($_SESSION["role"] !== 'admin') {
    header("Location: user_dashboard.php");
    exit;
}

include "db.php";
include "functions.php";

if (isset($_GET["id"])) {
    $user_id = $_GET["id"];
    
    // Don't allow deleting self
    if ($user_id == $_SESSION["user_id"]) {
        header("Location: admin_dashboard.php?error=Cannot delete yourself");
        exit;
    }
    
    $result = deleteUser($conn, $user_id);
}

header("Location: admin_dashboard.php");
exit;
?>
```

---

## 11. index.php - Landing/Home Page

```php
<?php
// If logged in, redirect to dashboard
if (isset($_SESSION["user_id"])) {
    if ($_SESSION["role"] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit;
}

// If not logged in, redirect to login
header("Location: login.php");
exit;
?>
```

---

## File Summary

| File | Type | Status | Priority |
|------|------|--------|----------|
| db.php | ✅ Database | Provided | HIGH |
| functions.php | ✅ Functions | Provided | HIGH |
| auth.php | 📋 Guard | TO CREATE | HIGH |
| login.php | ✅ Login | Provided | HIGH |
| register.php | ✅ Registration | Provided | HIGH |
| logout.php | 📋 Session | TO CREATE | HIGH |
| index.php | 📋 Home | TO CREATE | MEDIUM |
| admin_dashboard.php | 📋 Admin | TO CREATE | HIGH |
| user_dashboard.php | 📋 User Profile | TO CREATE | HIGH |
| user_edit.php | 📋 Edit Profile | TO CREATE | HIGH |
| user_change_password.php | 📋 Security | TO CREATE | MEDIUM |
| user_delete_account.php | 📋 Account | TO CREATE | MEDIUM |
| user_detail.php | 📋 Admin View | TO CREATE | MEDIUM |
| edit_user.php | 📋 Admin Edit | TO CREATE | MEDIUM |
| delete_user.php | 📋 Admin Delete | TO CREATE | MEDIUM |
| user_db.sql | ✅ Database | Provided | HIGH |

---

## Loop & Array Demonstrations by File

### For Loop
- `user_change_password.php` - Validate multiple password fields
- `user_dashboard.php` - Display user statistics using for loop

### While Loop
- `admin_dashboard.php` - Process paginated results
- `user_detail.php` - Display related data in loop

### Foreach Loop
- `admin_dashboard.php` - Display all users in table (MAIN DEMO)
- `register.php` - Display error array (ALREADY PROVIDED)
- `functions.php` - displayUserTable() function
- `functions.php` - displayErrors() function

### Associative Arrays
- `functions.php` - $user array from database
- `functions.php` - $stats array in getUserStatistics()
- `register.php` - $errors array for validation
- All pages - $_SESSION associative array

---

## Security Checklist for New Files

For each new file, ensure:
- [ ] `include "auth.php"` for protected pages
- [ ] Check `$_SESSION["role"]` for admin pages
- [ ] Use `getUserById()` with session user_id
- [ ] Use `safeOutput()` for all echo statements
- [ ] Use `htmlspecialchars()` in form values
- [ ] Use prepared statements (from functions.php)
- [ ] Validate all POST inputs
- [ ] Use functions from functions.php
- [ ] No direct database queries
- [ ] Proper error handling

---

## Testing Each File

1. **auth.php** - Try accessing protected page without login → should redirect
2. **logout.php** - Login, then logout → should destroy session
3. **admin_dashboard.php** - Login as admin → should show all users
4. **user_dashboard.php** - Login as user → should show own profile
5. **user_edit.php** - Edit profile → should update without changing email/role
6. **user_change_password.php** - Change password → should verify current before changing
7. **user_delete_account.php** - Delete account → should require password confirmation
8. **user_detail.php** - As admin, view user → should show details
9. **edit_user.php** - As admin, edit user → should update all fields
10. **delete_user.php** - As admin, delete user → should remove from database

---

## Next Steps

1. ✅ Review all provided files
2. ✅ Import user_db.sql into MySQL
3. ✅ Create auth.php and logout.php (simple files)
4. ✅ Create admin_dashboard.php (complex, has search)
5. ✅ Create user_dashboard.php (show profile)
6. ✅ Create remaining user pages
7. ✅ Test all functionality
8. ✅ Take screenshots
9. ✅ Verify all requirements met
10. ✅ Submit before Feb 28

---

**Files Provided:** 8 (db.php, functions.php, login.php, register.php, user_db.sql, + 3 guides)

**Files To Create:** 10 (basic templates provided above)

**Estimated Time:** 4-6 hours with templates
