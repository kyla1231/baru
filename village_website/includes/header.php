<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Website resmi Desa Maju Bersama - Informasi terkini tentang desa, pelayanan, dan kegiatan masyarakat'; ?>">
    <meta name="keywords" content="desa, pemerintahan desa, pelayanan desa, berita desa, kegiatan desa">
    <meta name="author" content="<?php echo SITE_NAME; ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/assets/images/logo.png">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-top">
                <div class="logo">
                    <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="Logo Desa" onerror="this.style.display='none'">
                    <div class="logo-text">
                        <h1><?php echo SITE_NAME; ?></h1>
                        <p>Kabupaten Contoh, Provinsi Contoh</p>
                    </div>
                </div>
                <div class="contact-info">
                    <div><i class="fas fa-phone"></i> +62 123 456 7890</div>
                    <div><i class="fas fa-envelope"></i> <?php echo ADMIN_EMAIL; ?></div>
                    <div><i class="fas fa-map-marker-alt"></i> Jl. Desa Maju No. 123</div>
                </div>
            </div>
            
            <nav class="navbar">
                <ul class="nav-menu">
                    <li><a href="<?php echo SITE_URL; ?>/index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Beranda
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                        <i class="fas fa-info-circle"></i> Profil Desa
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/government.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'government.php' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i> Pemerintahan
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/services.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-list"></i> Pelayanan
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/news.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : ''; ?>">
                        <i class="fas fa-newspaper"></i> Berita
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/gallery.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : ''; ?>">
                        <i class="fas fa-images"></i> Galeri
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/contact.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">
                        <i class="fas fa-envelope"></i> Kontak
                    </a></li>
                </ul>
            </nav>
        </div>
    </header>