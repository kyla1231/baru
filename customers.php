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

// Process delete action
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Check if the customer has any orders
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM orders WHERE customer_id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        setFlashMessage('danger', 'This customer cannot be deleted because they have existing orders.');
    } else {
        try {
            $stmt = $db->prepare("DELETE FROM customers WHERE id = ?");
            $stmt->execute([$id]);
            
            setFlashMessage('success', 'Customer deleted successfully.');
        } catch (PDOException $e) {
            setFlashMessage('danger', 'Error deleting customer: ' . $e->getMessage());
        }
    }
    
    header('Location: customers.php');
    exit;
}

// Include header
$pageTitle = "Customer Management";
include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 animate-fade-in">Customer Management</h1>
        <a href="customer_form.php" class="btn btn-primary animate-on-hover" data-hover-animation="pulse">
            <i class="fas fa-plus"></i> Add New Customer
        </a>
    </div>

    <?php displayFlashMessages(); ?>

    <div class="card shadow mb-4 animate-fade-in" style="animation-delay: 0.2s">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">All Customers</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="customersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Total Orders</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table will be filled by AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Call animation initialization
    initializeAnimations();

    // Initialize DataTable
    const table = $('#customersTable').DataTable({
        ajax: {
            url: 'ajax/get_customers.php',
            dataSrc: ''
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'name' },
            { data: 'phone' },
            { data: 'address' },
            { data: 'total_orders' },
            { 
                data: 'created_at',
                render: function(data) {
                    return formatDate(data);
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data) {
                    return `
                        <div class="table-actions">
                            <a href="customer_details.php?id=${data.id}" class="btn btn-sm btn-info animate-on-hover" data-hover-animation="pulse" data-bs-toggle="tooltip" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="customer_form.php?id=${data.id}" class="btn btn-sm btn-info animate-on-hover" data-hover-animation="pulse" data-bs-toggle="tooltip" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0);" onclick="confirmDelete(${data.id})" class="btn btn-sm btn-danger animate-on-hover" data-hover-animation="pulse" data-bs-toggle="tooltip" title="Delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    `;
                }
            }
        ],

        order: [[0, 'desc']]
    });

    // After data is loaded, apply row animations
    table.on('draw.dt', function() {
        setTimeout(function() {
            animateTableRows();
        }, 100);
    });

    // Refresh table data every 30 seconds
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 30000);
});

// Function to confirm deletion
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
        window.location.href = `customers.php?delete=true&id=${id}`;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
