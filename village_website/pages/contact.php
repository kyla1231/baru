<?php
require_once '../includes/config.php';

$page_title = 'Kontak';
$page_description = 'Hubungi Desa Maju Bersama - informasi kontak, alamat, dan formulir kontak untuk berkomunikasi dengan pemerintah desa';

// Handle contact form submission
$message = '';
$message_type = '';

if ($_POST && isset($_POST['submit_contact'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $contact_message = htmlspecialchars(trim($_POST['message']));
    
    if ($name && $email && $subject && $contact_message) {
        // In a real application, you would save this to database or send email
        // For demo purposes, we'll just show a success message
        $message = 'Terima kasih! Pesan Anda telah dikirim. Kami akan merespons dalam 1-2 hari kerja.';
        $message_type = 'success';
        
        // Clear form data
        $_POST = array();
    } else {
        $message = 'Mohon lengkapi semua field yang wajib diisi.';
        $message_type = 'error';
    }
}

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">
        
        <!-- Page Header -->
        <section class="section">
            <div class="section-title">
                <h2>Hubungi Kami</h2>
                <p>Kami siap melayani dan membantu kebutuhan Anda</p>
            </div>
        </section>

        <!-- Contact Information -->
        <section class="section">
            <div class="grid grid-3">
                <div class="card text-center">
                    <i class="fas fa-map-marker-alt" style="font-size: 3rem; color: #3498db; margin-bottom: 1rem;"></i>
                    <h3>Alamat</h3>
                    <p>Jl. Desa Maju No. 123<br>
                    Kecamatan Contoh<br>
                    Kabupaten Contoh 12345<br>
                    Provinsi Contoh</p>
                </div>
                <div class="card text-center">
                    <i class="fas fa-phone" style="font-size: 3rem; color: #27ae60; margin-bottom: 1rem;"></i>
                    <h3>Telepon</h3>
                    <p><strong>Kantor Desa:</strong><br>
                    +62 123 456 7890</p>
                    <p><strong>Kepala Desa:</strong><br>
                    +62 123 456 7891</p>
                    <p><strong>Sekretaris Desa:</strong><br>
                    +62 123 456 7892</p>
                </div>
                <div class="card text-center">
                    <i class="fas fa-envelope" style="font-size: 3rem; color: #e74c3c; margin-bottom: 1rem;"></i>
                    <h3>Email</h3>
                    <p><strong>Email Resmi:</strong><br>
                    <?php echo ADMIN_EMAIL; ?></p>
                    <p><strong>Pengaduan:</strong><br>
                    pengaduan@desamajubersama.id</p>
                    <p><strong>Informasi:</strong><br>
                    info@desamajubersama.id</p>
                </div>
            </div>
        </section>

        <!-- Office Hours -->
        <section class="section">
            <div class="card">
                <h3>Jam Pelayanan</h3>
                <div class="grid grid-2 mt-3">
                    <div>
                        <h4>Pelayanan Administrasi</h4>
                        <ul>
                            <li><strong>Senin - Kamis:</strong> 08:00 - 15:30 WIB</li>
                            <li><strong>Jumat:</strong> 08:00 - 11:30 WIB</li>
                            <li><strong>Sabtu - Minggu:</strong> Tutup</li>
                        </ul>
                        <p><em>Istirahat: 12:00 - 13:00 WIB (Senin-Kamis)</em></p>
                    </div>
                    <div>
                        <h4>Pelayanan Darurat</h4>
                        <ul>
                            <li><strong>24 Jam:</strong> Keadaan darurat</li>
                            <li><strong>Pos Keamanan:</strong> +62 123 456 7893</li>
                            <li><strong>Ambulan Desa:</strong> +62 123 456 7894</li>
                        </ul>
                        <p><em>Untuk keperluan mendesak di luar jam kerja</em></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Form -->
        <section class="section">
            <div class="grid grid-2">
                <div class="card">
                    <h3>Kirim Pesan</h3>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 5px; background: <?php echo $message_type == 'success' ? '#d4edda' : '#f8d7da'; ?>; border: 1px solid <?php echo $message_type == 'success' ? '#c3e6cb' : '#f5c6cb'; ?>; color: <?php echo $message_type == 'success' ? '#155724' : '#721c24'; ?>;">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name">Nama Lengkap *</label>
                            <input type="text" id="name" name="name" class="form-control" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Nomor Telepon</label>
                            <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subjek *</label>
                            <select id="subject" name="subject" class="form-control" required>
                                <option value="">Pilih Subjek</option>
                                <option value="Informasi Umum" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Informasi Umum') ? 'selected' : ''; ?>>Informasi Umum</option>
                                <option value="Pelayanan Administrasi" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Pelayanan Administrasi') ? 'selected' : ''; ?>>Pelayanan Administrasi</option>
                                <option value="Pengaduan" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Pengaduan') ? 'selected' : ''; ?>>Pengaduan</option>
                                <option value="Saran dan Kritik" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Saran dan Kritik') ? 'selected' : ''; ?>>Saran dan Kritik</option>
                                <option value="Kerjasama" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Kerjasama') ? 'selected' : ''; ?>>Kerjasama</option>
                                <option value="Lainnya" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Pesan *</label>
                            <textarea id="message" name="message" class="form-control" rows="6" required placeholder="Tuliskan pesan Anda di sini..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>
                        
                        <button type="submit" name="submit_contact" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
                
                <!-- Map and Additional Info -->
                <div class="card">
                    <h3>Peta Lokasi</h3>
                    <div style="width: 100%; height: 300px; background: #f0f0f0; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <div style="text-align: center; color: #888;">
                            <i class="fas fa-map-marked-alt" style="font-size: 3rem; margin-bottom: 10px;"></i>
                            <p>Peta akan ditampilkan di sini<br>
                            <small>Integrasi dengan Google Maps atau OpenStreetMap</small></p>
                        </div>
                    </div>
                    
                    <h4>Akses Transportasi</h4>
                    <ul>
                        <li><strong>Kendaraan Pribadi:</strong> Parkir tersedia di halaman kantor desa</li>
                        <li><strong>Transportasi Umum:</strong> Angkot jurusan Terminal-Desa Maju</li>
                        <li><strong>Ojek Online:</strong> Tersedia layanan ojek online</li>
                        <li><strong>Jalan Kaki:</strong> 5 menit dari jalan raya utama</li>
                    </ul>
                    
                    <h4>Landmark Terdekat</h4>
                    <ul>
                        <li>Masjid Besar Desa (50m)</li>
                        <li>Sekolah Dasar Negeri 1 (100m)</li>
                        <li>Pasar Tradisional (200m)</li>
                        <li>Puskesmas Pembantu (300m)</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Department Contacts -->
        <section class="section">
            <div class="section-title">
                <h2>Kontak Perangkat Desa</h2>
                <p>Hubungi langsung perangkat desa sesuai kebutuhan Anda</p>
            </div>
            <div class="grid grid-2">
                <div class="card">
                    <h3>Pemerintahan</h3>
                    <div style="margin-bottom: 20px;">
                        <h4>Kepala Desa</h4>
                        <p><strong>Bapak Suyanto, S.AP</strong><br>
                        <i class="fas fa-phone"></i> +62 123 456 7891<br>
                        <i class="fas fa-envelope"></i> kepala.desa@desamajubersama.id</p>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <h4>Sekretaris Desa</h4>
                        <p><strong>Ibu Siti Aminah, S.Sos</strong><br>
                        <i class="fas fa-phone"></i> +62 123 456 7892<br>
                        <i class="fas fa-envelope"></i> sekretaris@desamajubersama.id</p>
                    </div>
                    
                    <div>
                        <h4>Bendahara Desa</h4>
                        <p><strong>Bapak Ahmad Fauzi, S.E</strong><br>
                        <i class="fas fa-phone"></i> +62 123 456 7895<br>
                        <i class="fas fa-envelope"></i> bendahara@desamajubersama.id</p>
                    </div>
                </div>
                
                <div class="card">
                    <h3>Pelayanan</h3>
                    <div style="margin-bottom: 20px;">
                        <h4>Kaur Pemerintahan</h4>
                        <p><strong>Bapak Joko Susilo</strong><br>
                        <i class="fas fa-phone"></i> +62 123 456 7896<br>
                        <i class="fas fa-envelope"></i> pemerintahan@desamajubersama.id</p>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <h4>Kaur Pembangunan</h4>
                        <p><strong>Bapak Rudi Hartono</strong><br>
                        <i class="fas fa-phone"></i> +62 123 456 7897<br>
                        <i class="fas fa-envelope"></i> pembangunan@desamajubersama.id</p>
                    </div>
                    
                    <div>
                        <h4>Kaur Kesejahteraan</h4>
                        <p><strong>Ibu Dewi Sartika</strong><br>
                        <i class="fas fa-phone"></i> +62 123 456 7898<br>
                        <i class="fas fa-envelope"></i> kesejahteraan@desamajubersama.id</p>
                    </div>
                </div>
            </div>
        </section>

    </div>
</main>

<?php include '../includes/footer.php'; ?>