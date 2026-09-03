# Jaspel Automation System (JAS)

Aplikasi otomasi pengolahan data Jasa Pelayanan (Jaspel) untuk Unit Administrasi / Keuangan RSGM Universitas Jember. Aplikasi ini dibangun untuk mempermudah dan mempercepat proses transformasi data dari file Excel mentah menjadi file rekap Jaspel secara otomatis tanpa proses manual.

## Teknologi
*   **Backend:** PHP Native
*   **Frontend:** HTML5, CSS3, Vanilla JavaScript
*   **Excel Library:** PhpSpreadsheet
*   **Database:** MySQL

## Panduan Instalasi Lengkap (Dari Awal)

Ikuti langkah-langkah di bawah ini untuk memasang dan menjalankan aplikasi ini pada komputer baru (misalnya di komputer RSGM).

### 1. Instalasi XAMPP
XAMPP digunakan sebagai server lokal (Web Server & Database).
1. Download XAMPP untuk Windows melalui situs resminya: [apachefriends.org](https://www.apachefriends.org/index.html).
2. Jalankan installer (`.exe`) yang sudah didownload.
3. Klik **Next** hingga selesai (pastikan komponen **Apache** dan **MySQL** tercentang).
4. Buka aplikasi **XAMPP Control Panel**.
5. Sebelum menjalankan Apache, klik tombol **Config** di baris Apache, lalu pilih **PHP (php.ini)**.
6. Saat Notepad terbuka, cari (Ctrl+F) teks `;extension=gd` lalu hapus tanda titik koma (`;`) di depannya sehingga menjadi `extension=gd`.
7. Cari lagi teks `;extension=zip` dan hapus titik komanya menjadi `extension=zip`.
8. Simpan file (Ctrl+S) dan tutup Notepad.
9. Klik tombol **Start** pada baris **Apache** dan **MySQL** hingga indikatornya berwarna hijau.

### 2. Instalasi Git
Git digunakan untuk mengambil (*clone*) kode aplikasi dari GitHub.
1. Download Git untuk Windows melalui: [git-scm.com](https://git-scm.com/download/win).
2. Jalankan file installernya.
3. Klik **Next** terus menerus menggunakan pengaturan default sampai proses instalasi selesai.

### 3. Instalasi Composer
Composer dibutuhkan untuk mengelola dependensi PHP (seperti library PhpSpreadsheet).
1. Download Composer melalui: [getcomposer.org](https://getcomposer.org/download/).
2. Jalankan `Composer-Setup.exe`.
3. Klik **Next**, dan pastikan Composer mendeteksi path PHP dari XAMPP Anda (biasanya di `C:\xampp\php\php.exe`).
4. Selesaikan instalasi.

### 4. Clone Project dari GitHub
1. Buka folder htdocs XAMPP Anda (biasanya di `C:\xampp\htdocs\`).
2. Klik kanan di area kosong di dalam folder tersebut, lalu pilih **"Open Git Bash here"** (atau "Show more options" -> "Open Git Bash here" di Windows 11).
3. Pada terminal hitam yang muncul, ketikkan perintah berikut lalu tekan Enter:
   ```bash
   git clone https://github.com/Aisarsyb/jaspel-automation-system.git Excel_Automation_System
   ```
4. Tunggu hingga proses download selesai. Akan muncul folder baru bernama `Excel_Automation_System`.

### 5. Install Dependensi (Composer)
1. Buka folder hasil clone tadi: `C:\xampp\htdocs\Excel_Automation_System`.
2. Klik kanan di area kosong, pilih **"Open Git Bash here"**.
3. Jalankan perintah berikut untuk menginstall library yang dibutuhkan:
   ```bash
   composer install
   ```
   *(Jika terjadi error merah terkait ekstensi, jalankan perintah ini sebagai alternatif: `composer install --ignore-platform-reqs`)*

### 6. Import Database
1. Buka web browser (Chrome/Edge/Firefox) dan akses: `http://localhost/phpmyadmin`
2. Klik menu **New** di sebelah kiri untuk membuat database baru.
3. Beri nama database (misalnya: `jas_db` atau sesuai di file konfigurasi aplikasi Anda) lalu klik **Create**.
4. Pastikan Anda sedang berada di database yang baru dibuat, lalu klik tab **Import** di bagian atas.
5. Klik **Choose File** dan pilih file `database.sql` yang ada di dalam folder `C:\xampp\htdocs\Excel_Automation_System`.
6. Scroll ke bawah lalu klik **Import** / **Go**.

### 7. Jalankan Aplikasi
1. Buka web browser Anda.
2. Akses alamat berikut:
   ```text
   http://localhost/Excel_Automation_System
   ```
3. Aplikasi Jaspel Automation System sudah siap digunakan!

---
**Tips Memperbarui Aplikasi (Update):**
Jika di kemudian hari ada pembaruan kode di GitHub, Anda cukup masuk ke folder `C:\xampp\htdocs\Excel_Automation_System` lalu klik 2x pada file **`update.bat`**. Aplikasi akan otomatis menarik update terbaru.
