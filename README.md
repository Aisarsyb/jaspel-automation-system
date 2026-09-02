# Jaspel Automation System (JAS)

Aplikasi otomasi pengolahan data Jasa Pelayanan (Jaspel) untuk Unit Administrasi / Keuangan RSGM Universitas Jember. Aplikasi ini dibangun untuk mempermudah dan mempercepat proses transformasi data dari file Excel mentah menjadi file rekap Jaspel secara otomatis tanpa proses manual.

## Teknologi
*   **Backend:** PHP Native
*   **Frontend:** HTML5, CSS3, Vanilla JavaScript
*   **Excel Library:** PhpSpreadsheet
*   **Database:** MySQL

## Prasyarat
*   Web Server (Apache/Nginx) dan PHP (sudah termasuk jika menggunakan XAMPP/MAMP/WAMP)
*   MySQL/MariaDB Database
*   Composer (untuk menginstal library `PhpSpreadsheet`)

## Panduan Instalasi (Memindahkan Project)

Ikuti langkah-langkah berikut untuk menjalankan project ini di komputer/server baru:

### 1. Pindahkan Folder Project
Copy seluruh folder `Excel_Automation_System` ke dalam direktori web server Anda:
*   Jika menggunakan XAMPP: Masukkan ke folder `c:\xampp\htdocs\`
*   Pastikan struktur foldernya menjadi `htdocs/Excel_Automation_System`

### 2. Import Database
1. Buka phpMyAdmin (biasanya melalui `http://localhost/phpmyadmin`).
2. Buat database baru (misalnya dengan nama `jas_db` atau sesuai konfigurasi di file koneksi Anda).
3. Pilih database yang baru dibuat, lalu masuk ke tab **Import**.
4. Pilih file `database.sql` yang ada di dalam folder project ini.
5. Klik **Go** atau **Import** untuk mengeksekusi struktur tabel dan data master.

### 3. Install Dependensi (Composer)
Project ini menggunakan `PhpSpreadsheet` untuk membaca dan menulis file Excel. Anda perlu menginstal dependensinya:
1. Buka terminal atau command prompt.
2. Arahkan ke folder project: `cd c:\xampp\htdocs\Excel_Automation_System`
3. Jalankan perintah berikut:
   ```bash
   composer install
   ```
*(Catatan: Jika folder `vendor` sudah ada dan lengkap beserta isinya saat Anda mengcopy project, langkah ini bisa dilewati, namun disarankan untuk tetap menjalankannya agar dependensi terbarui).*

### 4. Konfigurasi Koneksi Database
Jika Anda membuat nama database yang berbeda, pastikan Anda menyesuaikan konfigurasi koneksi database di file project (biasanya di file koneksi seperti `config.php` atau sejenisnya) dengan nama database, user, dan password yang benar.

### 5. Jalankan Aplikasi
Buka web browser dan akses URL berikut:
```text
http://localhost/Excel_Automation_System
```

Aplikasi Jaspel Automation System (JAS) siap digunakan!
