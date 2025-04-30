<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Cek apakah pengguna sudah login dan adalah admin
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Hanya admin yang dapat mengakses halaman ini
if (!isAdmin()) {
    setFlashMessage('danger', 'You do not have permission to access that page.');
    header('Location: dashboard.php');
    exit;
}

// Inisialisasi default setting jika belum ada dalam sesi
if (!isset($_SESSION['settings'])) {
    $_SESSION['settings'] = [
        'company_name' => 'Laundry Management System',
        'company_address' => 'Jl. Contoh No. 123, Kota',
        'company_phone' => '0812-3456-7890',
        'company_email' => 'info@laundry.com',
        'tax_percentage' => 0,
        'currency_symbol' => 'Rp',
        'date_format' => 'd/m/Y',
        'receipt_footer' => 'Terima kasih telah menggunakan jasa kami!'
    ];
}

// Cek jika ada form yang disubmit untuk mengupdate setting
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['settings'] = [
        'company_name' => trim($_POST['company_name']),
        'company_address' => trim($_POST['company_address']),
        'company_phone' => trim($_POST['company_phone']),
        'company_email' => trim($_POST['company_email']),
        'tax_percentage' => (float)$_POST['tax_percentage'],
        'currency_symbol' => trim($_POST['currency_symbol']),
        'date_format' => trim($_POST['date_format']),
        'receipt_footer' => trim($_POST['receipt_footer'])
    ];

    setFlashMessage('success', 'Settings saved successfully.');
}

// Include header
$pageTitle = "System Settings";
include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 animate-fade-in">System Settings</h1>
    </div>

    <?php displayFlashMessages(); ?>

    <div class="card shadow mb-4 animate-fade-in" style="animation-delay: 0.1s">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">General Settings</h6>
        </div>
        <div class="card-body">
            <form method="post" id="settingsForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo htmlspecialchars($_SESSION['settings']['company_name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="company_address" class="form-label">Company Address</label>
                            <textarea class="form-control" id="company_address" name="company_address" rows="2"><?php echo htmlspecialchars($_SESSION['settings']['company_address']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="company_phone" class="form-label">Company Phone</label>
                            <input type="text" class="form-control" id="company_phone" name="company_phone" value="<?php echo htmlspecialchars($_SESSION['settings']['company_phone']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="company_email" class="form-label">Company Email</label>
                            <input type="email" class="form-control" id="company_email" name="company_email" value="<?php echo htmlspecialchars($_SESSION['settings']['company_email']); ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tax_percentage" class="form-label">Tax Percentage (%)</label>
                            <input type="number" min="0" max="100" step="0.01" class="form-control" id="tax_percentage" name="tax_percentage" value="<?php echo htmlspecialchars($_SESSION['settings']['tax_percentage']); ?>">
                            <div class="form-text">Enter tax percentage (e.g., 10 for 10%)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="currency_symbol" class="form-label">Currency Symbol</label>
                            <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="<?php echo htmlspecialchars($_SESSION['settings']['currency_symbol']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="date_format" class="form-label">Date Format</label>
                            <select class="form-select" id="date_format" name="date_format" required>
                                <option value="d/m/Y" <?php echo ($_SESSION['settings']['date_format'] == 'd/m/Y') ? 'selected' : ''; ?>>DD/MM/YYYY (31/12/2023)</option>
                                <option value="m/d/Y" <?php echo ($_SESSION['settings']['date_format'] == 'm/d/Y') ? 'selected' : ''; ?>>MM/DD/YYYY (12/31/2023)</option>
                                <option value="Y-m-d" <?php echo ($_SESSION['settings']['date_format'] == 'Y-m-d') ? 'selected' : ''; ?>>YYYY-MM-DD (2023-12-31)</option>
                                <option value="d M Y" <?php echo ($_SESSION['settings']['date_format'] == 'd M Y') ? 'selected' : ''; ?>>DD Mon YYYY (31 Dec 2023)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="receipt_footer" class="form-label">Receipt Footer Message</label>
                            <textarea class="form-control" id="receipt_footer" name="receipt_footer" rows="2"><?php echo htmlspecialchars($_SESSION['settings']['receipt_footer']); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary animate-on-hover" data-hover-animation="pulse">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize animations
    initializeAnimations();

    // Toggle dark mode on button click
    const toggleButton = document.getElementById('toggle-dark-mode');
    const currentMode = localStorage.getItem('dark_mode') === 'true';
    if (currentMode) {
        document.body.classList.add('dark-mode');
    }

    toggleButton.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        const isDarkMode = document.body.classList.contains('dark-mode');
        localStorage.setItem('dark_mode', isDarkMode);
    });
});
</script>

<?php include 'includes/footer.php'; ?>
