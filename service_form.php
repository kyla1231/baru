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
$service = [
    'id' => '',
    'name' => '',
    'price' => '',
    'description' => ''
];
$isEdit = false;

// Check if we're editing an existing service
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $service_id = (int)$_GET['id'];
    $stmt = $db->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $service = $result;
        $isEdit = true;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $service['name'] = trim($_POST['name']);
    $service['price'] = (float)$_POST['price'];
    $service['description'] = trim($_POST['description']);
    $service['unit_type'] = trim($_POST['unit_type']);
    
    // Validate form data
    $errors = [];
    
    if (empty($service['name'])) {
        $errors[] = 'Service name is required.';
    }
    
    if ($service['price'] <= 0) {
        $errors[] = 'Price must be greater than zero.';
    }
    
    if (!in_array($service['unit_type'], ['kg', 'item'])) {
        $errors[] = 'Unit type must be either kg or item.';
    }
    
    if (empty($errors)) {
        try {
            if ($isEdit) {
                // Update existing service
                $stmt = $db->prepare("UPDATE services SET 
                    name = ?, 
                    price = ?, 
                    description = ?,
                    unit_type = ?,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?");
                
                $stmt->execute([
                    $service['name'], 
                    $service['price'], 
                    $service['description'],
                    $service['unit_type'],
                    $service['id']
                ]);
                
                setFlashMessage('success', 'Service updated successfully.');
            } else {
                // Create new service
                $stmt = $db->prepare("INSERT INTO services (
                    name, price, description, unit_type, created_at, updated_at
                ) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
                
                $stmt->execute([
                    $service['name'], 
                    $service['price'], 
                    $service['description'],
                    $service['unit_type']
                ]);
                
                setFlashMessage('success', 'Service created successfully.');
            }
            
            header('Location: services.php');
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
$pageTitle = $isEdit ? "Edit Service" : "Add New Service";
include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo $pageTitle; ?></h1>
        <a href="services.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Services
        </a>
    </div>

    <?php displayFlashMessages(); ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $isEdit ? 'Edit' : 'New'; ?> Service Form</h6>
        </div>
        <div class="card-body">
            <form method="post" id="serviceForm">
                <div class="mb-3">
                    <label for="name" class="form-label form-required">Service Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($service['name']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="price" class="form-label form-required">Price (Rp)</label>
                    <input type="number" min="0" step="500" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($service['price']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="unit_type" class="form-label form-required">Unit Type</label>
                    <select class="form-select" id="unit_type" name="unit_type" required>
                        <option value="kg" <?php echo (isset($service['unit_type']) && $service['unit_type'] === 'kg') ? 'selected' : ''; ?>>Per Kilogram (kg)</option>
                        <option value="item" <?php echo (isset($service['unit_type']) && $service['unit_type'] === 'item') ? 'selected' : ''; ?>>Per Item</option>
                    </select>
                    <small class="text-muted">Pilih satuan untuk perhitungan biaya layanan</small>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($service['description']); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update' : 'Save'; ?> Service
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>