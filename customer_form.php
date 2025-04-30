<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Initialize variables
$customer = [
    'id' => '',
    'name' => '',
    'phone' => '',
    'address' => '',
    'email' => ''
];
$isEdit = false;

// Check if we're editing an existing customer
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $customer_id = (int)$_GET['id'];
    $stmt = $db->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$customer_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $customer = $result;
        $isEdit = true;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $customer['name'] = trim($_POST['name']);
    $customer['phone'] = trim($_POST['phone']);
    $customer['address'] = trim($_POST['address']);
    $customer['email'] = trim($_POST['email']);
    
    // Validate data
    $errors = [];
    if (empty($customer['name'])) {
        $errors[] = 'Name is required';
    }
    
    if (empty($customer['phone'])) {
        $errors[] = 'Phone number is required';
    }
    
    if (empty($errors)) {
        try {
            if ($isEdit) {
                // Update existing customer
                $stmt = $db->prepare("UPDATE customers SET 
                    name = ?, 
                    phone = ?, 
                    address = ?, 
                    email = ?, 
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?");
                
                $stmt->execute([
                    $customer['name'], 
                    $customer['phone'], 
                    $customer['address'], 
                    $customer['email'],
                    $customer['id']
                ]);
                
                setFlashMessage('success', 'Customer updated successfully.');
            } else {
                // Create new customer
                $stmt = $db->prepare("INSERT INTO customers (
                    name, phone, address, email, created_at, updated_at
                ) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
                
                $stmt->execute([
                    $customer['name'], 
                    $customer['phone'], 
                    $customer['address'], 
                    $customer['email']
                ]);
                
                $customer['id'] = $db->lastInsertId();
                setFlashMessage('success', 'Customer created successfully.');
            }
            
            // Check if we're in a popup window
            if (isset($_GET['popup']) && $_GET['popup'] === 'true') {
                echo "<script>
                    window.opener.location.reload();
                    window.close();
                </script>";
                exit;
            }
            
            header('Location: customers.php');
            exit;
        } catch (PDOException $e) {
            setFlashMessage('danger', 'Error: ' . $e->getMessage());
        }
    } else {
        foreach ($errors as $error) {
            setFlashMessage('danger', $error);
        }
    }
}

// Set page title
$pageTitle = $isEdit ? "Edit Customer" : "New Customer";

// Check if we're in a popup
$isPopup = isset($_GET['popup']) && $_GET['popup'] === 'true';

// Include header
if (!$isPopup) {
    include 'includes/header.php';
} else {
    // Simple header for popup
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($pageTitle) . ' - Laundry Management System</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/css/style.css" rel="stylesheet">
    </head>
    <body class="bg-light">
    <div class="container py-4">';
}
?>

<div class="<?php echo $isPopup ? '' : 'container-fluid py-4'; ?>">
    <?php if (!$isPopup): ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo $pageTitle; ?></h1>
        <a href="customers.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Customers
        </a>
    </div>
    <?php else: ?>
    <h2 class="text-center mb-4"><?php echo $pageTitle; ?></h2>
    <?php endif; ?>

    <?php displayFlashMessages(); ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $isEdit ? 'Edit' : 'New'; ?> Customer Form</h6>
        </div>
        <div class="card-body">
            <form method="post" id="customerForm">
                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($customer['address']); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update' : 'Save'; ?> Customer
                </button>
            </form>
        </div>
    </div>
</div>

<?php 
if (!$isPopup) {
    include 'includes/footer.php';
} else {
    // Simple footer for popup
    echo '</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>';
}
?>
