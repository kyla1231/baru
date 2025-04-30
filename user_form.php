<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Only admin can access this page
if (!isAdmin()) {
    setFlashMessage('danger', 'You do not have permission to access that page.');
    header('Location: dashboard.php');
    exit;
}

// Initialize variables
$user = [
    'id' => '',
    'username' => '',
    'password' => '',
    'role' => 'cashier'
];
$isEdit = false;

// Check if we're editing an existing user
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    $stmt = $db->prepare("SELECT id, username, role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $user = $result;
        $isEdit = true;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $user['username'] = trim($_POST['username']);
    $user['role'] = $_POST['role'];
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirmPassword = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
    
    // Validate form data
    $errors = [];
    
    if (empty($user['username'])) {
        $errors[] = 'Username is required.';
    }
    
    if (!$isEdit && empty($password)) {
        $errors[] = 'Password is required for new users.';
    }
    
    if (!empty($password) && $password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }
    
    // Check if username is already taken (for new users or when changing username)
    if (!$isEdit || ($isEdit && $user['username'] !== $result['username'])) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
        $stmt->execute([$user['username']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            $errors[] = 'Username is already taken.';
        }
    }
    
    if (empty($errors)) {
        try {
            if ($isEdit) {
                if (!empty($password)) {
                    // Update user with new password
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE users SET 
                        username = ?, 
                        password = ?,
                        role = ?,
                        updated_at = CURRENT_TIMESTAMP
                        WHERE id = ?");
                    
                    $stmt->execute([
                        $user['username'],
                        $passwordHash,
                        $user['role'],
                        $user['id']
                    ]);
                } else {
                    // Update user without changing password
                    $stmt = $db->prepare("UPDATE users SET 
                        username = ?, 
                        role = ?,
                        updated_at = CURRENT_TIMESTAMP
                        WHERE id = ?");
                    
                    $stmt->execute([
                        $user['username'],
                        $user['role'],
                        $user['id']
                    ]);
                }
                
                setFlashMessage('success', 'User updated successfully.');
            } else {
                // Create new user
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (
                    username, password, role, created_at, updated_at
                ) VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
                
                $stmt->execute([
                    $user['username'],
                    $passwordHash,
                    $user['role']
                ]);
                
                setFlashMessage('success', 'User created successfully.');
            }
            
            header('Location: users.php');
            exit;
        } catch (PDOException $e) {
            setFlashMessage('danger', 'Error: ' . $e->getMessage());
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            setFlashMessage('danger', $error);
        }
    }
}

// Include header
$pageTitle = $isEdit ? "Edit User" : "Add New User";
include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo $pageTitle; ?></h1>
        <a href="users.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
    </div>

    <?php displayFlashMessages(); ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $isEdit ? 'Edit' : 'New'; ?> User Form</h6>
        </div>
        <div class="card-body">
            <form method="post" id="userForm">
                <div class="mb-3">
                    <label for="username" class="form-label form-required">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label <?php echo $isEdit ? '' : 'form-required'; ?>">
                        Password <?php echo $isEdit ? '(Leave blank to keep current password)' : ''; ?>
                    </label>
                    <input type="password" class="form-control" id="password" name="password" <?php echo $isEdit ? '' : 'required'; ?>>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label <?php echo $isEdit ? '' : 'form-required'; ?>">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" <?php echo $isEdit ? '' : 'required'; ?>>
                </div>
                
                <div class="mb-3">
                    <label for="role" class="form-label form-required">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="cashier" <?php echo ($user['role'] == 'cashier') ? 'selected' : ''; ?>>Cashier</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update' : 'Save'; ?> User
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userForm = document.getElementById('userForm');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    userForm.addEventListener('submit', function(e) {
        // Check if passwords match
        if (passwordInput.value !== confirmPasswordInput.value) {
            e.preventDefault();
            showToast('Passwords do not match.', 'danger');
            return false;
        }
        
        // Additional validation can be added here
        return true;
    });
});
</script>

<?php include 'includes/footer.php'; ?>