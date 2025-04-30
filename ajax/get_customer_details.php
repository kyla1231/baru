<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Set headers
header('Content-Type: application/json');

// Check if customer ID is provided
if (!isset($_POST['customer_id']) || empty($_POST['customer_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Customer ID is required'
    ]);
    exit;
}

$customer_id = (int)$_POST['customer_id'];

try {
    // Get customer details including order history
    $customer = getCustomerDetails($db, $customer_id);
    
    if (!$customer) {
        echo json_encode([
            'success' => false,
            'message' => 'Customer not found'
        ]);
        exit;
    }
    
    // Return customer details as JSON
    echo json_encode([
        'success' => true,
        'data' => $customer
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
