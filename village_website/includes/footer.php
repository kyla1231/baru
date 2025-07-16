    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Tentang Desa</h3>
                    <p><?php echo SITE_NAME; ?> adalah desa yang terletak di Kabupaten Contoh, Provinsi Contoh. Kami berkomitmen untuk memberikan pelayanan terbaik kepada masyarakat.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Link Cepat</h3>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/pages/profile.php">Profil Desa</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/government.php">Pemerintahan</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/services.php">Pelayanan</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/news.php">Berita Terkini</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/gallery.php">Galeri</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Kontak Kami</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Jl. Desa Maju No. 123<br>
                    Kabupaten Contoh, Provinsi Contoh 12345</p>
                    <p><i class="fas fa-phone"></i> +62 123 456 7890</p>
                    <p><i class="fas fa-envelope"></i> <?php echo ADMIN_EMAIL; ?></p>
                    <p><i class="fas fa-clock"></i> Senin - Jumat: 08:00 - 16:00 WIB</p>
                </div>
                
                <div class="footer-section">
                    <h3>Jam Pelayanan</h3>
                    <ul>
                        <li>Senin - Kamis: 08:00 - 15:30 WIB</li>
                        <li>Jumat: 08:00 - 11:30 WIB</li>
                        <li>Sabtu - Minggu: Tutup</li>
                    </ul>
                    <p><strong>Pelayanan Darurat:</strong><br>
                    24 jam (Emergency)</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Hak Cipta Dilindungi Undang-Undang. | Dibuat dengan ❤️ untuk kemajuan desa</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    
    <!-- Additional page scripts -->
    <?php if (isset($additional_scripts)): ?>
        <?php echo $additional_scripts; ?>
    <?php endif; ?>
</body>
</html>