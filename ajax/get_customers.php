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

try {
    // Get all customers with order counts
    $stmt = $db->prepare("
        SELECT c.*, 
               (SELECT COUNT(*) FROM orders o WHERE o.customer_id = c.id) AS total_orders
        FROM customers c
        ORDER BY c.created_at DESC
    ");
    
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return customers as JSON
    echo json_encode($customers);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
