<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// ==================== IMPROVED TIME LOGIC ====================
// Set default timezone (sesuaikan dengan lokasi bisnis)
date_default_timezone_set('Asia/Jakarta');

$currentTime = time();
$currentHour = (int)date('H', $currentTime);
$currentDate = date('Y-m-d', $currentTime);
$isMidnight = ($currentHour >= 0 && $currentHour < 1); // Rentang 1 jam setelah midnight

// Use full datetime format for database queries
$todayStart = date('Y-m-d 00:00:00', $currentTime);
$todayEnd = date('Y-m-d 23:59:59', $currentTime);
$yesterdayStart = date('Y-m-d 00:00:00', strtotime('-1 day', $currentTime));
$yesterdayEnd = date('Y-m-d 23:59:59', strtotime('-1 day', $currentTime));

// ==================== FIXED DASHBOARD DATA FETCHING ====================
$stats = getDashboardStats();

// Get today's orders and revenue - FIXED QUERY
$stmtTodayOrders = $db->prepare("SELECT COUNT(*) as order_count, COALESCE(SUM(total_amount), 0) as total_revenue 
                               FROM orders 
                               WHERE DATE(created_at) = ?");
$stmtTodayOrders->execute([$currentDate]);
$todayData = $stmtTodayOrders->fetch(PDO::FETCH_ASSOC);

$todayStats = [
    'order_count' => (int)($todayData['order_count'] ?? 0),
    'total_revenue' => (float)($todayData['total_revenue'] ?? 0)
];

// Get yesterday's data - FIXED QUERY
$yesterdayDate = date('Y-m-d', strtotime('-1 day', $currentTime));
$stmtYesterday = $db->prepare("SELECT 
                              COUNT(*) as order_count,
                              COALESCE(SUM(total_amount), 0) as total_revenue 
                              FROM orders 
                              WHERE DATE(created_at) = ?");
$stmtYesterday->execute([$yesterdayDate]);
$yesterdayData = $stmtYesterday->fetch(PDO::FETCH_ASSOC);

// Perhitungan persentase yang lebih aman
$percentChange = 0;
$yesterdayRevenue = (float)($yesterdayData['total_revenue'] ?? 0);
$yesterdayOrderCount = (int)($yesterdayData['order_count'] ?? 0);

if ($yesterdayRevenue > 0 && $todayStats['total_revenue'] > 0) {
    $percentChange = (($todayStats['total_revenue'] - $yesterdayRevenue) / $yesterdayRevenue * 100);
} elseif ($yesterdayRevenue == 0 && $todayStats['total_revenue'] > 0) {
    // Handle case where yesterday was zero but today has revenue
    $percentChange = 100; // 100% increase
}

// Other stats
$customerCount = $stats['customer_count'];
$pendingCount = $stats['diproses_count'];
$completedCount = $stats['diambil_count'];
$unpaidCount = $stats['unpaid_count'];
$unpaidAmount = $stats['unpaid_amount'];

// Recent orders (last 5)
$stmtRecentOrders = $db->prepare("SELECT o.*, c.name as customer_name FROM orders o 
                                JOIN customers c ON o.customer_id = c.id 
                                ORDER BY o.created_at DESC LIMIT 5");
$stmtRecentOrders->execute();
$recentOrders = $stmtRecentOrders->fetchAll(PDO::FETCH_ASSOC);

// Weekly data for chart (last 7 days) - FIXED QUERY
$weeklyData = [];
$weeklyLabels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days", $currentTime));
    $weeklyLabels[] = date('D', strtotime("-$i days", $currentTime));
    
    $stmtDaily = $db->prepare("SELECT COALESCE(SUM(total_amount), 0) as daily_revenue 
                             FROM orders 
                             WHERE DATE(created_at) = ?");
    $stmtDaily->execute([$date]);
    $dailyRevenue = $stmtDaily->fetch(PDO::FETCH_ASSOC)['daily_revenue'];
    $weeklyData[] = $dailyRevenue ? (float)$dailyRevenue : 0;
}

// Include header
$pageTitle = "Dashboard";
include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Welcome Message & Quick Links -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-gray-800">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h4>
                            <p class="text-gray-600 mb-0">Here's what's happening with your laundry business today.</p>
                            <?php if ($isMidnight): ?>
                            <div class="alert alert-info mt-2 p-2 small">
                                <i class="fas fa-sync-alt me-1"></i> Daily stats have been reset for the new day.
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex align-items-center">
                            <!-- Digital Clock -->
                            <div class="me-4 text-center">
                                <div id="digitalClock" class="h4 mb-0 fw-bold text-primary"></div>
                            </div>
                            <a href="order_form.php" class="btn btn-primary me-2">
                                <i class="fas fa-plus-circle me-1"></i> New Order
                            </a>
                            <a href="customer_form.php" class="btn btn-outline-primary">
                                <i class="fas fa-user-plus me-1"></i> New Customer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row">
        <!-- Today's Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 dashboard-card animate-fade-in">
                <div class="card-body py-3 px-4">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                <?php echo $isMidnight ? 'New Day Revenue' : 'Today\'s Revenue'; ?>
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800">Rp <?php echo number_format($todayStats['total_revenue'], 0, ',', '.'); ?></div>
                            <div class="mt-1 text-gray-600 small">
                                <?php if ($isMidnight): ?>
                                    <span class="text-info"><i class="fas fa-sync-alt me-1"></i>Fresh start for new day</span>
                                <?php else: ?>
                                    <?php if ($yesterdayOrderCount > 0): ?>
                                        <?php if ($percentChange > 0): ?>
                                            <span class="text-success"><i class="fas fa-arrow-up me-1"></i><?php echo number_format(abs($percentChange), 1); ?>% from yesterday</span>
                                        <?php elseif ($percentChange < 0): ?>
                                            <span class="text-danger"><i class="fas fa-arrow-down me-1"></i><?php echo number_format(abs($percentChange), 1); ?>% from yesterday</span>
                                        <?php else: ?>
                                            <span class="text-gray-500"><i class="fas fa-equals me-1"></i>Same as yesterday</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-500"><i class="fas fa-calendar-plus me-1"></i>No orders yesterday</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="text-primary rounded-circle p-3 d-inline-flex shadow-sm" style="background-color: rgba(78, 115, 223, 0.1);">
                                <i class="fas fa-money-bill-wave fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($isMidnight): ?>
                <div class="card-footer bg-primary bg-opacity-10 py-2">
                    <small class="text-primary">
                        <i class="fas fa-info-circle me-1"></i> Reset at midnight
                    </small>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Today's Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 dashboard-card animate-fade-in" style="animation-delay: 0.2s">
                <div class="card-body py-3 px-4">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                <?php echo $isMidnight ? 'New Day Orders' : 'Today\'s Orders'; ?>
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?php echo $todayStats['order_count']; ?></div>
                            <div class="mt-1 text-gray-600 small">
                                <?php if ($isMidnight): ?>
                                    <span class="text-info"><i class="fas fa-sync-alt me-1"></i>Ready for new orders</span>
                                <?php elseif ($todayStats['order_count'] > 0): ?>
                                    <?php if ($yesterdayOrderCount > 0): ?>
                                        <?php $orderPercentChange = (($todayStats['order_count'] - $yesterdayOrderCount) / $yesterdayOrderCount * 100); ?>
                                        <?php if ($orderPercentChange > 0): ?>
                                            <span class="text-success"><i class="fas fa-arrow-up me-1"></i><?php echo number_format(abs($orderPercentChange), 1); ?>% from yesterday</span>
                                        <?php elseif ($orderPercentChange < 0): ?>
                                            <span class="text-warning"><i class="fas fa-arrow-down me-1"></i><?php echo number_format(abs($orderPercentChange), 1); ?>% from yesterday</span>
                                        <?php else: ?>
                                            <span class="text-gray-500"><i class="fas fa-equals me-1"></i>Same as yesterday</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-success"><i class="fas fa-check-circle me-1"></i><?php echo $todayStats['order_count']; ?> orders today</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-500"><i class="fas fa-info-circle me-1"></i>No orders yet today</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="text-success rounded-circle p-3 d-inline-flex shadow-sm" style="background-color: rgba(28, 200, 138, 0.1);">
                                <i class="fas fa-clipboard-list fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unpaid Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 dashboard-card animate-fade-in" style="animation-delay: 0.4s">
                <div class="card-body py-3 px-4">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Unpaid Orders</div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?php echo $unpaidCount; ?></div>
                            <div class="mt-1 text-gray-600 small">
                                <?php
                                if ($unpaidCount > 0) {
                                    echo '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>Rp ' . 
                                        number_format($unpaidAmount, 0, ',', '.') . ' pending</span>';
                                } else {
                                    echo '<span class="text-success"><i class="fas fa-check-circle me-1"></i>All orders paid</span>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="text-danger rounded-circle p-3 d-inline-flex shadow-sm" style="background-color: rgba(231, 74, 59, 0.1);">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 dashboard-card animate-fade-in" style="animation-delay: 0.6s">
                <div class="card-body py-3 px-4">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Total Customers</div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?php echo $customerCount; ?></div>
                            <div class="mt-1 text-gray-600 small">
                                <?php
                                $monthlyCustomers = $stats['monthly_new_customers'];
                                if ($monthlyCustomers > 0) {
                                    echo '<span class="text-info"><i class="fas fa-user-plus me-1"></i>' . $monthlyCustomers . ' new this month</span>';
                                } else {
                                    echo '<span class="text-gray-500"><i class="fas fa-info-circle me-1"></i>Loyal customer base</span>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="text-info rounded-circle p-3 d-inline-flex shadow-sm" style="background-color: rgba(54, 185, 204, 0.1);">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rest of the code remains unchanged -->
    <!-- Second row of stat cards -->
    <div class="row">
        <!-- Pending Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 dashboard-card animate-fade-in" style="animation-delay: 0.8s">
                <div class="card-body py-3 px-4">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Pending Orders</div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?php echo $pendingCount; ?></div>
                            <div class="mt-1 text-gray-600 small">
                                <?php 
                                if ($pendingCount > 0) {
                                    echo '<span class="text-warning"><i class="fas fa-spinner me-1"></i>' . $pendingCount . ' currently processing</span>';
                                } else {
                                    echo '<span class="text-gray-500"><i class="fas fa-check-circle me-1"></i>All caught up!</span>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="text-warning rounded-circle p-3 d-inline-flex shadow-sm" style="background-color: rgba(246, 194, 62, 0.1);">
                                <i class="fas fa-spinner fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 dashboard-card animate-fade-in" style="animation-delay: 1.0s">
                <div class="card-body py-3 px-4">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Completed Orders</div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?php echo $completedCount; ?></div>
                            <div class="mt-1 text-gray-600 small">
                                <?php 
                                $lastMonth = date('Y-m-d', strtotime('-1 month'));
                                $stmtLastMonthCompleted = $db->prepare("SELECT COUNT(*) as count FROM orders WHERE status = 'Diambil' AND created_at >= ?");
                                $stmtLastMonthCompleted->execute([$lastMonth]);
                                $lastMonthCompleted = $stmtLastMonthCompleted->fetch(PDO::FETCH_ASSOC)['count'];
                                
                                echo '<span class="text-success"><i class="fas fa-check-circle me-1"></i>' . $lastMonthCompleted . ' in the last month</span>';
                                ?>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="text-success rounded-circle p-3 d-inline-flex shadow-sm" style="background-color: rgba(28, 200, 138, 0.1);">
                                <i class="fas fa-check-double fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Additional space -->
        <div class="col-xl-6 col-md-6">
            <!-- Empty space for now, can be used for additional metrics later -->
        </div>
    </div>

    <div class="row">
        <!-- Weekly Revenue Chart -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">Weekly Revenue Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Revenue Options:</div>
                            <a class="dropdown-item" href="reports.php">View Detailed Reports</a>
                            <a class="dropdown-item" href="orders.php">View All Orders</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="window.print()">Print Chart</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="weeklyRevenueChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="me-2">
                            <i class="fas fa-circle text-primary"></i> Daily Revenue
                        </span>
                        <span class="me-2">
                            <i class="fas fa-circle text-success"></i> Target
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">Order Terbaru</h6>
                    <a href="orders.php" class="btn btn-sm btn-primary rounded-pill">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">No</th>
                                    <th>Pelanggan</th>
                                    <th>Status</th>
                                    <th class="pe-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td class="ps-3 fw-bold"><?php echo $no++; ?></td>
                                    <td>
                                        <a href="order_form.php?id=<?php echo $order['id']; ?>" class="text-primary">
                                            <?php echo htmlspecialchars($order['customer_name']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            switch ($order['status']) {
                                                case 'Diterima': echo 'primary'; break;
                                                case 'Diproses': echo 'warning'; break;
                                                case 'Selesai': echo 'success'; break;
                                                case 'Diambil': echo 'secondary'; break;
                                                default: echo 'info';
                                            }
                                        ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </td>
                                    <td class="pe-3 text-start fw-bold">
                                        Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($recentOrders)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        Belum ada order
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Quick Order Status Overview -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Order Status Overview</h6>
                </div>
                <div class="card-body">
                    <?php
                    $statusCounts = [
                        'Diterima' => $stats['diterima_count'],
                        'Diproses' => $stats['diproses_count'],
                        'Selesai' => $stats['selesai_count'],
                        'Diambil' => $stats['diambil_count']
                    ];
                    
                    $totalOrders = array_sum($statusCounts);
                    ?>
                    
                    <?php foreach ($statusCounts as $status => $count): ?>
                    <?php 
                        $percent = $totalOrders > 0 ? ($count / $totalOrders * 100) : 0;
                        
                        $colorClass = '';
                        switch ($status) {
                            case 'Diterima': $colorClass = 'primary'; break;
                            case 'Diproses': $colorClass = 'warning'; break;
                            case 'Selesai': $colorClass = 'success'; break;
                            case 'Diambil': $colorClass = 'secondary'; break;
                            default: $colorClass = 'info';
                        }
                    ?>
                    <h6 class="fw-bold text-<?php echo $colorClass; ?> d-flex justify-content-between">
                        <span><?php echo $status; ?></span>
                        <span><?php echo $count; ?> orders (<?php echo number_format($percent, 1); ?>%)</span>
                    </h6>
                    <div class="progress mb-3" style="height: 10px">
                        <div class="progress-bar bg-<?php echo $colorClass; ?>" role="progressbar" 
                             style="width: <?php echo $percent; ?>%" 
                             aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Enhanced Digital Clock Function
function updateClock() {
    const now = new Date();
    let hours = now.getHours();
    const minutes = now.getMinutes();
    const seconds = now.getSeconds();
    let ampm = hours >= 12 ? 'PM' : 'AM';
    
    // Convert to 12-hour format
    hours = hours % 12;
    hours = hours ? hours : 12;
    
    // Add leading zeros
    hours = hours < 10 ? '0' + hours : hours;
    const mins = minutes < 10 ? '0' + minutes : minutes;
    const secs = seconds < 10 ? '0' + seconds : seconds;
    
    // Display the time
    document.getElementById('digitalClock').innerHTML = hours + ':' + mins + ':' + secs + ' ' + ampm;
    
    setTimeout(updateClock, 1000);
}

// Initialize the clock when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    updateClock();
    
    // Weekly revenue chart initialization
    const revenueData = <?php echo json_encode($weeklyData); ?>;
    const averageRevenue = revenueData.reduce((a, b) => a + parseFloat(b), 0) / revenueData.length;
    const targetData = revenueData.map(() => averageRevenue * 1.2);

    const ctx = document.getElementById('weeklyRevenueChart').getContext('2d');
    const weeklyRevenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($weeklyLabels); ?>,
            datasets: [
                {
                    label: 'Daily Revenue',
                    data: revenueData,
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 4,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: '#FFFFFF',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Target Revenue',
                    data: targetData,
                    backgroundColor: 'transparent',
                    borderColor: 'rgba(28, 200, 138, 0.6)',
                    borderWidth: 2,
                    borderDash: [6, 3],
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    pointHoverBackgroundColor: 'rgba(28, 200, 138, 1)',
                    tension: 0.4,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgb(255, 255, 255)',
                    titleColor: '#5a5c69',
                    titleFont: {
                        family: 'Nunito',
                        size: 14,
                        weight: 'bold'
                    },
                    bodyColor: '#5a5c69',
                    bodyFont: {
                        family: 'Nunito',
                        size: 14
                    },
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    caretPadding: 10,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.raw;
                            return label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            family: 'Nunito',
                            size: 12
                        },
                        color: '#858796'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: "rgba(234, 236, 244, 1)",
                        borderDash: [2],
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            family: 'Nunito',
                            size: 12
                        },
                        color: '#858796',
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        },
                        maxTicksLimit: 5,
                        padding: 10
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>