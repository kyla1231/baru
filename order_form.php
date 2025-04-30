<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Set zona waktu ke Indonesia
date_default_timezone_set('Asia/Jakarta');

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Initialize variables
$order = [
    'id' => '',
    'customer_id' => '',
    'total_amount' => '',
    'payment_method' => '',
    'payment_amount' => '',
    'payment_change' => '',
    'payment_status' => 'Belum Dibayar',
    'status' => 'Diterima',
    'notes' => '',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
];
$isEdit = false;
$customerId = '';
$customers = [];
$orderServices = [];

// Fetch all customers for dropdown
$stmt = $db->query("SELECT id, name, phone FROM customers ORDER BY name");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all services for dropdown
try {
    $stmt = $db->query("SELECT id, name, price, description, unit_type FROM services ORDER BY name");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $services = [];
    setFlashMessage('warning', 'Could not load services: ' . $e->getMessage());
}

// Check if we're editing an existing order
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $order_id = (int)$_GET['id'];
    $stmt = $db->prepare("SELECT *, 
                          DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at,
                          DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at 
                          FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $order = $result;
        $isEdit = true;
        
        // Fetch order services
        $stmtServices = $db->prepare("SELECT * FROM order_services WHERE order_id = ?");
        $stmtServices->execute([$order_id]);
        $orderServices = $stmtServices->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $order['customer_id'] = (int)$_POST['customer_id'];
    $order['service_type'] = $_POST['service_type'];
    $order['weight'] = (float)$_POST['weight'];
    $order['price_per_unit'] = (float)$_POST['price_per_unit'];
    $order['total_amount'] = (float)$_POST['total_amount'];
    $order['payment_method'] = $_POST['payment_method'];
    $order['payment_amount'] = isset($_POST['payment_amount']) ? (float)$_POST['payment_amount'] : 0;
    $order['payment_change'] = isset($_POST['payment_change']) ? (float)$_POST['payment_change'] : 0;
    $order['payment_status'] = $_POST['payment_status'];
    $order['status'] = $_POST['status'];
    $order['notes'] = $_POST['notes'];
    $order['updated_at'] = date('Y-m-d H:i:s');
    
    // Get selected services from JSON
    $selectedServices = [];
    if (isset($_POST['selected_services']) && !empty($_POST['selected_services'])) {
        $selectedServices = json_decode($_POST['selected_services'], true);
    }
    
    try {
        $db->beginTransaction();
        
        if ($isEdit) {
            // Update existing order
            $stmt = $db->prepare("UPDATE orders SET 
                customer_id = ?, 
                service_type = ?, 
                weight = ?, 
                price_per_unit = ?, 
                total_amount = ?, 
                payment_method = ?,
                payment_amount = ?,
                payment_change = ?,
                payment_status = ?, 
                status = ?, 
                notes = ?,
                updated_at = ?
                WHERE id = ?");
            
            $stmt->execute([
                $order['customer_id'], 
                $order['service_type'], 
                $order['weight'], 
                $order['price_per_unit'], 
                $order['total_amount'], 
                $order['payment_method'],
                $order['payment_amount'],
                $order['payment_change'],
                $order['payment_status'],
                $order['status'],
                $order['notes'],
                $order['updated_at'],
                $order['id']
            ]);
            
            // Delete previous order services
            $stmtDelete = $db->prepare("DELETE FROM order_services WHERE order_id = ?");
            $stmtDelete->execute([$order['id']]);
            
            // Insert new order services
            if (!empty($selectedServices)) {
                $stmtInsertService = $db->prepare("INSERT INTO order_services (
                    order_id, service_id, service_name, price, weight, quantity, subtotal, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                
                foreach ($selectedServices as $service) {
                    $weight = $service['quantityType'] === 'weight' ? $service['quantity'] : 0;
                    $quantity = $service['quantityType'] === 'unit' ? $service['quantity'] : 1;
                    
                    $stmtInsertService->execute([
                        $order['id'],
                        $service['id'],
                        $service['name'],
                        $service['price'],
                        $weight,
                        $quantity,
                        $service['subtotal'],
                        date('Y-m-d H:i:s')
                    ]);
                }
            }
            
            setFlashMessage('success', 'Order updated successfully.');
        } else {
            // Create new order
            $order['created_at'] = date('Y-m-d H:i:s');
            
            $stmt = $db->prepare("INSERT INTO orders (
                customer_id, service_type, weight, price_per_unit, total_amount, 
                payment_method, payment_amount, payment_change, payment_status,
                status, notes, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $order['customer_id'], 
                $order['service_type'], 
                $order['weight'], 
                $order['price_per_unit'], 
                $order['total_amount'], 
                $order['payment_method'],
                $order['payment_amount'],
                $order['payment_change'],
                $order['payment_status'],
                $order['status'],
                $order['notes'],
                $order['created_at'],
                $order['updated_at']
            ]);
            
            $order['id'] = $db->lastInsertId();
            
            // Insert order services
            if (!empty($selectedServices)) {
                $stmtInsertService = $db->prepare("INSERT INTO order_services (
                    order_id, service_id, service_name, price, weight, quantity, subtotal, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                
                foreach ($selectedServices as $service) {
                    $weight = $service['quantityType'] === 'weight' ? $service['quantity'] : 0;
                    $quantity = $service['quantityType'] === 'unit' ? $service['quantity'] : 1;
                    
                    $stmtInsertService->execute([
                        $order['id'],
                        $service['id'],
                        $service['name'],
                        $service['price'],
                        $weight,
                        $quantity,
                        $service['subtotal'],
                        date('Y-m-d H:i:s')
                    ]);
                }
            }
            
            setFlashMessage('success', 'Order created successfully.');
        }
        
        $db->commit();
        
        // If this is a new order and payment is completed, redirect to receipt page
        if (!$isEdit && $order['payment_status'] === 'Lunas') {
            header('Location: receipt.php?id=' . $order['id']);
            exit;
        }
        
        header('Location: orders.php');
        exit;
    } catch (PDOException $e) {
        $db->rollBack();
        setFlashMessage('danger', 'Error: ' . $e->getMessage());
    }
}

// Format tanggal untuk tampilan
function formatTanggal($dateString) {
    if (empty($dateString)) return '';
    
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $date = new DateTime($dateString);
    $tanggal = $date->format('d');
    $bulan = $bulan[(int)$date->format('m')];
    $tahun = $date->format('Y');
    $waktu = $date->format('H:i:s');
    
    return "$tanggal $bulan $tahun $waktu";
}

// Include header
$pageTitle = $isEdit ? "Edit Order #" . $order['id'] : "New Order";
include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Order</h1>
        <a href="orders.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    <?php displayFlashMessages(); ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $isEdit ? 'Edit' : 'New'; ?> Order Form</h6>
        </div>
        <div class="card-body">
            <form method="post" id="orderForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer</label>
                            <div class="input-group">
                                <select class="form-select" id="customer_id" name="customer_id" required <?php echo $isEdit ? 'disabled' : ''; ?>>
                                    <option value="">Select a customer</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['id']; ?>" <?php echo ($order['customer_id'] == $customer['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($customer['name'] . ' (' . $customer['phone'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!$isEdit): ?>
                                <a href="customer_form.php" class="btn btn-outline-secondary" target="_blank">
                                    <i class="fas fa-plus"></i> New
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php if ($isEdit): ?>
                            <input type="hidden" name="customer_id" value="<?php echo $order['customer_id']; ?>">
                            <?php endif; ?>
                        </div>
                        
                        <!-- Selected Services Table -->
                        <div class="card mb-3">
                            <div class="card-header bg-light py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-primary">Layanan yang Dipilih</h6>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                                        <i class="fas fa-plus"></i> Tambah Layanan
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Layanan</th>
                                                <th>Jumlah</th>
                                                <th>Harga</th>
                                                <th>Subtotal</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="selectedServicesTable">
                                            <!-- Selected services will be displayed here via JavaScript -->
                                            <tr id="noServicesRow">
                                                <td colspan="5" class="text-center py-3 text-muted">
                                                    <i class="fas fa-info-circle me-1"></i> Belum ada layanan yang dipilih
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="3" class="text-end">Total:</th>
                                                <th colspan="2" id="totalAmount">Rp 0</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden field to store the JSON of selected services -->
                        <input type="hidden" id="selectedServicesJson" name="selected_services" value="[]">
                        
                        <!-- Hidden fields for backward compatibility -->
                        <input type="hidden" id="service_type" name="service_type" value="">
                        <input type="hidden" id="weight" name="weight" value="1">
                        <input type="hidden" id="price_per_unit" name="price_per_unit" value="0">
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="total_amount" class="form-label">Total Amount (Rp)</label>
                            <input type="number" class="form-control" id="total_amount" name="total_amount" value="<?php echo htmlspecialchars($order['total_amount']); ?>" readonly>
                        </div>
                        
                        <!-- Hidden fields for payment -->
                        <input type="hidden" id="payment_method" name="payment_method" value="<?php echo $isEdit ? htmlspecialchars($order['payment_method']) : ''; ?>">
                        <input type="hidden" id="payment_amount" name="payment_amount" value="<?php echo $isEdit ? htmlspecialchars($order['payment_amount']) : '0'; ?>">
                        <input type="hidden" id="payment_change" name="payment_change" value="<?php echo $isEdit ? htmlspecialchars($order['payment_change']) : '0'; ?>">
                        <input type="hidden" id="payment_status" name="payment_status" value="<?php echo $isEdit ? htmlspecialchars($order['payment_status']) : 'Belum Dibayar'; ?>">
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Order Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Diterima" <?php echo ($order['status'] == 'Diterima') ? 'selected' : ''; ?>>Diterima</option>
                                <option value="Diproses" <?php echo ($order['status'] == 'Diproses') ? 'selected' : ''; ?>>Diproses</option>
                                <option value="Selesai" <?php echo ($order['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                <option value="Diambil" <?php echo ($order['status'] == 'Diambil') ? 'selected' : ''; ?>>Diambil</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($order['notes']); ?></textarea>
                        </div>
                        
                        <?php if ($isEdit): ?>
                        <div class="card mb-3 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Informasi Order</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Dibuat pada:</strong> <?php echo formatTanggal($order['created_at']); ?></p>
                                <p class="mb-1"><strong>Terakhir diupdate:</strong> <?php echo formatTanggal($order['updated_at']); ?></p>
                                <p class="mb-1"><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method'] ?: 'Not set'); ?></p>
                                <p class="mb-1"><strong>Payment Amount:</strong> Rp <?php echo number_format($order['payment_amount'], 0, ',', '.'); ?></p>
                                <p class="mb-1"><strong>Change:</strong> Rp <?php echo number_format($order['payment_change'], 0, ',', '.'); ?></p>
                                <p class="mb-0"><strong>Payment Status:</strong> 
                                    <span class="badge bg-<?php echo ($order['payment_status'] == 'Lunas') ? 'success' : 'danger'; ?>">
                                        <?php echo htmlspecialchars($order['payment_status']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update' : 'Save'; ?> Order
                    </button>
                    
                    <?php if ($isEdit): ?>
                    <a href="receipt.php?id=<?php echo $order['id']; ?>" class="btn btn-info">
                        <i class="fas fa-print"></i> Print Receipt
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const totalAmountInput = document.getElementById('total_amount');
    const paymentAmountInput = document.getElementById('payment_amount');
    const paymentChangeInput = document.getElementById('payment_change');
    const paymentStatusSelect = document.getElementById('payment_status');
    const selectedServicesTable = document.getElementById('selectedServicesTable');
    const selectedServicesJson = document.getElementById('selectedServicesJson');
    const noServicesRow = document.getElementById('noServicesRow');
    const totalAmountDisplay = document.getElementById('totalAmount');
    
    // Modal Elements
    const modalServiceSelect = document.getElementById('modal_service_id');
    const modalQuantity = document.getElementById('modal_quantity');
    const modalSubtotal = document.getElementById('modal_subtotal');
    const addServiceBtn = document.getElementById('addServiceBtn');
    
    // Store selected services
    let selectedServices = [];
    
    // Format currency
    function formatCurrency(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }
    
    // Update the quantity label and hint based on unit type
    function updateQuantityField() {
        const selectedOption = modalServiceSelect.options[modalServiceSelect.selectedIndex];
        if (!selectedOption || selectedOption.value === '') {
            return;
        }
        
        const unitType = selectedOption.getAttribute('data-unit-type') || 'kg';
        const quantityLabel = document.getElementById('quantity_label');
        const quantityHint = document.getElementById('quantity_hint');
        
        if (unitType === 'kg') {
            quantityLabel.textContent = 'Berat (kg)';
            quantityHint.textContent = 'Masukkan berat dalam kilogram';
            modalQuantity.min = '0.1';
            modalQuantity.step = '0.1';
        } else {
            quantityLabel.textContent = 'Jumlah Item';
            quantityHint.textContent = 'Masukkan jumlah item';
            modalQuantity.min = '1';
            modalQuantity.step = '1';
            modalQuantity.value = Math.max(1, Math.floor(modalQuantity.value));
        }
        
        calculateModalSubtotal();
    }
    
    // Calculate subtotal in modal
    function calculateModalSubtotal() {
        const selectedOption = modalServiceSelect.options[modalServiceSelect.selectedIndex];
        if (!selectedOption || selectedOption.value === '') {
            modalSubtotal.value = '0';
            return;
        }
        
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const quantity = parseFloat(modalQuantity.value) || 0;
        const subtotal = price * quantity;
        
        modalSubtotal.value = new Intl.NumberFormat('id-ID').format(subtotal);
    }
    
    // Calculate total from all selected services
    function calculateTotal() {
        let total = 0;
        selectedServices.forEach(service => {
            total += service.subtotal;
        });
        
        // Update displays
        totalAmountInput.value = total;
        totalAmountDisplay.textContent = formatCurrency(total);
        
        // Recalculate change whenever total changes
        calculateChange();
        
        // Update the JSON field
        selectedServicesJson.value = JSON.stringify(selectedServices);
        
        // For backward compatibility (first service becomes the main one)
        if (selectedServices.length > 0) {
            document.getElementById('service_type').value = selectedServices[0].name;
            document.getElementById('price_per_unit').value = selectedServices[0].price;
            if (selectedServices[0].quantityType === 'weight') {
                document.getElementById('weight').value = selectedServices[0].quantity;
            } else {
                document.getElementById('weight').value = 1; // Default for non-weight items
            }
        } else {
            document.getElementById('service_type').value = '';
            document.getElementById('price_per_unit').value = 0;
            document.getElementById('weight').value = 1;
        }
    }
    
    // Function to calculate change
    function calculateChange() {
        const totalAmount = parseFloat(totalAmountInput.value) || 0;
        const paymentAmount = parseFloat(paymentAmountInput.value) || 0;
        const change = paymentAmount - totalAmount;
        
        // Update change field
        paymentChangeInput.value = change > 0 ? change.toFixed(0) : 0;
        
        // Auto-update payment status based on payment amount
        if (paymentAmount >= totalAmount && totalAmount > 0) {
            paymentStatusSelect.value = 'Lunas';
        } else {
            paymentStatusSelect.value = 'Belum Dibayar';
        }
    }
    
    // Add a service to the order
    function addService() {
        const selectedOption = modalServiceSelect.options[modalServiceSelect.selectedIndex];
        if (!selectedOption || selectedOption.value === '' || !modalQuantity.value) {
            alert('Silahkan pilih layanan dan masukkan jumlah');
            return;
        }
        
        const serviceId = parseInt(selectedOption.value);
        const serviceName = selectedOption.getAttribute('data-name');
        const price = parseFloat(selectedOption.getAttribute('data-price'));
        const unitType = selectedOption.getAttribute('data-unit-type') || 'kg';
        const quantityType = unitType === 'kg' ? 'weight' : 'unit';
        const quantity = parseFloat(modalQuantity.value);
        const subtotal = price * quantity;
        
        // Check if service already exists
        const existingIndex = selectedServices.findIndex(s => s.id === serviceId);
        
        if (existingIndex !== -1) {
            const existingService = selectedServices[existingIndex];
            const quantityLabel = quantityType === 'weight' ? 'kg' : 'item';
            
            // Opsi konfirmasi dengan tombol
            const userAction = confirm(
                `⚠️ Layanan "${serviceName}" sudah ada dalam order!\n\n` +
                `Jumlah saat ini: ${existingService.quantity} ${quantityLabel}\n` +
                `Jumlah yang akan ditambahkan: ${quantity} ${quantityLabel}\n\n` +
                `Pilih OK untuk menambahkan ke jumlah yang sudah ada (total menjadi: ${existingService.quantity + quantity} ${quantityLabel}).\n` +
                `Pilih CANCEL untuk mengganti dengan nilai baru (menjadi: ${quantity} ${quantityLabel}).`
            );
            
            if (userAction) {
                // Update existing service by adding quantity
                selectedServices[existingIndex].quantity += quantity;
                selectedServices[existingIndex].subtotal = selectedServices[existingIndex].price * selectedServices[existingIndex].quantity;
            } else {
                // Replace with new quantity
                selectedServices[existingIndex].quantity = quantity;
                selectedServices[existingIndex].subtotal = price * quantity;
            }
        } else {
            // Add new service
            selectedServices.push({
                id: serviceId,
                name: serviceName,
                price: price,
                quantityType: quantityType,
                quantity: quantity,
                subtotal: subtotal
            });
        }
        
        // Update hidden field untuk menyimpan layanan yang dipilih
        document.getElementById('selectedServicesJson').value = JSON.stringify(selectedServices);
        
        // Update the UI
        renderSelectedServices();
        
        // Hide modal
        try {
            const modal = bootstrap.Modal.getInstance(document.getElementById('addServiceModal'));
            if (modal) {
                modal.hide();
            } else {
                // Fallback if modal instance not found
                $('#addServiceModal').modal('hide');
            }
        } catch (error) {
            // Another fallback
            $('#addServiceModal').modal('hide');
        }
        
        // Reset modal form
        modalServiceSelect.value = '';
        modalQuantity.value = 1;
        modalSubtotal.value = '';
    }
    
     // Remove a service from the order
    // Define removeService in global scope
    window.removeService = function(index) {
        // Konfirmasi penghapusan
        if (confirm('Apakah Anda yakin ingin menghapus layanan ini?')) {
            selectedServices.splice(index, 1);
            renderSelectedServices();
            // Update hidden field untuk menyimpan layanan yang dipilih
            document.getElementById('selectedServicesJson').value = JSON.stringify(selectedServices);
        }
        // Return false untuk mencegah link diikuti
        return false;
    };
    
    // Render the selected services table
    function renderSelectedServices() {
        // Kosongkan tabel terlebih dahulu
        selectedServicesTable.innerHTML = '';
        
        // Tambahkan baris "tidak ada layanan" jika array kosong
        if (selectedServices.length === 0) {
            selectedServicesTable.innerHTML = `
                <tr id="noServicesRow">
                    <td colspan="5" class="text-center py-3 text-muted">
                        <i class="fas fa-info-circle me-1"></i> Belum ada layanan yang dipilih
                    </td>
                </tr>
            `;
        } else {
            // Add rows for each service
            selectedServices.forEach((service, index) => {
                const quantityLabel = service.quantityType === 'weight' ? 'kg' : 'item';
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${service.name}</td>
                    <td>${service.quantity} ${quantityLabel}</td>
                    <td>${formatCurrency(service.price)}</td>
                    <td>${formatCurrency(service.subtotal)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="return removeService(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                
                selectedServicesTable.appendChild(row);
            });
        }
        
        // Calculate and update total
        calculateTotal();
    }
    
    // Add event listeners
    modalServiceSelect.addEventListener('change', updateQuantityField);
    modalQuantity.addEventListener('input', calculateModalSubtotal);
    addServiceBtn.addEventListener('click', addService);
    paymentAmountInput.addEventListener('input', calculateChange);
    
    // Initialize with existing data if editing
    <?php if ($isEdit && !empty($orderServices)): ?>
    // Convert PHP data to JavaScript
    selectedServices = <?php 
        $jsServices = [];
        foreach ($orderServices as $service) {
            $quantityType = $service['weight'] > 0 ? 'weight' : 'unit';
            $quantity = $quantityType === 'weight' ? $service['weight'] : $service['quantity'];
            
            $jsServices[] = [
                'id' => (int)$service['service_id'],
                'name' => $service['service_name'],
                'price' => (float)$service['price'],
                'quantityType' => $quantityType,
                'quantity' => (float)$quantity,
                'subtotal' => (float)$service['subtotal']
            ];
        }
        echo json_encode($jsServices);
    ?>;
    <?php endif; ?>
    
    // Initialize the UI
    renderSelectedServices();
});
</script>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addServiceModalLabel"><i class="fas fa-plus-circle me-2"></i> Tambah Layanan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addServiceForm">
                    <div class="mb-3">
                        <label for="modal_service_id" class="form-label">Layanan</label>
                        <select class="form-select" id="modal_service_id" required>
                            <option value="">Pilih layanan</option>
                            <?php foreach ($services as $service): ?>
                            <option value="<?php echo $service['id']; ?>" 
                                data-name="<?php echo htmlspecialchars($service['name']); ?>"
                                data-price="<?php echo $service['price']; ?>"
                                data-unit-type="<?php echo isset($service['unit_type']) ? $service['unit_type'] : 'kg'; ?>">
                                <?php echo htmlspecialchars($service['name']); ?> 
                                (Rp <?php echo number_format($service['price'], 0, ',', '.'); ?>/<?php echo isset($service['unit_type']) && $service['unit_type'] === 'item' ? 'item' : 'kg'; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="modal_quantity" class="form-label" id="quantity_label">Kuantitas (kg/item)</label>
                        <input type="number" class="form-control" id="modal_quantity" min="0.1" step="0.1" value="1" required>
                        <small class="text-muted" id="quantity_hint">Masukkan berat dalam kg</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="modal_subtotal" class="form-label">Subtotal</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="modal_subtotal" readonly>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="addServiceBtn">Tambah ke Order</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>