<?php
require_once 'includes/config.php';

$page_title = 'Beranda';
$page_description = 'Website resmi Desa Maju Bersama - Informasi terkini tentang desa, pelayanan, dan kegiatan masyarakat';

// Fetch latest news (simulated data for now)
$latest_news = [
    [
        'id' => 1,
        'title' => 'Pembangunan Jalan Desa Fase 2 Dimulai',
        'excerpt' => 'Pemerintah desa memulai proyek pembangunan jalan desa fase 2 yang akan meningkatkan akses transportasi warga.',
        'date' => '2024-01-15',
        'image' => 'assets/images/news1.jpg'
    ],
    [
        'id' => 2,
        'title' => 'Program Bantuan UMKM Untuk Warga Desa',
        'excerpt' => 'Launching program bantuan modal usaha untuk mendukung UMKM warga desa dalam mengembangkan ekonomi lokal.',
        'date' => '2024-01-12',
        'image' => 'assets/images/news2.jpg'
    ],
    [
        'id' => 3,
        'title' => 'Gotong Royong Pembersihan Lingkungan',
        'excerpt' => 'Kegiatan gotong royong membersihkan lingkungan desa dilaksanakan setiap hari Minggu melibatkan seluruh warga.',
        'date' => '2024-01-10',
        'image' => 'assets/images/news3.jpg'
    ]
];

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h2>Selamat Datang di <?php echo SITE_NAME; ?></h2>
            <p>Desa yang maju, mandiri, dan sejahtera dengan semangat gotong royong</p>
            <a href="pages/profile.php" class="btn">Pelajari Lebih Lanjut</a>
        </div>
    </div>
</section>

<!-- Main Content -->
<main class="main-content">
    <div class="container">
        
        <!-- Statistics Section -->
        <section class="stats">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-item">
                        <h3>2547</h3>
                        <p>Total Penduduk</p>
                    </div>
                    <div class="stat-item">
                        <h3>867</h3>
                        <p>Kepala Keluarga</p>
                    </div>
                    <div class="stat-item">
                        <h3>12</h3>
                        <p>Dusun</p>
                    </div>
                    <div class="stat-item">
                        <h3>45</h3>
                        <p>RT/RW</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="section">
            <div class="section-title">
                <h2>Tentang Desa Kami</h2>
                <p>Mengenal lebih dalam tentang sejarah, visi misi, dan potensi Desa Maju Bersama</p>
            </div>
            <div class="grid grid-2">
                <div class="card">
                    <img src="assets/images/village-overview.jpg" alt="Gambaran Desa" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkdhbWJhcmFuIERlc2E8L3RleHQ+PC9zdmc+'">
                    <h3>Profil Desa</h3>
                    <p>Desa Maju Bersama terletak di daerah yang strategis dengan potensi alam yang melimpah. Kami berkomitmen untuk terus berkembang dengan tetap mempertahankan nilai-nilai budaya lokal.</p>
                    <a href="pages/profile.php" class="btn btn-primary">Selengkapnya</a>
                </div>
                <div class="card">
                    <img src="assets/images/government.jpg" alt="Pemerintahan Desa" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPlBlbWVyaW50YWhhbjwvdGV4dD48L3N2Zz4='" >
                    <h3>Pemerintahan Desa</h3>
                    <p>Struktur pemerintahan desa yang transparan dan akuntabel dengan kepala desa dan perangkat desa yang berpengalaman dan berdedikasi tinggi untuk melayani masyarakat.</p>
                    <a href="pages/government.php" class="btn btn-primary">Lihat Struktur</a>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="section">
            <div class="section-title">
                <h2>Pelayanan Desa</h2>
                <p>Berbagai layanan administrasi dan kemasyarakatan yang tersedia untuk warga desa</p>
            </div>
            <div class="grid grid-3">
                <div class="card">
                    <i class="fas fa-id-card" style="font-size: 3rem; color: #3498db; margin-bottom: 1rem;"></i>
                    <h3>Administrasi Kependudukan</h3>
                    <p>Pelayanan pembuatan dan pembaruan dokumen kependudukan seperti KTP, KK, dan surat keterangan lainnya.</p>
                </div>
                <div class="card">
                    <i class="fas fa-file-alt" style="font-size: 3rem; color: #27ae60; margin-bottom: 1rem;"></i>
                    <h3>Surat Menyurat</h3>
                    <p>Pengurusan berbagai surat keterangan seperti surat domisili, surat usaha, dan surat keterangan lainnya.</p>
                </div>
                <div class="card">
                    <i class="fas fa-hands-helping" style="font-size: 3rem; color: #e74c3c; margin-bottom: 1rem;"></i>
                    <h3>Bantuan Sosial</h3>
                    <p>Program bantuan sosial untuk masyarakat kurang mampu dan berbagai program pemberdayaan masyarakat.</p>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="pages/services.php" class="btn">Lihat Semua Layanan</a>
            </div>
        </section>

        <!-- Latest News Section -->
        <section class="section">
            <div class="section-title">
                <h2>Berita Terkini</h2>
                <p>Update terbaru tentang kegiatan dan program-program di desa kami</p>
            </div>
            <div class="news-container">
                <?php foreach($latest_news as $news): ?>
                <article class="news-item">
                    <img src="<?php echo $news['image']; ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMiIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkJlcml0YTwvdGV4dD48L3N2Zz4='">
                    <div class="news-content">
                        <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                        <div class="news-meta">
                            <i class="fas fa-calendar"></i> <?php echo date('d F Y', strtotime($news['date'])); ?>
                        </div>
                        <p><?php echo htmlspecialchars($news['excerpt']); ?></p>
                        <a href="pages/news-detail.php?id=<?php echo $news['id']; ?>" class="btn btn-primary">Baca Selengkapnya</a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="pages/news.php" class="btn">Lihat Semua Berita</a>
            </div>
        </section>

        <!-- Quick Access Section -->
        <section class="section">
            <div class="section-title">
                <h2>Akses Cepat</h2>
                <p>Tautan penting dan informasi yang sering dibutuhkan warga</p>
            </div>
            <div class="grid grid-4">
                <div class="card text-center">
                    <i class="fas fa-download" style="font-size: 2.5rem; color: #3498db; margin-bottom: 1rem;"></i>
                    <h3>Download</h3>
                    <p>Formulir dan dokumen penting</p>
                    <a href="#" class="btn btn-primary">Unduh</a>
                </div>
                <div class="card text-center">
                    <i class="fas fa-calendar-alt" style="font-size: 2.5rem; color: #27ae60; margin-bottom: 1rem;"></i>
                    <h3>Agenda</h3>
                    <p>Jadwal kegiatan desa</p>
                    <a href="#" class="btn btn-primary">Lihat</a>
                </div>
                <div class="card text-center">
                    <i class="fas fa-chart-bar" style="font-size: 2.5rem; color: #f39c12; margin-bottom: 1rem;"></i>
                    <h3>Data Desa</h3>
                    <p>Statistik dan data desa</p>
                    <a href="#" class="btn btn-primary">Lihat</a>
                </div>
                <div class="card text-center">
                    <i class="fas fa-phone-alt" style="font-size: 2.5rem; color: #e74c3c; margin-bottom: 1rem;"></i>
                    <h3>Kontak</h3>
                    <p>Hubungi perangkat desa</p>
                    <a href="pages/contact.php" class="btn btn-primary">Hubungi</a>
                </div>
            </div>
        </section>

        <!-- Gallery Preview -->
        <section class="section">
            <div class="section-title">
                <h2>Galeri Desa</h2>
                <p>Dokumentasi kegiatan dan keindahan alam Desa Maju Bersama</p>
            </div>
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="assets/images/gallery1.jpg" alt="Kegiatan Gotong Royong" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjUwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkdvdG9uZyBSb3lvbmc8L3RleHQ+PC9zdmc+'">
                    <div class="gallery-overlay">
                        <h4>Kegiatan Gotong Royong</h4>
                        <p>Warga desa bergotong royong membersihkan lingkungan</p>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="assets/images/gallery2.jpg" alt="Pemandangan Desa" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjUwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPlBlbWFuZGFuZ2FuPC90ZXh0Pjwvc3ZnPg=='">
                    <div class="gallery-overlay">
                        <h4>Pemandangan Desa</h4>
                        <p>Keindahan alam desa di pagi hari</p>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="assets/images/gallery3.jpg" alt="Kegiatan Posyandu" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjUwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPlBvc3lhbmR1PC90ZXh0Pjwvc3ZnPg=='">
                    <div class="gallery-overlay">
                        <h4>Kegiatan Posyandu</h4>
                        <p>Pelayanan kesehatan untuk ibu dan anak</p>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="assets/images/gallery4.jpg" alt="Festival Desa" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjUwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkZlc3RpdmFsPC90ZXh0Pjwvc3ZnPg=='">
                    <div class="gallery-overlay">
                        <h4>Festival Desa</h4>
                        <p>Perayaan budaya dan seni tradisional desa</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="pages/gallery.php" class="btn">Lihat Galeri Lengkap</a>
            </div>
        </section>

    </div>
</main>

<?php include 'includes/footer.php'; ?>