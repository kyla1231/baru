<?php
// Tampilkan semua error (matikan di production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi ke database MySQL di InfinityFree
try {
    $dbHost = 'sql200.infinityfree.com';
    $dbName = 'if0_38786264_laundry_db';
    $dbUser = 'if0_38786264';
    $dbPass = 'nuxJKJ8x5wKt'; // ganti ini

    $db = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);

    // Set mode error jadi Exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
