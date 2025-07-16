<?php
require_once '../includes/config.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';

if ($_POST && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if ($username && $password) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_name'] = $user['full_name'];
                $_SESSION['admin_role'] = $user['role'];
                
                // Update last login
                $update_stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $update_stmt->execute([$user['id']]);
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error_message = 'Username atau password salah!';
            }
        } catch (PDOException $e) {
            $error_message = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    } else {
        $error_message = 'Mohon lengkapi username dan password.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .login-header {
            margin-bottom: 30px;
        }
        
        .login-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        
        .login-header h1 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .login-header p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
        }
        
        .form-control {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .error-message {
            background: #fee;
            color: #c0392b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #fadbd8;
        }
        
        .back-to-site {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }
        
        .back-to-site a {
            color: #7f8c8d;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .back-to-site a:hover {
            color: #3498db;
        }
        
        .default-credentials {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .default-credentials strong {
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="../assets/images/logo.png" alt="Logo" onerror="this.style.display='none'">
            <h1>Admin Panel</h1>
            <p><?php echo SITE_NAME; ?></p>
        </div>
        
        <?php if ($error_message): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" class="form-control" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                           placeholder="Masukkan username">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Masukkan password">
                </div>
            </div>
            
            <button type="submit" name="login" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </form>
        
        <div class="default-credentials">
            <strong>Default Login:</strong><br>
            Username: <code>admin</code><br>
            Password: <code>admin123</code>
        </div>
        
        <div class="back-to-site">
            <a href="../index.php">
                <i class="fas fa-arrow-left"></i> Kembali ke Website
            </a>
        </div>
    </div>

    <script>
        // Auto focus on username field
        document.getElementById('username').focus();
        
        // Simple form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Mohon lengkapi username dan password.');
                return false;
            }
        });
    </script>
</body>
</html>