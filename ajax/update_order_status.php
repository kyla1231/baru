<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Set headers
header('Content-Type: application/json');

// Check if required fields are provided
if (!isset($_POST['order_id']) || empty($_POST['order_id']) || 
    !isset($_POST['status']) || empty($_POST['status'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Order ID and status are required'
    ]);
    exit;
}

$order_id = (int)$_POST['order_id'];
$status = $_POST['status'];

// Validate status
$validStatuses = ['Diterima', 'Diproses', 'Selesai', 'Diambil'];
if (!in_array($status, $validStatuses)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid status value'
    ]);
    exit;
}

try {
    // Update order status
    $result = updateOrderStatus($db, $order_id, $status);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update order status'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
