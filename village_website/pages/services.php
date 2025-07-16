<?php
require_once '../includes/config.php';

$page_title = 'Pelayanan Desa';
$page_description = 'Layanan administrasi dan kemasyarakatan Desa Maju Bersama - informasi lengkap prosedur, persyaratan, dan biaya';

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">
        
        <!-- Page Header -->
        <section class="section">
            <div class="section-title">
                <h2>Pelayanan Desa</h2>
                <p>Berbagai layanan administrasi dan kemasyarakatan untuk memudahkan warga</p>
            </div>
        </section>

        <!-- Service Categories -->
        <section class="section">
            <div class="grid grid-3">
                <div class="card text-center">
                    <i class="fas fa-id-card" style="font-size: 3rem; color: #3498db; margin-bottom: 1rem;"></i>
                    <h3>Administrasi Kependudukan</h3>
                    <p>Layanan dokumen kependudukan dan identitas</p>
                </div>
                <div class="card text-center">
                    <i class="fas fa-file-alt" style="font-size: 3rem; color: #27ae60; margin-bottom: 1rem;"></i>
                    <h3>Surat Menyurat</h3>
                    <p>Berbagai surat keterangan dan perizinan</p>
                </div>
                <div class="card text-center">
                    <i class="fas fa-hands-helping" style="font-size: 3rem; color: #e74c3c; margin-bottom: 1rem;"></i>
                    <h3>Bantuan Sosial</h3>
                    <p>Program bantuan dan pemberdayaan masyarakat</p>
                </div>
            </div>
        </section>

        <!-- Administration Services -->
        <section class="section">
            <div class="section-title">
                <h2>Layanan Administrasi Kependudukan</h2>
            </div>
            <div class="grid grid-2">
                <div class="card">
                    <h3>Kartu Tanda Penduduk (KTP)</h3>
                    <p><strong>Persyaratan:</strong></p>
                    <ul>
                        <li>Surat Pengantar RT/RW</li>
                        <li>Kartu Keluarga asli dan fotokopi</li>
                        <li>Akta Kelahiran asli dan fotokopi</li>
                        <li>Pas foto 4x6 (3 lembar)</li>
                        <li>Formulir biodata penduduk</li>
                    </ul>
                    <p><strong>Biaya:</strong> Gratis</p>
                    <p><strong>Waktu Proses:</strong> 1-3 hari kerja</p>
                </div>
                
                <div class="card">
                    <h3>Kartu Keluarga (KK)</h3>
                    <p><strong>Persyaratan:</strong></p>
                    <ul>
                        <li>Surat Pengantar RT/RW</li>
                        <li>KTP kepala keluarga dan anggota keluarga</li>
                        <li>Surat Nikah/Cerai (jika ada)</li>
                        <li>Akta Kelahiran anak (jika ada)</li>
                        <li>Formulir perubahan kartu keluarga</li>
                    </ul>
                    <p><strong>Biaya:</strong> Gratis</p>
                    <p><strong>Waktu Proses:</strong> 1-3 hari kerja</p>
                </div>
                
                <div class="card">
                    <h3>Akta Kelahiran</h3>
                    <p><strong>Persyaratan:</strong></p>
                    <ul>
                        <li>Surat keterangan kelahiran dari bidan/dokter</li>
                        <li>KTP dan KK orang tua</li>
                        <li>Surat Nikah orang tua</li>
                        <li>Formulir permohonan akta kelahiran</li>
                        <li>Pas foto 3x4 (2 lembar)</li>
                    </ul>
                    <p><strong>Biaya:</strong> Gratis (0-60 hari), Rp 50.000 (>60 hari)</p>
                    <p><strong>Waktu Proses:</strong> 7-14 hari kerja</p>
                </div>
                
                <div class="card">
                    <h3>Akta Kematian</h3>
                    <p><strong>Persyaratan:</strong></p>
                    <ul>
                        <li>Surat keterangan kematian dari dokter/RS</li>
                        <li>KTP dan KK almarhum</li>
                        <li>KTP pelapor</li>
                        <li>Surat Pengantar RT/RW</li>
                        <li>Formulir permohonan akta kematian</li>
                    </ul>
                    <p><strong>Biaya:</strong> Gratis</p>
                    <p><strong>Waktu Proses:</strong> 1-3 hari kerja</p>
                </div>
            </div>
        </section>

        <!-- Letter Services -->
        <section class="section">
            <div class="section-title">
                <h2>Layanan Surat Menyurat</h2>
            </div>
            <div class="grid grid-2">
                <div class="card">
                    <h3>Surat Keterangan Domisili</h3>
                    <p><strong>Persyaratan:</strong></p>
                    <ul>
                        <li>Surat Pengantar RT/RW</li>
                        <li>Fotokopi KTP</li>
                        <li>Fotokopi KK</li>
                        <li>Pas foto 3x4 (2 lembar)</li>
                    </ul>
                    <p><strong>Biaya:</strong> Rp 10.000</p>
                    <p><strong>Waktu Proses:</strong> 1 hari kerja</p>
                </div>
                
                <div class="card">
                    <h3>Surat Keterangan Usaha</h3>
                    <p><strong>Persyaratan:</strong></p>
                    <ul>
                        <li>Surat Pengantar RT/RW</li>
                        <li>Fotokopi KTP</li>
                        <li>Fotokopi KK</li>
                        <li>Keterangan jenis usaha</li>
                        <li>Pas foto 3x4 (2 lembar)</li>
                    </ul>
                    <p><strong>Biaya:</strong> Rp 15.000</p>
                    <p><strong>Waktu Proses:</strong> 1-2 hari kerja</p>
                </div>
                
                <div class="card">
                    <h3>Surat Keterangan Tidak Mampu</h3>
                    <p><strong>Persyaratan:</strong></p>
                    <ul>
                        <li>Surat Pengantar RT/RW</li>
                        <li>Fotokopi KTP</li>
                        <li>Fotokopi KK</li>
                        <li>Keterangan penghasilan</li>
                        <li>Pas foto 3x4 (2 lembar)</li>
                    </ul>
                    <p><strong>Biaya:</strong> Gratis</p>
                    <p><strong>Waktu Proses:</strong> 1 hari kerja</p>
                </div>
                
                <div class="card">
                    <h3>Surat Keterangan Belum Menikah</h3>
                    <p><strong>Persyaratan:</strong></p>
                    <ul>
                        <li>Surat Pengantar RT/RW</li>
                        <li>Fotokopi KTP</li>
                        <li>Fotokopi KK</li>
                        <li>Surat pernyataan belum menikah</li>
                        <li>Pas foto 3x4 (2 lembar)</li>
                    </ul>
                    <p><strong>Biaya:</strong> Rp 10.000</p>
                    <p><strong>Waktu Proses:</strong> 1 hari kerja</p>
                </div>
            </div>
        </section>

        <!-- Social Services -->
        <section class="section">
            <div class="section-title">
                <h2>Program Bantuan Sosial</h2>
            </div>
            <div class="grid grid-2">
                <div class="card">
                    <h3>Bantuan Langsung Tunai (BLT)</h3>
                    <p>Program bantuan tunai untuk keluarga kurang mampu yang terdampak berbagai situasi ekonomi.</p>
                    <p><strong>Syarat:</strong></p>
                    <ul>
                        <li>Tercatat sebagai warga desa</li>
                        <li>Keluarga pra sejahtera</li>
                        <li>Memiliki KTP dan KK</li>
                        <li>Tidak menerima bantuan sosial lainnya</li>
                    </ul>
                    <p><strong>Besaran:</strong> Rp 300.000/bulan</p>
                </div>
                
                <div class="card">
                    <h3>Program Keluarga Harapan (PKH)</h3>
                    <p>Bantuan sosial bersyarat untuk keluarga miskin dengan komponen kesehatan dan pendidikan.</p>
                    <p><strong>Syarat:</strong></p>
                    <ul>
                        <li>Keluarga sangat miskin</li>
                        <li>Memiliki anak usia sekolah</li>
                        <li>Ibu hamil/menyusui</li>
                        <li>Terdaftar dalam data terpadu</li>
                    </ul>
                    <p><strong>Besaran:</strong> Rp 550.000-2.000.000/tahap</p>
                </div>
                
                <div class="card">
                    <h3>Bantuan Sembako</h3>
                    <p>Bantuan sembilan bahan pokok untuk keluarga kurang mampu secara rutin setiap bulan.</p>
                    <p><strong>Syarat:</strong></p>
                    <ul>
                        <li>Keluarga pra sejahtera</li>
                        <li>Lansia tanpa keluarga</li>
                        <li>Penyandang disabilitas</li>
                        <li>Memiliki kartu keluarga</li>
                    </ul>
                    <p><strong>Isi Paket:</strong> Beras, minyak, gula, dll</p>
                </div>
                
                <div class="card">
                    <h3>Bantuan Modal Usaha</h3>
                    <p>Program pemberdayaan ekonomi melalui bantuan modal untuk mengembangkan usaha kecil.</p>
                    <p><strong>Syarat:</strong></p>
                    <ul>
                        <li>Memiliki usaha atau rencana usaha</li>
                        <li>Proposal usaha sederhana</li>
                        <li>Rekomendasi RT/RW</li>
                        <li>Sanggup mengikuti pembinaan</li>
                    </ul>
                    <p><strong>Besaran:</strong> Rp 1.000.000-5.000.000</p>
                </div>
            </div>
        </section>

        <!-- Other Services -->
        <section class="section">
            <div class="section-title">
                <h2>Layanan Lainnya</h2>
            </div>
            <div class="grid grid-3">
                <div class="card text-center">
                    <i class="fas fa-heartbeat" style="font-size: 2.5rem; color: #e74c3c; margin-bottom: 1rem;"></i>
                    <h3>Posyandu</h3>
                    <p>Pelayanan kesehatan ibu dan anak setiap bulan di 5 titik posyandu desa.</p>
                    <p><strong>Jadwal:</strong> Minggu ke-2 setiap bulan</p>
                </div>
                
                <div class="card text-center">
                    <i class="fas fa-graduation-cap" style="font-size: 2.5rem; color: #3498db; margin-bottom: 1rem;"></i>
                    <h3>Pendidikan</h3>
                    <p>Program beasiswa dan bantuan pendidikan untuk anak kurang mampu berprestasi.</p>
                    <p><strong>Periode:</strong> Setiap tahun ajaran baru</p>
                </div>
                
                <div class="card text-center">
                    <i class="fas fa-seedling" style="font-size: 2.5rem; color: #27ae60; margin-bottom: 1rem;"></i>
                    <h3>Penyuluhan Pertanian</h3>
                    <p>Bimbingan teknis pertanian modern dan organik untuk meningkatkan hasil panen.</p>
                    <p><strong>Jadwal:</strong> Setiap hari Rabu minggu ke-3</p>
                </div>
            </div>
        </section>

        <!-- Service Hours -->
        <section class="section">
            <div class="card">
                <h3>Informasi Pelayanan</h3>
                <div class="grid grid-2 mt-3">
                    <div>
                        <h4>Jam Operasional</h4>
                        <ul>
                            <li><strong>Senin - Kamis:</strong> 08:00 - 15:30 WIB</li>
                            <li><strong>Jumat:</strong> 08:00 - 11:30 WIB</li>
                            <li><strong>Sabtu - Minggu:</strong> Tutup</li>
                        </ul>
                        <p><em>Istirahat: 12:00 - 13:00 WIB</em></p>
                        
                        <h4>Lokasi Pelayanan</h4>
                        <p>Kantor Desa Maju Bersama<br>
                        Jl. Desa Maju No. 123<br>
                        Telp: +62 123 456 7890</p>
                    </div>
                    
                    <div>
                        <h4>Prosedur Pelayanan</h4>
                        <ol>
                            <li>Siapkan persyaratan sesuai jenis layanan</li>
                            <li>Datang ke kantor desa pada jam operasional</li>
                            <li>Ambil nomor antrian di loket pendaftaran</li>
                            <li>Tunggu panggilan sesuai nomor antrian</li>
                            <li>Serahkan berkas dan isi formulir</li>
                            <li>Bayar biaya administrasi (jika ada)</li>
                            <li>Terima tanda bukti dan jadwal pengambilan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact for Services -->
        <section class="section">
            <div class="card text-center">
                <h3>Butuh Bantuan?</h3>
                <p>Tim pelayanan kami siap membantu Anda dengan berbagai pertanyaan seputar pelayanan desa.</p>
                <div style="margin-top: 2rem;">
                    <a href="contact.php" class="btn btn-primary" style="margin: 0 10px;">
                        <i class="fas fa-phone"></i> Hubungi Kami
                    </a>
                    <a href="https://wa.me/6212345678901" class="btn" style="margin: 0 10px; background: #25d366;" target="_blank">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
        </section>

    </div>
</main>

<?php include '../includes/footer.php'; ?>