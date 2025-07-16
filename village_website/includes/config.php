<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'village_website');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site configuration
define('SITE_NAME', 'Desa Maju Bersama');
define('SITE_URL', 'http://localhost/village_website');
define('ADMIN_EMAIL', 'admin@desamajubersama.id');

// Create database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If database doesn't exist, create it
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $pdo->exec("USE " . DB_NAME);
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Session management
session_start();

// Timezone
date_default_timezone_set('Asia/Jakarta');
?>