<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Handle order actions (delete, pay, etc)
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if ($action && $order_id) {
    // Delete order
    if ($action === 'delete' && isAdmin()) {
        // Hapus terlebih dahulu dari order_services karena foreign key constraint
        $stmt = $db->prepare("DELETE FROM order_services WHERE order_id = ?");
        $stmt->execute([$order_id]);
        
        // Kemudian hapus order
        $stmt = $db->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
        
        // Reset auto-increment dengan vacuum
        $db->exec("ALTER TABLE orders AUTO_INCREMENT = 1");

        
        setFlashMessage('success', 'Order berhasil dihapus');
        header('Location: orders.php');
        exit;
    }
    
    // Process payment
    if ($action === 'pay') {
        $stmt = $db->prepare("SELECT o.*, 
                             (SELECT SUM(subtotal) FROM order_services WHERE order_id = o.id) as service_total
                             FROM orders o WHERE o.id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($order) {
            // Get payment details from request
            $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : $order['payment_method'];
            $payment_amount = isset($_POST['payment_amount']) ? (float)$_POST['payment_amount'] : 0;
            $payment_notes = isset($_POST['payment_notes']) ? $_POST['payment_notes'] : '';
            
            // Use service_total if available, otherwise use the order's total_amount
            $total_to_pay = !empty($order['service_total']) ? $order['service_total'] : $order['total_amount'];
            
            // Calculate change (kembalian)
            $payment_change = $payment_amount - $total_to_pay;
            if ($payment_change < 0) $payment_change = 0; // Pastikan kembalian tidak negatif
            
            // Update order with payment information
            $stmt = $db->prepare("UPDATE orders SET 
                 
                payment_status = 'Lunas',
                payment_method = ?,
                payment_amount = ?,
                payment_change = ?,
                notes = CASE WHEN notes IS NULL OR notes = '' THEN ? ELSE notes || '\n\nPayment Note: ' || ? END,
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?");
            $stmt->execute([
                $payment_method, 
                $payment_amount, 
                $payment_change, 
                ($payment_notes ? 'Payment Note: ' . $payment_notes : ''),
                $payment_notes,
                $order_id
            ]);
            
            setFlashMessage('success', 'Pembayaran berhasil diproses');
            // Redirect to receipt page
            header('Location: receipt.php?id=' . $order_id);
            exit;
        } else {
            setFlashMessage('danger', 'Order tidak ditemukan');
            header('Location: orders.php');
            exit;
        }
    }
}

// Include header
$pageTitle = "Manage Orders";
include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 animate-fade-in">Manage Orders</h1>
        <a href="order_form.php" class="btn btn-primary animate-on-hover" data-hover-animation="pulse">
            <i class="fas fa-plus"></i> New Order
        </a>
    </div>

    <?php displayFlashMessages(); ?>

    <div class="card shadow mb-4 animate-fade-in">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Orders</h6>
            <div class="input-group" style="width: 300px;">
                
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Customer</th>
                            <th>Service Type</th>
                            <th>Weight/Items</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <input type="hidden" id="order_id" name="order_id">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Diterima">Diterima</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Diambil">Diambil</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveStatus">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="paymentModalLabel"><i class="fas fa-money-bill-wave me-2"></i> Proses Pembayaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm" action="orders.php" method="post">
                    <input type="hidden" id="payment_order_id" name="id">
                    <input type="hidden" name="action" value="pay">
                    <input type="hidden" id="payment_change" name="payment_change" value="0">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Proses pembayaran akan mengubah status pesanan menjadi "Diproses" dan menghasilkan struk.
                    </div>
                    
                    <!-- Order Summary Section -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-receipt me-1"></i> Ringkasan Pesanan</h6>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="mb-1"><small>Total Tagihan:</small></p>
                                    <h4 class="text-primary" id="payment_total">Rp 0</h4>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Metode Pembayaran</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="E-Wallet">E-Wallet</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Jumlah Pembayaran</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="payment_amount" name="payment_amount" required>
                            <div class="invalid-feedback" id="payment_amount_feedback"></div>
                        </div>
                        <small class="text-muted cash-only">Masukkan jumlah uang yang diberikan pelanggan</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_notes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="payment_notes" name="payment_notes" rows="2" placeholder="Opsional: Nomor transaksi, referensi, dll."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="submit" class="btn btn-success" id="processPayment" form="paymentForm">
                    <i class="fas fa-check me-1"></i> Proses Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Call animateTableRows function from animations.js after loading data
document.addEventListener('DOMContentLoaded', function() {
    // Initialize table rows with animation after a short delay to ensure data is loaded
    setTimeout(function() {
        animateTableRows();
    }, 300);
});
$("#payment_amount").on("input", function() {
    let bayar = parseFloat($(this).val());
    let total = parseFloat($("#payment_total").text().replace("Rp ", "").replace(".", ""));
    let kembalian = bayar - total;
    $("#payment_change_display").text("Rp " + (kembalian < 0 ? 0 : kembalian));
});

// Inisialisasi komponen UI
$(document).ready(function() {
    // Tooltips akan diinisialisasi oleh orders.js
});
</script>
<script src="assets/js/orders.js"></script>

<?php include 'includes/footer.php'; ?>
