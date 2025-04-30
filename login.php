<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Redirect to dashboard if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Prepare the query to prevent SQL injection
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bindParam(1, $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables (session already started in includes/functions.php)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect to dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - TIBA LAUNDRY EXPRESS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4caf50;
            --info-color: #2196f3;
            --warning-color: #ff9800;
            --danger-color: #f44336;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f2f5;
            position: relative;
            overflow-x: hidden;
        }

        /* Background Animation */
        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(-45deg, #4361ee, #3a0ca3, #4cc9f0, #4895ef);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        /* Floating Shapes - Simplified for mobile */
        .shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            display: none; /* Disable on mobile to improve performance */
        }

        .shape {
            position: absolute;
            display: block;
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.08);
            animation: animate 25s linear infinite;
            bottom: -150px;
            border-radius: 50%;
        }

        @keyframes animate {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
                border-radius: 0;
            }
            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
                border-radius: 50%;
            }
        }

        .container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1200px;
            padding: 0 15px;
        }

        .login-content {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            width: 100%;
        }

        /* Left side - Branding */
        .login-branding {
            flex: 1;
            min-width: 100%;
            padding: 20px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            animation: slide-in-left 0.8s cubic-bezier(0.250, 0.460, 0.450, 0.940) both;
        }

        @keyframes slide-in-left {
            0% {
                transform: translateX(-100px);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .brand-logo {
            font-size: 50px;
            color: white;
            margin-bottom: 15px;
            filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.2));
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        .brand-title {
            font-size: 24px;
            font-weight: 700;
            color: white;
            margin-bottom: 10px;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: text-focus-in 1s ease-out both;
        }

        @keyframes text-focus-in {
            0% {
                filter: blur(12px);
                opacity: 0;
            }
            100% {
                filter: blur(0px);
                opacity: 1;
            }
        }

        .brand-subtitle {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            max-width: 300px;
            margin: 0 auto;
            animation: fade-in 1.2s ease-out 0.5s both;
        }

        @keyframes fade-in {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Right side - Login Form */
        .login-form-container {
            flex: 1;
            min-width: 100%;
            max-width: 100%;
            padding: 15px;
            animation: slide-in-right 0.8s cubic-bezier(0.250, 0.460, 0.450, 0.940) 0.3s both;
        }

        @keyframes slide-in-right {
            0% {
                transform: translateX(100px);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            overflow: hidden;
            position: relative;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: 0.5s;
        }

        .login-card:hover::before {
            left: 100%;
            transition: 0.5s;
        }

        .login-header {
            margin-bottom: 20px;
            text-align: center;
        }

        .login-header h3 {
            font-size: 22px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
            position: relative;
            display: inline-block;
        }

        .login-header h3::after {
            content: '';
            position: absolute;
            width: 50%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            bottom: -6px;
            left: 25%;
            border-radius: 5px;
        }

        .login-header p {
            color: #777;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #555;
            transition: all 0.3s;
            opacity: 0;
            transform: translateY(-20px);
            animation: fade-in-down 0.5s forwards;
            animation-delay: calc(var(--i) * 0.1s);
            font-size: 14px;
        }

        @keyframes fade-in-down {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-control-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            animation: slide-up 0.5s forwards;
            opacity: 0;
            transform: translateY(20px);
            animation-delay: calc(var(--i) * 0.2s);
        }

        @keyframes slide-up {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .input-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background-color: #f8f9fa;
            color: var(--primary-color);
            border-right: 1px solid #eaeaea;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .form-control {
            flex: 1;
            height: 45px;
            padding: 10px 12px;
            font-size: 14px;
            border: none;
            outline: none;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: none;
        }

        .form-control:focus + .input-icon {
            background-color: var(--primary-color);
            color: white;
        }

        .form-control-wrapper::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            transition: all 0.3s ease;
        }

        .form-control-wrapper:focus-within::after {
            width: 100%;
            left: 0;
        }

        .login-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
            margin-top: 15px;
            height: 45px;
            animation: bounce-in 0.6s ease 1s both;
        }

        @keyframes bounce-in {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            40% {
                opacity: 1;
                transform: scale(1.05);
            }
            60% {
                transform: scale(0.95);
            }
            100% {
                transform: scale(1);
            }
        }

        .login-btn:hover {
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.5);
            transform: translateY(-3px);
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .alert-error {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger-color);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
            font-size: 13px;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .alert-error i {
            margin-right: 8px;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            animation: fade-in 1s ease 1.5s both;
        }

        /* Loading button animation */
        .loading-btn {
            position: relative;
        }

        .loading-btn .spinner {
            animation: spin 1.2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Styles */
        @media (min-width: 576px) {
            .login-branding {
                padding: 30px 20px;
            }
            
            .login-form-container {
                padding: 20px;
            }
            
            .login-card {
                padding: 30px;
            }
            
            .brand-title {
                font-size: 28px;
            }
            
            .brand-subtitle {
                font-size: 16px;
            }
            
            .login-header h3 {
                font-size: 24px;
            }
            
            .login-header p {
                font-size: 15px;
            }
            
            .form-group label {
                font-size: 15px;
            }
            
            .form-control {
                font-size: 15px;
            }
            
            .login-btn {
                font-size: 16px;
            }
            
            .alert-error {
                font-size: 14px;
            }
            
            .footer {
                font-size: 13px;
            }
        }

        @media (min-width: 768px) {
            .login-content {
                flex-direction: row;
            }
            
            .login-branding {
                min-width: 320px;
                max-width: 50%;
                padding: 40px;
            }
            
            .login-form-container {
                min-width: 320px;
                max-width: 450px;
                padding: 20px;
            }
            
            .brand-logo {
                font-size: 70px;
            }
            
            .brand-title {
                font-size: 32px;
            }
            
            .brand-subtitle {
                font-size: 18px;
            }
            
            .shapes {
                display: block;
            }
        }

        @media (min-width: 992px) {
            .login-branding {
                padding: 50px;
            }
            
            .login-form-container {
                padding: 30px;
            }
            
            .login-card {
                padding: 40px;
            }
            
            .brand-title {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="background"></div>
    
    <!-- Animated Shapes - Disabled on mobile -->
    <div class="shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="container">
        <div class="login-content">
            <!-- Branding Section -->
            <div class="login-branding">
                <div class="brand-logo">
                    <i class="fas fa-tshirt"></i>
                </div>
                <h1 class="brand-title">TIBA LAUNDRY EXPRESS</h1>
                <p class="brand-subtitle">Your trusted partner for clean, fresh, and professional laundry services.</p>
            </div>
            
            <!-- Login Form Section -->
            <div class="login-form-container">
                <div class="login-card">
                    <div class="login-header">
                        <h3>Welcome Back</h3>
                        <p>Enter your credentials to access your account</p>
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="login.php" id="loginForm">
                        <div class="form-group">
                            <label for="username" style="--i:1">Username</label>
                            <div class="form-control-wrapper" style="--i:1">
                                <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
                                <div class="input-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" style="--i:2">Password</label>
                            <div class="form-control-wrapper" style="--i:2">
                                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                                <div class="input-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" id="loginButton" class="login-btn">
                            <span id="buttonText"><i class="fas fa-sign-in-alt me-2"></i> Login</span>
                        </button>
                    </form>
                </div>
                
                <div class="footer">
                    <p>&copy; <?php echo date('Y'); ?> TIBA LAUNDRY EXPRESS. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Form submission animation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const button = document.getElementById('loginButton');
            const buttonText = document.getElementById('buttonText');
            
            button.classList.add('loading-btn');
            buttonText.innerHTML = '<i class="fas fa-spinner spinner me-2"></i> Logging in...';
            button.disabled = true;
        });
        
        // Interactive form effects
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            // Focus effect with scale
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
                this.parentElement.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
            });
            
            // Return to normal on blur
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.style.transform = 'translateY(0)';
                    this.parentElement.style.boxShadow = '0 3px 10px rgba(0, 0, 0, 0.05)';
                }
            });
            
            // Keep the effect if input has value
            input.addEventListener('input', function() {
                if (this.value) {
                    this.parentElement.classList.add('has-value');
                } else {
                    this.parentElement.classList.remove('has-value');
                }
            });
        });
        
        // Initial animation sequence
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.querySelector('.login-card').style.opacity = '1';
            }, 300);
        });
        
        // Enable shapes animation only on larger screens
        function checkScreenSize() {
            const shapes = document.querySelector('.shapes');
            if (window.innerWidth >= 768) {
                shapes.style.display = 'block';
            } else {
                shapes.style.display = 'none';
            }
        }
        
        // Check on load and resize
        window.addEventListener('load', checkScreenSize);
        window.addEventListener('resize', checkScreenSize);
    </script>
</body>
</html>