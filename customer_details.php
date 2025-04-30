<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get customer ID from URL
if (!isset($_GET['id'])) {
    header('Location: customers.php');
    exit;
}

$customer_id = (int)$_GET['id'];

// Get customer details
$stmt_customer = $db->prepare("SELECT * FROM customers WHERE id = ?");
$stmt_customer->execute([$customer_id]);
$customer = $stmt_customer->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    header('Location: customers.php');
    exit;
}

// Get orders for this customer
$stmt_orders = $db->prepare("
    SELECT o.*, o.service_type AS service_name, o.created_at AS order_date
    FROM orders o
    WHERE o.customer_id = ?
    ORDER BY o.created_at DESC
");
$stmt_orders->execute([$customer_id]);
$orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

// Include header
$pageTitle = "Customer Details - " . htmlspecialchars($customer['name']);
include 'includes/header.php';
?>

<!-- Custom CSS for status badges -->
<style>
    .badge-primary {
        background-color: #007bff !important;  /* Blue for Diterima */
        color: white !important;
    }

    .badge-warning {
        background-color: #ffc107 !important; /* Yellow for Diproses */
        color: black !important;
    }

    .badge-success {
        background-color: #28a745 !important; /* Green for Selesai */
        color: white !important;
    }

    .badge-secondary {
        background-color: #6c757d !important; /* Gray for Diambil */
        color: white !important;
    }

    .badge-light {
        background-color: #f8f9fa !important; /* Light for default or unknown status */
        color: black !important;
    }
</style>

<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 animate-fade-in">Customer Details</h1>
        <a href="customers.php" class="btn btn-secondary animate-on-hover" data-hover-animation="pulse">
            <i class="fas fa-arrow-left"></i> Back to Customers
        </a>
    </div>

    <?php displayFlashMessages(); ?>

    <div class="row">
        <!-- Customer Information Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-lg border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Customer Information</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><strong>Name:</strong> <?php echo htmlspecialchars($customer['name']); ?></li>
                        <li><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone']); ?></li>
                        <li><strong>Address:</strong> <?php echo htmlspecialchars($customer['address']); ?></li>
                        <li><strong>Created At:</strong> <?php echo date('d-m-Y H:i', strtotime($customer['created_at'])); ?></li>
                    </ul>
                    <div class="text-center">
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Orders Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-lg border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">Customer Orders</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Service Type</th>
                                    <th>Weight</th>
                                    <th>Total Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['service_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['weight']); ?> kg</td>
                                    <td><?php echo htmlspecialchars($order['total_amount']); ?></td>
                                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                    <td>
                                        <?php 
                                        // Set status badge color based on order status
                                        $statusClass = '';
                                        switch ($order['status']) {
                                            case 'Diterima':
                                                $statusClass = 'badge-primary'; // Blue for "Diterima"
                                                break;
                                            case 'Diproses':
                                                $statusClass = 'badge-warning'; // Yellow for "Diproses"
                                                break;
                                            case 'Selesai':
                                                $statusClass = 'badge-success'; // Green for "Selesai"
                                                break;
                                            case 'Diambil':
                                                $statusClass = 'badge-secondary'; // Gray for "Diambil"
                                                break;
                                            default:
                                                $statusClass = 'badge-light'; // Light for other statuses
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($order['status']); ?></span>
                                    </td>
                                    <td><?php echo date('d M Y, H.i', strtotime($order['order_date'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
