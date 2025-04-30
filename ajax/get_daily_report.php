<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Set headers
header('Content-Type: application/json');

// Check if date is provided
if (!isset($_GET['date']) || empty($_GET['date'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Date is required'
    ]);
    exit;
}

$date = $_GET['date'];

try {
    // Get orders for the specified date
    $stmt = $db->prepare("
        SELECT o.*, c.name as customer_name
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        WHERE DATE(o.created_at) = ?
        ORDER BY o.created_at DESC
    ");
    
    $stmt->execute([$date]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return orders as JSON
    echo json_encode([
        'success' => true,
        'data' => $orders
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
