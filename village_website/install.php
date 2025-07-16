<?php
// Database Installation Script for Village Website
require_once 'includes/config.php';

// Check if installation is already done
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    echo '<h2>Installation Successful!</h2>';
    echo '<p>Database tables have been created successfully.</p>';
    echo '<p><a href="index.php">Go to Website</a> | <a href="admin/login.php">Admin Panel</a></p>';
    exit;
}

try {
    // Create tables
    $tables = [
        // News table
        "CREATE TABLE IF NOT EXISTS news (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            excerpt TEXT,
            content LONGTEXT,
            image VARCHAR(255),
            author VARCHAR(100),
            status ENUM('draft', 'published') DEFAULT 'draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        // Gallery table
        "CREATE TABLE IF NOT EXISTS gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            image VARCHAR(255) NOT NULL,
            category VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Government officials table
        "CREATE TABLE IF NOT EXISTS officials (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            position VARCHAR(255) NOT NULL,
            photo VARCHAR(255),
            phone VARCHAR(20),
            email VARCHAR(255),
            description TEXT,
            order_num INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Contact messages table
        "CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            subject VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Admin users table
        "CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(255),
            email VARCHAR(255),
            role ENUM('admin', 'editor') DEFAULT 'editor',
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Pages table for dynamic content
        "CREATE TABLE IF NOT EXISTS pages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            content LONGTEXT,
            meta_description TEXT,
            status ENUM('draft', 'published') DEFAULT 'draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        // Settings table
        "CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) NOT NULL UNIQUE,
            setting_value TEXT,
            description TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )"
    ];
    
    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
    
    // Insert default admin user (password: admin123)
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO admin_users (username, password, full_name, email, role) 
                VALUES ('admin', '$admin_password', 'Administrator', 'admin@desamajubersama.id', 'admin')");
    
    // Insert default settings
    $default_settings = [
        ['site_name', 'Desa Maju Bersama', 'Nama website desa'],
        ['site_description', 'Website resmi Desa Maju Bersama', 'Deskripsi website'],
        ['contact_phone', '+62 123 456 7890', 'Nomor telepon kontak'],
        ['contact_email', 'admin@desamajubersama.id', 'Email kontak'],
        ['contact_address', 'Jl. Desa Maju No. 123, Kabupaten Contoh', 'Alamat desa'],
        ['facebook_url', '', 'URL Facebook'],
        ['instagram_url', '', 'URL Instagram'],
        ['youtube_url', '', 'URL YouTube']
    ];
    
    foreach ($default_settings as $setting) {
        $pdo->exec("INSERT IGNORE INTO settings (setting_key, setting_value, description) 
                    VALUES ('{$setting[0]}', '{$setting[1]}', '{$setting[2]}')");
    }
    
    // Insert sample officials
    $officials = [
        ['Bapak Suyanto, S.AP', 'Kepala Desa', '', '+62 123 456 7891', 'kepala.desa@desamajubersama.id', 'Kepala Desa periode 2019-2025', 1],
        ['Ibu Siti Aminah, S.Sos', 'Sekretaris Desa', '', '+62 123 456 7892', 'sekretaris@desamajubersama.id', 'Sekretaris Desa', 2],
        ['Bapak Ahmad Fauzi, S.E', 'Bendahara Desa', '', '+62 123 456 7895', 'bendahara@desamajubersama.id', 'Bendahara Desa', 3]
    ];
    
    foreach ($officials as $official) {
        $pdo->exec("INSERT IGNORE INTO officials (name, position, photo, phone, email, description, order_num) 
                    VALUES ('{$official[0]}', '{$official[1]}', '{$official[2]}', '{$official[3]}', '{$official[4]}', '{$official[5]}', {$official[6]})");
    }
    
    // Insert sample news
    $sample_news = [
        [
            'title' => 'Pembangunan Jalan Desa Fase 2 Dimulai',
            'slug' => 'pembangunan-jalan-desa-fase-2-dimulai',
            'excerpt' => 'Pemerintah desa memulai proyek pembangunan jalan desa fase 2 yang akan meningkatkan akses transportasi warga.',
            'content' => '<p>Desa Maju Bersama kembali melanjutkan program pembangunan infrastruktur dengan memulai proyek pembangunan jalan desa fase 2. Proyek ini merupakan kelanjutan dari fase 1 yang telah berhasil diselesaikan tahun lalu.</p><p>Pembangunan jalan fase 2 ini akan menghubungkan dusun-dusun yang sebelumnya sulit diakses, terutama pada musim hujan. Total panjang jalan yang akan dibangun mencapai 2.5 kilometer dengan lebar 4 meter.</p><p>Kepala Desa, Bapak Suyanto, menyatakan bahwa proyek ini dibiayai dari Dana Desa dan bantuan Pemerintah Kabupaten dengan total anggaran Rp 2.8 miliar. Pembangunan direncanakan selesai dalam 6 bulan.</p>',
            'author' => 'Admin Desa',
            'status' => 'published'
        ],
        [
            'title' => 'Program Bantuan UMKM Untuk Warga Desa',
            'slug' => 'program-bantuan-umkm-untuk-warga-desa',
            'excerpt' => 'Launching program bantuan modal usaha untuk mendukung UMKM warga desa dalam mengembangkan ekonomi lokal.',
            'content' => '<p>Pemerintah Desa Maju Bersama meluncurkan program bantuan UMKM (Usaha Mikro Kecil Menengah) untuk mendorong perekonomian masyarakat desa. Program ini memberikan bantuan modal usaha kepada warga yang memiliki usaha atau berencana memulai usaha.</p><p>Bantuan modal berkisar antara Rp 1 juta hingga Rp 5 juta per usaha, disertai dengan pendampingan dan pelatihan manajemen usaha. Target program ini adalah 50 UMKM yang akan dibina selama 1 tahun.</p><p>Pendaftaran dibuka mulai tanggal 1 Februari 2024 di Kantor Desa dengan membawa proposal usaha sederhana dan rekomendasi dari RT/RW setempat.</p>',
            'author' => 'Admin Desa',
            'status' => 'published'
        ]
    ];
    
    foreach ($sample_news as $news) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO news (title, slug, excerpt, content, author, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$news['title'], $news['slug'], $news['excerpt'], $news['content'], $news['author'], $news['status']]);
    }
    
    echo '<div style="max-width: 600px; margin: 50px auto; padding: 20px; font-family: Arial, sans-serif;">';
    echo '<h2 style="color: green;">✓ Installation Successful!</h2>';
    echo '<p>Database tables have been created successfully with sample data.</p>';
    echo '<h3>Default Admin Login:</h3>';
    echo '<p><strong>Username:</strong> admin<br><strong>Password:</strong> admin123</p>';
    echo '<p style="background: #f0f0f0; padding: 15px; border-radius: 5px;"><strong>Important:</strong> Please change the default admin password after first login for security reasons.</p>';
    echo '<div style="margin-top: 30px;">';
    echo '<a href="index.php" style="background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;">View Website</a>';
    echo '<a href="admin/login.php" style="background: #27ae60; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Admin Panel</a>';
    echo '</div>';
    echo '</div>';
    
} catch (PDOException $e) {
    echo '<div style="max-width: 600px; margin: 50px auto; padding: 20px; font-family: Arial, sans-serif;">';
    echo '<h2 style="color: red;">Installation Failed!</h2>';
    echo '<p>Error: ' . $e->getMessage() . '</p>';
    echo '<p>Please check your database configuration in includes/config.php</p>';
    echo '</div>';
}
?>