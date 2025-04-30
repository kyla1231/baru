<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']) && isset($_SESSION['role']);
}

/**
 * Check if the logged-in user is an admin
 * 
 * @return bool True if user is an admin, false otherwise
 */
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

/**
 * Check if the logged-in user is a cashier
 * 
 * @return bool True if user is a cashier, false otherwise
 */
function isCashier() {
    return isLoggedIn() && $_SESSION['role'] === 'cashier';
}

/**
 * Check if the user has permission to access admin features
 * Redirects to dashboard if user doesn't have permission
 * 
 * @return void
 */
function requireAdmin() {
    if (!isAdmin()) {
        // Set flash message
        if (function_exists('setFlashMessage')) {
            setFlashMessage('danger', 'You do not have permission to access this page.');
        }
        
        // Redirect to dashboard
        header('Location: dashboard.php');
        exit;
    }
}

/**
 * Logout the current user
 * 
 * @return void
 */
function logoutUser() {
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
}
