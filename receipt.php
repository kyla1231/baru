<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Check for order ID parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('danger', 'Order ID is required.');
    header('Location: orders.php');
    exit;
}

$order_id = (int)$_GET['id'];

// Get order details
$stmt = $db->prepare("SELECT o.*, c.name as customer_name, c.phone as customer_phone, c.address as customer_address 
                      FROM orders o
                      JOIN customers c ON o.customer_id = c.id
                      WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Get order services
$stmtServices = $db->prepare("SELECT * FROM order_services WHERE order_id = ? ORDER BY id");
$stmtServices->execute([$order_id]);
$orderServices = $stmtServices->fetchAll(PDO::FETCH_ASSOC);

if (!$order) {
    setFlashMessage('danger', 'Order not found.');
    header('Location: orders.php');
    exit;
}

// Check if this is a direct print request
$directPrint = isset($_GET['print']) && $_GET['print'] === 'true';

// Include FPDF library
require('includes/fpdf/fpdf.php');

// If direct print is requested, generate PDF
if ($directPrint) {
    // Create PDF receipt
    class ReceiptPDF extends FPDF {
        function Header() {
            // Set font
            $this->SetFont('Arial', 'B', 16);
            // Title
            $this->Cell(0, 10, 'TIBA LAUNDRY RECEIPT', 0, 1, 'C');
            // Line break
            $this->Ln(5);
        }
        
        function Footer() {
            // Position at 1.5 cm from bottom
            $this->SetY(-15);
            // Set font
            $this->SetFont('Arial', 'I', 8);
            // Page number
            $this->Cell(0, 10, 'Thank you for your business!', 0, 0, 'C');
        }
    }
    
    // Initialize PDF
    $pdf = new ReceiptPDF('P', 'mm', array(80, 200)); // 80mm width for thermal printer
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('Arial', '', 10);
    
    // Store
    $pdf->Cell(0, 5, 'TIBA LAUNDRY EXPRESS', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 5, 'Jl. Laundry No. 123, Jakarta', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Phone: (021) 123-4567', 0, 1, 'C');
    $pdf->Cell(0, 3, '', 0, 1);
    
    // Dashed line
    $pdf->SetDash(1, 1);
    $pdf->Line(10, $pdf->GetY(), 70, $pdf->GetY());
    $pdf->SetDash();
    $pdf->Cell(0, 3, '', 0, 1);
    
    // Receipt info
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(25, 5, 'Order ID:', 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 5, $order['id'], 0, 1);
    
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(25, 5, 'Date:', 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 5, date('d/m/Y H:i', strtotime($order['created_at'])), 0, 1);
    
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(25, 5, 'Customer:', 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 5, $order['customer_name'], 0, 1);
    
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(25, 5, 'Phone:', 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 5, $order['customer_phone'], 0, 1);
    
    $pdf->Cell(0, 3, '', 0, 1);
    // Dashed line
    $pdf->SetDash(1, 1);
    $pdf->Line(10, $pdf->GetY(), 70, $pdf->GetY());
    $pdf->SetDash();
    $pdf->Cell(0, 3, '', 0, 1);
    
    // Order details
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(35, 5, 'Service', 0, 0);
    $pdf->Cell(15, 5, 'Weight/Qty', 0, 0, 'R');
    $pdf->Cell(20, 5, 'Amount', 0, 1, 'R');
    
    $pdf->SetFont('Arial', '', 8);
    
    // Check if we have order services
    if (!empty($orderServices)) {
        // Display all services
        foreach ($orderServices as $service) {
            $pdf->Cell(35, 5, $service['service_name'], 0, 0);
            
            // Display weight or quantity
            if ($service['weight'] > 0) {
                $pdf->Cell(15, 5, number_format($service['weight'], 1) . ' kg', 0, 0, 'R');
            } else {
                $pdf->Cell(15, 5, $service['quantity'] . ' item', 0, 0, 'R');
            }
            
            $pdf->Cell(20, 5, 'Rp ' . number_format($service['subtotal'], 0, ',', '.'), 0, 1, 'R');
        }
    } else {
        // Fallback to old single service format
        $pdf->Cell(35, 5, $order['service_type'], 0, 0);
        $pdf->Cell(15, 5, $order['weight'] . ' kg', 0, 0, 'R');
        $pdf->Cell(20, 5, 'Rp ' . number_format($order['total_amount'], 0, ',', '.'), 0, 1, 'R');
    }
    
    $pdf->Cell(0, 3, '', 0, 1);
    // Dashed line
    $pdf->SetDash(1, 1);
    $pdf->Line(10, $pdf->GetY(), 70, $pdf->GetY());
    $pdf->SetDash();
    $pdf->Cell(0, 3, '', 0, 1);
    
    // Totals
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(50, 5, 'TOTAL:', 0, 0);
    $pdf->Cell(20, 5, 'Rp ' . number_format($order['total_amount'], 0, ',', '.'), 0, 1, 'R');
    
    // Payment details
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(50, 5, 'Payment Method:', 0, 0);
    $pdf->Cell(20, 5, $order['payment_method'], 0, 1, 'R');
    
    if (!empty($order['payment_amount'])) {
        $pdf->Cell(50, 5, 'Amount Paid:', 0, 0);
        $pdf->Cell(20, 5, 'Rp ' . number_format($order['payment_amount'], 0, ',', '.'), 0, 1, 'R');
        
        if (!empty($order['payment_change']) && $order['payment_change'] > 0) {
            $pdf->Cell(50, 5, 'Change:', 0, 0);
            $pdf->Cell(20, 5, 'Rp ' . number_format($order['payment_change'], 0, ',', '.'), 0, 1, 'R');
        }
    }
    
    // Payment status with badge-like styling
    $pdf->Cell(50, 5, 'Payment Status:', 0, 0);
    
    // Set different colors based on payment status
    if (isset($order['payment_status']) && $order['payment_status'] == 'Lunas') {
        $pdf->SetTextColor(0, 128, 0); // Green for paid
    } else {
        $pdf->SetTextColor(220, 53, 69); // Red for unpaid
    }
    
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(20, 5, isset($order['payment_status']) ? $order['payment_status'] : 'Belum Dibayar', 0, 1, 'R');
    $pdf->SetTextColor(0, 0, 0); // Reset text color
    $pdf->SetFont('Arial', '', 8);
    
    $pdf->Cell(0, 3, '', 0, 1);
    
    // Status
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 5, 'Status: ' . $order['status'], 0, 1, 'C');
    
    // Payment details
    $pdf->SetFont('Arial', 'B', 8);
    
    // Payment status with a highlighted box
    $paymentStatus = $order['payment_status'] ?? 'Belum Dibayar';
    if ($paymentStatus == 'Sudah Dibayar' || $paymentStatus == 'Lunas') {
        $pdf->SetFillColor(0, 200, 0); // Green background for paid
        $pdf->Cell(0, 5, 'Payment Status: ' . $paymentStatus, 0, 1, 'C', true);
    } else {
        $pdf->SetFillColor(255, 0, 0); // Red background for unpaid
        $pdf->Cell(0, 5, 'Payment Status: ' . $paymentStatus, 0, 1, 'C', true);
    }
    $pdf->SetFillColor(255, 255, 255); // Reset fill color
    
    // Add payment amount and change to PDF 
    if (!empty($order['payment_amount'])) {
        $pdf->Cell(0, 5, '', 0, 1); // Add space
        $pdf->Cell(40, 5, 'Amount Paid:', 0, 0);
        $pdf->Cell(30, 5, 'Rp ' . number_format($order['payment_amount'], 0, ',', '.'), 0, 1, 'R');
        
        if (!empty($order['payment_change']) && $order['payment_change'] > 0) {
            $pdf->Cell(40, 5, 'Change:', 0, 0);
            $pdf->Cell(30, 5, 'Rp ' . number_format($order['payment_change'], 0, ',', '.'), 0, 1, 'R');
        }
    }
    
    $pdf->Cell(0, 5, '', 0, 1);
    
    // Notes
    if (!empty($order['notes'])) {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 5, 'Notes:', 0, 1);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(0, 5, $order['notes'], 0, 'L');
    }
    
    $pdf->Cell(0, 5, '', 0, 1);
    
    // Estimated completion
    $estimatedDate = date('d/m/Y', strtotime($order['created_at'] . ' +2 days'));
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(0, 5, 'Estimated completion: ' . $estimatedDate, 0, 1, 'C');
    
    $pdf->Cell(0, 10, '', 0, 1);
    
    // Footer message
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 5, 'Thank you for your business!', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Please keep this receipt for order pickup.', 0, 1, 'C');
    
    // Output PDF
    $pdf->Output('Receipt_Order_' . $order['id'] . '.pdf', 'D');
    exit;
}

// Include header for normal view
$pageTitle = "Receipt for Order #" . $order_id;
include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Receipt for Order #<?php echo $order_id; ?></h1>
        <div>
            <button id="printButton" class="btn btn-info">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <a href="orders.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>

    <?php displayFlashMessages(); ?>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-body p-5" id="receiptContent">
                    <div class="text-center mb-4">
                        <h2 class="mb-0">TIBA LAUNDRY EXPRESS</h2>
                        <p class="mb-1">Jl. Luku 1 No. 51 Kwala Bekala Medan Johor</p>
                        <p>Phone: 085210364697</p>
                        <h4 class="mt-4 mb-3">RECEIPT</h4>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Order Information</h6>
                            <p>
                                <strong>Order ID:</strong> <?php echo $order['id']; ?><br>
                                <strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?><br>
                                <strong>Status:</strong> <span class="badge bg-<?php 
                                    switch ($order['status']) {
                                        case 'Diterima': echo 'primary'; break;
                                        case 'Diproses': echo 'warning'; break;
                                        case 'Selesai': echo 'success'; break;
                                        case 'Diambil': echo 'secondary'; break;
                                        default: echo 'info';
                                    }
                                ?>"><?php echo $order['status']; ?></span><br>
                                <strong>Payment Status:</strong> 
                                <span class="badge bg-<?php echo ($order['payment_status'] ?? 'Belum Dibayar') == 'Sudah Dibayar' ? 'success' : 'danger'; ?>">
                                    <?php echo $order['payment_status'] ?? 'Belum Dibayar'; ?>
                                </span><br>
                                <strong>Payment Method:</strong> <?php echo $order['payment_method']; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Customer Information</h6>
                            <p>
                                <strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?><br>
                                <strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?><br>
                                <strong>Address:</strong> <?php echo htmlspecialchars($order['customer_address']); ?>
                            </p>
                        </div>
                    </div>
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th class="text-end">Weight/Qty</th>
                                <th class="text-end">Price per Unit</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orderServices)): ?>
                                <?php foreach ($orderServices as $service): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                                    <td class="text-end">
                                        <?php if ($service['weight'] > 0): ?>
                                            <?php echo number_format($service['weight'], 1); ?> kg
                                        <?php else: ?>
                                            <?php echo $service['quantity']; ?> item
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">Rp <?php echo number_format($service['price'], 0, ',', '.'); ?></td>
                                    <td class="text-end">Rp <?php echo number_format($service['subtotal'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Fallback to backward compatibility if no order services -->
                                <tr>
                                    <td><?php echo htmlspecialchars($order['service_type']); ?></td>
                                    <td class="text-end"><?php echo $order['weight']; ?> kg</td>
                                    <td class="text-end">Rp <?php echo number_format($order['price_per_unit'], 0, ',', '.'); ?></td>
                                    <td class="text-end">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Amount</th>
                                <th class="text-end">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></th>
                            </tr>
                            <?php if (!empty($order['payment_amount'])): ?>
                            <tr>
                                <th colspan="3" class="text-end">Amount Paid</th>
                                <th class="text-end">Rp <?php echo number_format($order['payment_amount'], 0, ',', '.'); ?></th>
                            </tr>
                            <?php if (!empty($order['payment_change']) && $order['payment_change'] > 0): ?>
                            <tr>
                                <th colspan="3" class="text-end">Change</th>
                                <th class="text-end">Rp <?php echo number_format($order['payment_change'], 0, ',', '.'); ?></th>
                            </tr>
                            <?php endif; ?>
                            <?php endif; ?>
                        </tfoot>
                    </table>
                    
                    <?php if (!empty($order['notes'])): ?>
                    <div class="mt-4">
                        <h6 class="font-weight-bold">Notes:</h6>
                        <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-4 text-center">
                        <p>
                            <strong>Estimated completion:</strong> <?php echo date('d/m/Y', strtotime($order['created_at'] . ' +2 days')); ?><br>
                            Please keep this receipt for order pickup.
                        </p>
                        <p class="mt-4">Thank you for your business!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('printButton').addEventListener('click', function() {
    const content = document.getElementById('receiptContent').innerHTML;
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <html>
        <head>
            <title>Receipt - Order #<?php echo $order_id; ?></title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    padding: 20px;
                    max-width: 800px;
                    margin: 0 auto;
                }
                @media print {
                    body {
                        width: 80mm; /* Thermal printer width */
                        padding: 5mm;
                    }
                    .table {
                        font-size: 10px;
                    }
                    p {
                        font-size: 12px;
                        margin-bottom: 5px;
                    }
                }
            </style>
        </head>
        <body onload="window.print(); window.onfocus=function(){ window.close(); }">
            ${content}
        </body>
        </html>
    `);
    
    printWindow.document.close();
});
</script>

<?php include 'includes/footer.php'; ?>
