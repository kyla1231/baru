<?php
require_once '../includes/config.php';

$page_title = 'Profil Desa';
$page_description = 'Profil lengkap Desa Maju Bersama - sejarah, visi misi, potensi desa, dan demografi penduduk';

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">
        
        <!-- Page Header -->
        <section class="section">
            <div class="section-title">
                <h2>Profil Desa Maju Bersama</h2>
                <p>Mengenal lebih dalam tentang sejarah, visi misi, dan potensi desa kami</p>
            </div>
        </section>

        <!-- Village Overview -->
        <section class="section">
            <div class="grid grid-2">
                <div class="card">
                    <img src="../assets/images/village-profile.jpg" alt="Profil Desa" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNiIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkdhbWJhcmFuIERlc2E8L3RleHQ+PC9zdmc+'">
                </div>
                <div>
                    <h3>Gambaran Umum</h3>
                    <p><strong>Nama Desa:</strong> Desa Maju Bersama</p>
                    <p><strong>Kecamatan:</strong> Kecamatan Contoh</p>
                    <p><strong>Kabupaten:</strong> Kabupaten Contoh</p>
                    <p><strong>Provinsi:</strong> Provinsi Contoh</p>
                    <p><strong>Kode Pos:</strong> 12345</p>
                    <p><strong>Luas Wilayah:</strong> 15.5 km²</p>
                    <p><strong>Ketinggian:</strong> 450 mdpl</p>
                    <p><strong>Batas Wilayah:</strong></p>
                    <ul>
                        <li>Utara: Desa Maju Utara</li>
                        <li>Selatan: Desa Maju Selatan</li>
                        <li>Timur: Desa Maju Timur</li>
                        <li>Barat: Desa Maju Barat</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- History -->
        <section class="section">
            <div class="card">
                <h3>Sejarah Desa</h3>
                <p>Desa Maju Bersama didirikan pada tahun 1945 oleh para pendiri yang memiliki visi untuk menciptakan komunitas yang harmonis dan sejahtera. Nama "Maju Bersama" mencerminkan semangat gotong royong dan kerja sama yang menjadi ciri khas masyarakat desa ini.</p>
                
                <p>Pada awalnya, desa ini merupakan daerah pertanian dengan mayoritas penduduk bermata pencaharian sebagai petani. Seiring berkembangnya zaman, desa ini telah mengalami transformasi yang signifikan dengan tetap mempertahankan nilai-nilai tradisional dan kearifan lokal.</p>
                
                <p>Pencapaian penting dalam sejarah desa termasuk pembangunan infrastruktur jalan pada tahun 1960, elektrifikasi pada tahun 1975, dan pembangunan balai desa pada tahun 1980. Dalam dekade terakhir, desa ini telah aktif mengembangkan potensi wisata dan ekonomi kreatif.</p>
            </div>
        </section>

        <!-- Vision & Mission -->
        <section class="section">
            <div class="grid grid-2">
                <div class="card">
                    <h3>Visi Desa</h3>
                    <p>"Mewujudkan Desa Maju Bersama yang mandiri, sejahtera, dan berbudaya, dengan mengedepankan nilai-nilai gotong royong, transparansi, dan keberlanjutan lingkungan."</p>
                </div>
                <div class="card">
                    <h3>Misi Desa</h3>
                    <ol>
                        <li>Meningkatkan kualitas pelayanan publik yang transparan dan akuntabel</li>
                        <li>Mengembangkan potensi ekonomi lokal berbasis sumber daya alam dan budaya</li>
                        <li>Memperkuat solidaritas dan partisipasi masyarakat dalam pembangunan desa</li>
                        <li>Melestarikan lingkungan hidup dan kearifan lokal</li>
                        <li>Meningkatkan kualitas pendidikan dan kesehatan masyarakat</li>
                    </ol>
                </div>
            </div>
        </section>

        <!-- Demographics -->
        <section class="section">
            <div class="section-title">
                <h2>Demografi Penduduk</h2>
                <p>Data statistik kependudukan Desa Maju Bersama</p>
            </div>
            <div class="grid grid-3">
                <div class="card text-center">
                    <i class="fas fa-users" style="font-size: 3rem; color: #3498db; margin-bottom: 1rem;"></i>
                    <h3>2,547</h3>
                    <p>Total Penduduk</p>
                </div>
                <div class="card text-center">
                    <i class="fas fa-male" style="font-size: 3rem; color: #27ae60; margin-bottom: 1rem;"></i>
                    <h3>1,289</h3>
                    <p>Laki-laki</p>
                </div>
                <div class="card text-center">
                    <i class="fas fa-female" style="font-size: 3rem; color: #e74c3c; margin-bottom: 1rem;"></i>
                    <h3>1,258</h3>
                    <p>Perempuan</p>
                </div>
                <div class="card text-center">
                    <i class="fas fa-home" style="font-size: 3rem; color: #f39c12; margin-bottom: 1rem;"></i>
                    <h3>867</h3>
                    <p>Kepala Keluarga</p>
                </div>
                <div class="card text-center">
                    <i class="fas fa-map-marker-alt" style="font-size: 3rem; color: #9b59b6; margin-bottom: 1rem;"></i>
                    <h3>12</h3>
                    <p>Dusun</p>
                </div>
                <div class="card text-center">
                    <i class="fas fa-building" style="font-size: 3rem; color: #34495e; margin-bottom: 1rem;"></i>
                    <h3>45</h3>
                    <p>RT/RW</p>
                </div>
            </div>
        </section>

        <!-- Village Potential -->
        <section class="section">
            <div class="section-title">
                <h2>Potensi Desa</h2>
                <p>Berbagai potensi dan keunggulan yang dimiliki Desa Maju Bersama</p>
            </div>
            <div class="grid grid-2">
                <div class="card">
                    <h3>Potensi Pertanian</h3>
                    <ul>
                        <li>Lahan sawah produktif seluas 8.5 km²</li>
                        <li>Kebun buah-buahan tropis</li>
                        <li>Tanaman hortikultura</li>
                        <li>Peternakan sapi dan kambing</li>
                        <li>Budidaya ikan air tawar</li>
                    </ul>
                </div>
                <div class="card">
                    <h3>Potensi Wisata</h3>
                    <ul>
                        <li>Wisata alam pegunungan</li>
                        <li>Air terjun dan sumber mata air</li>
                        <li>Wisata agro dan edukasi</li>
                        <li>Kerajinan tangan tradisional</li>
                        <li>Kuliner khas desa</li>
                    </ul>
                </div>
                <div class="card">
                    <h3>Potensi Ekonomi</h3>
                    <ul>
                        <li>UMKM makanan dan minuman</li>
                        <li>Industri kerajinan bambu</li>
                        <li>Produksi gula aren</li>
                        <li>Pengolahan hasil pertanian</li>
                        <li>Jasa transportasi dan pariwisata</li>
                    </ul>
                </div>
                <div class="card">
                    <h3>Potensi Sumber Daya</h3>
                    <ul>
                        <li>Sumber daya manusia yang berkualitas</li>
                        <li>Kearifan lokal yang terjaga</li>
                        <li>Gotong royong yang kuat</li>
                        <li>Infrastruktur yang memadai</li>
                        <li>Akses transportasi yang baik</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Infrastructure -->
        <section class="section">
            <div class="card">
                <h3>Infrastruktur dan Fasilitas</h3>
                <div class="grid grid-3 mt-3">
                    <div>
                        <h4>Pendidikan</h4>
                        <ul>
                            <li>2 Sekolah Dasar</li>
                            <li>1 Sekolah Menengah Pertama</li>
                            <li>3 PAUD/TK</li>
                            <li>1 Perpustakaan Desa</li>
                        </ul>
                    </div>
                    <div>
                        <h4>Kesehatan</h4>
                        <ul>
                            <li>1 Puskesmas Pembantu</li>
                            <li>5 Posyandu Balita</li>
                            <li>2 Posyandu Lansia</li>
                            <li>10 Kader Kesehatan</li>
                        </ul>
                    </div>
                    <div>
                        <h4>Keagamaan</h4>
                        <ul>
                            <li>8 Masjid</li>
                            <li>15 Mushola</li>
                            <li>2 Gereja</li>
                            <li>1 Pura</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Achievements -->
        <section class="section">
            <div class="card">
                <h3>Prestasi dan Penghargaan</h3>
                <div class="grid grid-2 mt-3">
                    <div>
                        <h4>Penghargaan Nasional</h4>
                        <ul>
                            <li>Desa Wisata Terbaik 2023</li>
                            <li>Desa Sehat Nasional 2022</li>
                            <li>Desa Mandiri Energi 2021</li>
                        </ul>
                    </div>
                    <div>
                        <h4>Penghargaan Provinsi</h4>
                        <ul>
                            <li>Desa Inovatif Terbaik 2023</li>
                            <li>Desa Ramah Lingkungan 2022</li>
                            <li>Desa Sadar Hukum 2021</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

    </div>
</main>

<?php include '../includes/footer.php'; ?>