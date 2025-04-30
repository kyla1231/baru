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

// Get parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'daily';

try {
    // Get financial report data
    $reportData = getFinancialReport($db, $start_date, $end_date, $report_type);
    
    // Get service type distribution
    $serviceData = getSalesByServiceType($db, $start_date, $end_date);
    
    // Calculate summary data
    $totalOrders = 0;
    $totalRevenue = 0;
    $avgOrderValue = 0;
    
    foreach ($reportData as $row) {
        $totalOrders += $row['order_count'];
        $totalRevenue += $row['total_revenue'];
    }
    
    if ($totalOrders > 0) {
        $avgOrderValue = $totalRevenue / $totalOrders;
    }
    
    // Prepare chart data
    $chartLabels = [];
    $chartData = [];
    
    foreach ($reportData as $row) {
        $chartLabels[] = $row['date_label'];
        $chartData[] = $row['total_revenue'];
    }
    
    // Prepare response
    $response = [
        'success' => true,
        'report_data' => $reportData,
        'service_data' => $serviceData,
        'summary' => [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'avg_order_value' => $avgOrderValue
        ],
        'chart_data' => [
            'labels' => $chartLabels,
            'values' => $chartData
        ]
    ];
    
    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
