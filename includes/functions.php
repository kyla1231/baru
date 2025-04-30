<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Set a flash message to display on the next page load
 * 
 * @param string $type The type of message (success, danger, warning, info)
 * @param string $message The message text
 * @return void
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Display flash messages if they exist and then clear them
 * 
 * @return void
 */
function displayFlashMessages() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_message']['type'];
        $message = $_SESSION['flash_message']['message'];
        
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
        echo $message;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        
        // Clear the flash message
        unset($_SESSION['flash_message']);
    }
}

/**
 * Check if a string contains a search term (case-insensitive)
 * 
 * @param string $haystack The string to search in
 * @param string $needle The term to search for
 * @return bool True if the needle is found, false otherwise
 */
function containsString($haystack, $needle) {
    return stripos($haystack, $needle) !== false;
}

/**
 * Format a date into a readable format
 * 
 * @param string $date The date string to format
 * @param string $format The format to use (default: 'd M Y H:i')
 * @return string The formatted date
 */
function formatDate($date, $format = 'd M Y H:i') {
    if (empty($date)) return '';
    $dateObj = date_create($date);
    return date_format($dateObj, $format);
}

/**
 * Get a single setting value from the database
 * 
 * @param PDO $db The database connection
 * @param string $key The setting key
 * @param mixed $default Default value if setting is not found
 * @return mixed The setting value or default
 */
function getSetting($db, $key, $default = '') {
    try {
        $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        return $default;
    }
}

/**
 * Get all settings from the database
 * 
 * @param PDO $db The database connection
 * @return array Associative array of settings
 */
function getAllSettings($db) {
    $settings = [];
    try {
        $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($result as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $settings;
    } catch (PDOException $e) {
        return $settings;
    }
}

/**
 * Format a number as currency (with symbol from settings)
 * 
 * @param float $amount The amount to format
 * @return string The formatted currency
 */
function formatCurrency($amount) {
    global $db;
    
    // Try to get currency symbol from settings, default to 'Rp'
    $symbol = 'Rp';
    
    try {
        $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        if ($stmt) {
            $stmt->execute(['currency_symbol']);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $symbol = $result['setting_value'];
            }
        }
    } catch (Exception $e) {
        // Use default if there's an error
    }
    
    // Format number to currency format (with the selected symbol)
    return $symbol . ' ' . number_format($amount, 0, ',', '.');
}

/**
 * Generate a badge HTML for order status
 * 
 * @param string $status The order status
 * @return string HTML for the status badge
 */
function getStatusBadge($status) {
    $badgeClass = '';
    switch ($status) {
        case 'Diterima':
            $badgeClass = 'primary';
            break;
        case 'Diproses':
            $badgeClass = 'warning';
            break;
        case 'Selesai':
            $badgeClass = 'success';
            break;
        case 'Diambil':
            $badgeClass = 'secondary';
            break;
        default:
            $badgeClass = 'info';
    }
    
    return '<span class="badge bg-' . $badgeClass . '">' . htmlspecialchars($status) . '</span>';
}

/**
 * Sanitize input to prevent XSS attacks
 * 
 * @param string $input The input to sanitize
 * @return string The sanitized input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Get the daily summary data for the dashboard
 * 
 * @param PDO $db The database connection
 * @param string $date The date to get summary for (default: today)
 * @return array The summary data
 */
function getDailySummary($db, $date = null) {
    if ($date === null) {
        $date = date('Y-m-d');
    }
    
    // Get daily stats
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as order_count,
            SUM(total_amount) as total_revenue,
            AVG(total_amount) as avg_order_value
        FROM orders 
        WHERE DATE(created_at) = ?
    ");
    $stmt->execute([$date]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get customer details including order history
 * 
 * @param PDO $db The database connection
 * @param int $customerId The customer ID
 * @return array The customer data with order history
 */
function getCustomerDetails($db, $customerId) {
    // Get customer info
    $stmt = $db->prepare("
        SELECT * FROM customers WHERE id = ?
    ");
    $stmt->execute([$customerId]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$customer) {
        return null;
    }
    
    // Get order history
    $stmt = $db->prepare("
        SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC
    ");
    $stmt->execute([$customerId]);
    $customer['orders'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total spent
    $customer['total_spent'] = 0;
    foreach ($customer['orders'] as $order) {
        $customer['total_spent'] += $order['total_amount'];
    }
    
    return $customer;
}

/**
 * Get order details
 * 
 * @param PDO $db The database connection
 * @param int $orderId The order ID
 * @return array|null The order data or null if not found
 */
function getOrderDetails($db, $orderId) {
    $stmt = $db->prepare("
        SELECT o.*, c.name as customer_name, c.phone as customer_phone
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Update order status
 * 
 * @param PDO $db The database connection
 * @param int $orderId The order ID
 * @param string $status The new status
 * @return bool True if successful, false otherwise
 */
function updateOrderStatus($db, $orderId, $status) {
    try {
        $stmt = $db->prepare("
            UPDATE orders SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?
        ");
        $stmt->execute([$status, $orderId]);
        
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Get financial report data for a specific period
 * 
 * @param PDO $db The database connection
 * @param string $startDate Start date (Y-m-d)
 * @param string $endDate End date (Y-m-d)
 * @param string $groupBy How to group results (daily, weekly, monthly)
 * @return array The report data
 */
function getFinancialReport($db, $startDate, $endDate, $groupBy = 'daily') {
    $groupFormat = '';
    $dateFormat = '';
    
    switch ($groupBy) {
        case 'weekly':
            $groupFormat = "WEEK(created_at, 1)"; // MySQL WEEK() function to group by week
            $dateFormat = "CONCAT('Week ', WEEK(created_at, 1), ', ', YEAR(created_at))"; // Format for week display
            break;
        case 'monthly':
            $groupFormat = "DATE_FORMAT(created_at, '%Y-%m')"; // MySQL DATE_FORMAT() to group by month
            $dateFormat = "DATE_FORMAT(created_at, '%m/%Y')"; // Format for month display
            break;
        case 'daily':
        default:
            $groupFormat = "DATE(created_at)"; // Group by day using DATE()
            $dateFormat = "DATE(created_at)"; // Display as date
            break;
    }
    
    $query = "
        SELECT 
            $dateFormat as date_label,
            $groupFormat as date_group,
            COUNT(*) as order_count,
            SUM(total_amount) as total_revenue,
            AVG(total_amount) as avg_order_value
        FROM orders 
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY date_group
        ORDER BY date_group ASC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$startDate, $endDate]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Get sales by service type for a specific period
 * 
 * @param PDO $db The database connection
 * @param string $startDate Start date (Y-m-d)
 * @param string $endDate End date (Y-m-d)
 * @return array The sales data by service type
 */
function getSalesByServiceType($db, $startDate, $endDate) {
    $query = "
        SELECT 
            service_type,
            COUNT(*) as order_count,
            SUM(total_amount) as total_revenue
        FROM orders 
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY service_type
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$startDate, $endDate]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get dashboard statistics data for dashboard cards
 * 
 * @return array Associative array of dashboard statistics
 */
function getDashboardStats() {
    global $db;
    
    $today = date('Y-m-d');
    $stats = [];
    
    // Today's transactions
    $stmtToday = $db->prepare("SELECT COUNT(*) as order_count, SUM(total_amount) as total_revenue FROM orders WHERE DATE(created_at) = ?");
    $stmtToday->execute([$today]);
    $todayStats = $stmtToday->fetch(PDO::FETCH_ASSOC);
    $stats['today_revenue'] = $todayStats['total_revenue'] ?? 0;
    $stats['today_orders'] = $todayStats['order_count'] ?? 0;
    
    // Yesterday's transactions (for comparison)
    $yesterdayDate = date('Y-m-d', strtotime('-1 day'));
    $stmtYesterday = $db->prepare("SELECT SUM(total_amount) as total_revenue FROM orders WHERE DATE(created_at) = ?");
    $stmtYesterday->execute([$yesterdayDate]);
    $yesterdayRevenue = $stmtYesterday->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;
    $stats['yesterday_revenue'] = $yesterdayRevenue;
    
    // Total customers
    $stmtCustomers = $db->query("SELECT COUNT(*) as customer_count FROM customers");
    $stats['customer_count'] = $stmtCustomers->fetch(PDO::FETCH_ASSOC)['customer_count'] ?? 0;
    
    // Customers added this month
    $firstDayMonth = date('Y-m-01');
    $stmtMonthlyCustomers = $db->prepare("SELECT COUNT(*) as count FROM customers WHERE DATE(created_at) >= ?");
    $stmtMonthlyCustomers->execute([$firstDayMonth]);
    $stats['monthly_new_customers'] = $stmtMonthlyCustomers->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    // Orders by status
    $statuses = ['Diterima', 'Diproses', 'Selesai', 'Diambil'];
    foreach ($statuses as $status) {
        $stmtStatus = $db->prepare("SELECT COUNT(*) as count FROM orders WHERE status = ?");
        $stmtStatus->execute([$status]);
        $stats[strtolower($status) . '_count'] = $stmtStatus->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    }
    
    // Unpaid orders
    $stmtUnpaid = $db->prepare("SELECT COUNT(*) as unpaid_count FROM orders WHERE payment_status = 'Belum Dibayar'");
    $stmtUnpaid->execute();
    $stats['unpaid_count'] = $stmtUnpaid->fetch(PDO::FETCH_ASSOC)['unpaid_count'] ?? 0;
    
    // Total unpaid amount
    $stmtUnpaidAmount = $db->prepare("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'Belum Dibayar'");
    $stmtUnpaidAmount->execute();
    $stats['unpaid_amount'] = $stmtUnpaidAmount->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Total paid amount (all time)
    $stmtPaidAmount = $db->prepare("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'Lunas'");
    $stmtPaidAmount->execute();
    $stats['total_paid_amount'] = $stmtPaidAmount->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Monthly data
    $firstDayMonth = date('Y-m-01');
    $stmtMonthly = $db->prepare("SELECT COUNT(*) as order_count, SUM(total_amount) as total_revenue FROM orders WHERE DATE(created_at) >= ?");
    $stmtMonthly->execute([$firstDayMonth]);
    $monthlyStats = $stmtMonthly->fetch(PDO::FETCH_ASSOC);
    $stats['monthly_revenue'] = $monthlyStats['total_revenue'] ?? 0;
    $stats['monthly_orders'] = $monthlyStats['order_count'] ?? 0;
    
    return $stats;
}
