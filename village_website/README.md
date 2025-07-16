# Website Desa - PHP Village Website

Website desa modern yang dibangun dengan PHP, HTML, CSS, dan JavaScript. Website ini dirancang khusus untuk kebutuhan pemerintahan desa dengan fitur-fitur lengkap untuk informasi publik dan pelayanan masyarakat.

## ✨ Fitur Utama

### 🏠 Frontend (Website Publik)
- **Halaman Beranda** - Hero section, statistik desa, berita terkini, layanan
- **Profil Desa** - Sejarah, visi misi, demografi, potensi desa
- **Pemerintahan** - Struktur organisasi dan profil perangkat desa
- **Pelayanan** - Informasi layanan administrasi dan persyaratan
- **Berita** - Artikel dan pengumuman terkini
- **Galeri** - Dokumentasi foto kegiatan desa
- **Kontak** - Formulir kontak dan informasi kontak

### ⚙️ Backend (Admin Panel)
- Dashboard admin dengan statistik
- Manajemen berita dan artikel
- Manajemen galeri foto
- Manajemen data perangkat desa
- Pengaturan website
- Sistem login admin yang aman

### 🎨 Fitur Teknis
- **Responsive Design** - Optimal di desktop, tablet, dan mobile
- **Modern UI/UX** - Desain modern dengan animasi smooth
- **SEO Friendly** - Struktur HTML semantik dan meta tags
- **Fast Loading** - Optimized CSS dan JavaScript
- **Cross Browser** - Kompatibel dengan semua browser modern
- **Security** - Prepared statements, input validation, session management

## 📋 Persyaratan Sistem

- **Web Server:** Apache/Nginx
- **PHP:** Versi 7.4 atau lebih tinggi
- **Database:** MySQL 5.7+ atau MariaDB 10.3+
- **Extensions PHP:**
  - PDO MySQL
  - GD Library (untuk manipulasi gambar)
  - mbstring
  - fileinfo

## 🚀 Instalasi

### 1. Download dan Extract
```bash
# Clone atau download project
git clone [repository-url]
cd village_website
```

### 2. Konfigurasi Database
Buat database MySQL baru:
```sql
CREATE DATABASE village_website;
```

Edit file `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'village_website');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 3. Upload ke Web Server
- Upload semua file ke direktori web server (htdocs/public_html)
- Pastikan folder `assets/images/` memiliki permission write (755 atau 777)

### 4. Jalankan Installer
Akses `http://yoursite.com/install.php` di browser untuk:
- Membuat tabel database
- Insert data default
- Membuat admin user default

### 5. Login Admin
- **URL Admin:** `http://yoursite.com/admin/login.php`
- **Username:** `admin`
- **Password:** `admin123`

⚠️ **Penting:** Segera ganti password default setelah login pertama!

## 📁 Struktur Folder

```
village_website/
├── admin/                  # Panel admin
│   ├── login.php
│   ├── dashboard.php
│   └── ...
├── assets/                 # Asset statis
│   ├── css/
│   │   └── style.css      # Main stylesheet
│   ├── js/
│   │   └── main.js        # Main JavaScript
│   └── images/            # Folder gambar
├── includes/              # File include
│   ├── config.php         # Konfigurasi database
│   ├── header.php         # Header template
│   └── footer.php         # Footer template
├── pages/                 # Halaman website
│   ├── profile.php
│   ├── contact.php
│   ├── services.php
│   └── ...
├── index.php              # Halaman utama
├── install.php            # Installer database
└── README.md              # Dokumentasi
```

## 🎨 Kustomisasi

### Mengubah Informasi Desa
Edit file `includes/config.php`:
```php
define('SITE_NAME', 'Nama Desa Anda');
define('ADMIN_EMAIL', 'email@desaanda.id');
```

### Mengubah Warna dan Styling
Edit file `assets/css/style.css`:
```css
/* Ubah warna utama */
:root {
  --primary-color: #3498db;
  --secondary-color: #2c3e50;
}
```

### Menambah/Mengubah Logo
- Upload logo ke `assets/images/logo.png`
- Ukuran rekomendasi: 60x60 pixels (PNG dengan background transparan)

### Mengubah Background Hero
- Upload gambar ke `assets/images/village-bg.jpg`
- Ukuran rekomendasi: 1920x1080 pixels

## 📝 Panduan Penggunaan

### Mengelola Berita
1. Login ke admin panel
2. Masuk ke menu "Berita"
3. Klik "Tambah Berita Baru"
4. Isi form dan klik "Publish"

### Mengelola Galeri
1. Login ke admin panel
2. Masuk ke menu "Galeri"
3. Upload foto dengan deskripsi
4. Atur kategori jika diperlukan

### Mengubah Profil Perangkat Desa
1. Login ke admin panel
2. Masuk ke menu "Pemerintahan"
3. Edit atau tambah data perangkat desa

### Mengelola Pesan Kontak
1. Login ke admin panel
2. Masuk ke menu "Pesan Kontak"
3. Lihat dan balas pesan dari warga

## 🔧 Troubleshooting

### Error Database Connection
- Periksa kredensial database di `includes/config.php`
- Pastikan database server berjalan
- Pastikan user database memiliki privilege yang cukup

### Gambar Tidak Muncul
- Periksa permission folder `assets/images/`
- Pastikan path gambar benar
- Periksa ukuran file (max 2MB)

### Error 500 Internal Server
- Periksa PHP error log
- Pastikan semua extension PHP terinstall
- Periksa syntax error di file PHP

### Halaman Admin Tidak Bisa Diakses
- Pastikan sudah menjalankan `install.php`
- Periksa table `admin_users` di database
- Reset password admin jika perlu

## 🔒 Keamanan

### Rekomendasi Keamanan
1. **Ganti password default admin** segera setelah instalasi
2. **Hapus file install.php** setelah instalasi selesai
3. **Update PHP** ke versi terbaru secara berkala
4. **Backup database** secara rutin
5. **Gunakan HTTPS** jika memungkinkan
6. **Batasi akses admin** hanya dari IP terpercaya

### Backup Database
```bash
# Backup database
mysqldump -u username -p village_website > backup.sql

# Restore database
mysql -u username -p village_website < backup.sql
```

## 📞 Support

Jika mengalami kesulitan dalam instalasi atau penggunaan:

1. **Dokumentasi:** Baca file README ini dengan lengkap
2. **Forum:** Cari solusi di forum PHP/web development
3. **GitHub Issues:** Laporkan bug melalui GitHub issues
4. **Email Support:** Hubungi developer melalui email

## 📄 Lisensi

Project ini menggunakan lisensi MIT. Anda bebas menggunakan, memodifikasi, dan mendistribusikan sesuai kebutuhan.

## 🚀 Pengembangan Selanjutnya

Fitur yang dapat dikembangkan:
- [ ] System antrian pelayanan online
- [ ] Integrasi pembayaran digital
- [ ] Mobile app companion
- [ ] Sistem survei/polling online
- [ ] Integrasi WhatsApp Business API
- [ ] Dashboard analytics yang lebih detail
- [ ] Multi-language support
- [ ] PWA (Progressive Web App)

## 🙏 Kontribusi

Kontribusi sangat diterima! Silakan:
1. Fork repository ini
2. Buat branch fitur baru
3. Commit perubahan Anda
4. Push ke branch
5. Buat Pull Request

---

**Dibuat dengan ❤️ untuk kemajuan desa di Indonesia**