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
    // Get all orders with customer names and total from order_services if available
    $stmt = $db->prepare("
        SELECT o.*, c.name AS customer_name,
        (SELECT SUM(subtotal) FROM order_services WHERE order_id = o.id) as service_total
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        ORDER BY o.created_at DESC
    ");
    
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process orders to add row_number instead of ID
    $counter = 1;
    foreach ($orders as &$order) {
        $order['row_number'] = $counter++;
        // Use service_total if it exists
        if (!empty($order['service_total'])) {
            $order['total_amount'] = $order['service_total'];
        }
    }
    
    // Return orders as JSON
    echo json_encode($orders);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
