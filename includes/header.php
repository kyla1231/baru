<?php
// Set timezone ke WIB (Asia/Jakarta)
date_default_timezone_set('Asia/Jakarta');

// Start session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta charset="UTF-8">
    
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'TIBA LAUNDRY EXPRESS'; ?></title>
    <link rel="icon" type="image/png" href="favicon.png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style_new.css" rel="stylesheet">
    <link href="assets/css/style_animation.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-heading text-center">
                <i class="fas fa-tshirt me-2"></i> TIBA LAUNDRY EXPRESS
            </div>

            <div class="list-group list-group-flush">
                <a href="dashboard.php" class="list-group-item list-group-item-action animate-slide-left <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" style="animation-delay: 0.1s">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="orders.php" class="list-group-item list-group-item-action animate-slide-left <?php echo in_array(basename($_SERVER['PHP_SELF']), ['orders.php', 'order_form.php']) ? 'active' : ''; ?>" style="animation-delay: 0.2s">
                    <i class="fas fa-shopping-cart me-2"></i> Orders
                </a>
                <a href="customers.php" class="list-group-item list-group-item-action animate-slide-left <?php echo in_array(basename($_SERVER['PHP_SELF']), ['customers.php', 'customer_form.php']) ? 'active' : ''; ?>" style="animation-delay: 0.3s">
                    <i class="fas fa-users me-2"></i> Customers
                </a>
                <?php if (function_exists('isAdmin') && isAdmin()): ?>
                <a href="services.php" class="list-group-item list-group-item-action animate-slide-left <?php echo in_array(basename($_SERVER['PHP_SELF']), ['services.php', 'service_form.php']) ? 'active' : ''; ?>" style="animation-delay: 0.4s">
                    <i class="fas fa-concierge-bell me-2"></i> Services
                </a>
                <a href="users.php" class="list-group-item list-group-item-action animate-slide-left <?php echo in_array(basename($_SERVER['PHP_SELF']), ['users.php', 'user_form.php']) ? 'active' : ''; ?>" style="animation-delay: 0.5s">
                    <i class="fas fa-user-cog me-2"></i> Users
                </a>
                <a href="reports.php" class="list-group-item list-group-item-action animate-slide-left <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" style="animation-delay: 0.6s">
                    <i class="fas fa-chart-line me-2"></i> Reports
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-3">
                <div class="container-fluid">
                    <button class="btn btn-sm btn-primary shadow-sm" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <h5 class="mb-0 ms-3 text-gray-800 d-none d-md-block">
                        <?php 
                            $currentPage = basename($_SERVER['PHP_SELF']);
                            switch ($currentPage) {
                                case 'dashboard.php':
                                    echo '<i class="fas fa-tachometer-alt me-2"></i> Dashboard';
                                    break;
                                case 'orders.php':
                                    echo '<i class="fas fa-shopping-cart me-2"></i> Orders Management';
                                    break;
                                case 'order_form.php':
                                    echo '<i class="fas fa-edit me-2"></i> ' . (isset($_GET['id']) ? 'Edit Order' : 'New Order');
                                    break;
                                case 'customers.php':
                                    echo '<i class="fas fa-users me-2"></i> Customers Management';
                                    break;
                                case 'customer_form.php':
                                    echo '<i class="fas fa-user-edit me-2"></i> ' . (isset($_GET['id']) ? 'Edit Customer' : 'New Customer');
                                    break;
                                case 'services.php':
                                    echo '<i class="fas fa-concierge-bell me-2"></i> Services Management';
                                    break;
                                case 'service_form.php':
                                    echo '<i class="fas fa-edit me-2"></i> ' . (isset($_GET['id']) ? 'Edit Service' : 'New Service');
                                    break;
                                case 'users.php':
                                    echo '<i class="fas fa-user-cog me-2"></i> User Management';
                                    break;
                                case 'user_form.php':
                                    echo '<i class="fas fa-user-edit me-2"></i> ' . (isset($_GET['id']) ? 'Edit User' : 'New User');
                                    break;
                                case 'reports.php':
                                    echo '<i class="fas fa-chart-line me-2"></i> Financial Reports';
                                    break;
                                case 'settings.php':
                                    echo '<i class="fas fa-cog me-2"></i> System Settings';
                                    break;
                                default:
                                    echo 'TIBA LAUNDRY EXPRESS';
                            }
                        ?>
                    </h5>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <span class="d-none d-md-block me-3 text-gray-600">
                            <i class="far fa-calendar-alt me-1"></i> <?php echo date('d M Y'); ?>
                        </span>
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle shadow-sm" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1 text-primary"></i> 
                                <span class="d-none d-md-inline"><?php echo $_SESSION['username'] ?? 'User'; ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                                <li>
                                    <div class="dropdown-item-text text-center">
                                        <div class="fw-bold text-gray-800"><?php echo $_SESSION['username'] ?? 'User'; ?></div>
                                        <div class="small text-gray-600 mb-1">
                                            <span class="badge bg-primary"><?php echo ucfirst($_SESSION['role'] ?? 'Unknown'); ?></span>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2 text-danger"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
