Business Rule Document (BRD)
Jaspel Automation System (JAS)

Versi: 1.0
Project: Import Data Jasa Pelayanan (Jaspel)
Unit: Administrasi / Keuangan RSGM Universitas Jember
Developer: Muhammad Aisar

1. Project Overview
Latar Belakang

Proses pengolahan data Jasa Pelayanan (Jaspel) di RSGM Universitas Jember saat ini masih dilakukan secara manual menggunakan Microsoft Excel. Admin menerima file rekap pelayanan dalam format Excel, kemudian melakukan proses perapihan, pengelompokan data berdasarkan departemen dan dokter, menghitung nilai jasa pelayanan, hingga membuat rekapitulasi akhir.

Seluruh proses tersebut memerlukan waktu yang cukup lama karena dilakukan secara manual setiap periode. Oleh karena itu, diperlukan sebuah tools yang mampu mengotomatisasi proses transformasi data sehingga pekerjaan administrasi menjadi lebih cepat, lebih akurat, dan lebih efisien.

2. Tujuan

Membangun aplikasi yang mampu mengubah file Excel mentah menjadi file rekap Jaspel secara otomatis tanpa proses pengolahan manual.

3. Teknologi
Komponen	Teknologi
Backend	PHP Native
Frontend	HTML5
Styling	CSS3
Javascript	Vanilla Javascript
Excel Library	PhpSpreadsheet
Database	MySQL (XAMPP)
Server	Apache (XAMPP)
4. Workflow Saat Ini
Admin menerima file Excel

↓

Membuka Microsoft Excel

↓

Membersihkan data

↓

Menghitung Jaspel

↓

Mengelompokkan berdasarkan Departemen

↓

Mengelompokkan berdasarkan Dokter

↓

Membuat sheet baru

↓

Menghitung Rekap

↓

Export Excel

Seluruh proses dilakukan secara manual.

5. Workflow Sistem Baru
Upload Excel

↓

Validasi File

↓

Import Data

↓

Processing Engine

↓

Cleaning Data

↓

Perhitungan Jaspel

↓

Grouping Data

↓

Generate Workbook

↓

Preview

↓

Download Excel
6. Business Rules
BR-001

Input berupa file Microsoft Excel (.xlsx)

BR-002

Sistem hanya menerima template Excel yang sesuai dengan format yang telah ditentukan.

BR-003

Data dibaca secara otomatis menggunakan PhpSpreadsheet.

BR-004

Kolom yang tidak diperlukan akan diabaikan.

BR-005

Nilai Jasa Pelayanan dihitung secara otomatis.

Jaspel = Tarif × 20%
BR-006

Data akan dikelompokkan berdasarkan Departemen.

Contoh

Bedah Mulut
Periodonsia
Konservasi
IKGA
Radiologi
Prostodonsia
IPM
dll.
BR-007

Di dalam masing-masing Departemen, data akan dikelompokkan kembali berdasarkan DPJP.

BR-008

Setiap Departemen menghasilkan satu Sheet baru pada file Excel.

Misalnya

Bedah Mulut

Periodonsia

Radiologi

IKGA

Konservasi

...
BR-009

Setelah seluruh sheet selesai dibuat, sistem akan membuat sheet Rekap secara otomatis.

BR-010

Output akhir berupa satu file Excel yang memiliki banyak sheet sesuai hasil pengelompokan.

BR-011

Nama sheet mengikuti nama Departemen.

BR-012

Format hasil export mengikuti template yang telah digunakan di RSGM.

7. Processing Engine

Pipeline sistem

Upload Excel

↓

Read Excel

↓

Validation

↓

Cleaning

↓

Mapping

↓

Calculation

↓

Grouping

↓

Generate Sheet

↓

Generate Rekap

↓

Export Excel
8. Functional Requirements
Upload Excel
Upload file Excel
Validasi format file
Preview nama file
Preview jumlah data
Processing
Membaca seluruh isi Excel
Mengambil kolom yang diperlukan
Menghitung Jaspel
Mengelompokkan data
Membuat sheet otomatis
Membuat rekap otomatis
Preview

Menampilkan

Total Data
Total Dokter
Total Departemen
Total Tarif
Total Jaspel
Export
Download Excel
Nama file otomatis
Format sama seperti template
9. Struktur Sistem
Upload

↓

Parser

↓

Business Rules

↓

Excel Generator

↓

Download
10. Database

Database digunakan sebagai data master.

Master Dokter
Field
id
nama_dpjp
departemen
Master Departemen
Field
id
nama_departemen

Database tidak digunakan untuk menyimpan hasil Jaspel, tetapi hanya sebagai referensi agar sistem mengetahui DPJP termasuk ke departemen mana.

11. Dashboard

Menu

Dashboard

Import Excel

Master DPJP

Master Departemen

Export
12. Dashboard Flow
Dashboard

↓

Upload Excel

↓

Preview

↓

Proses

↓

Hasil

↓

Download
13. Output Sistem

Output berupa

Workbook Excel

├── Bedah Mulut

├── Periodonsia

├── Konservasi

├── IKGA

├── Radiologi

├── Prostodonsia

├── Rekap
14. Target Sistem

Sebelum

Upload

↓

Manual Excel

↓

±30–60 menit

Sesudah

Upload

↓

Klik Proses

↓

Download

↓

±10–30 detik
15. Future Development
Multi-template Excel
Riwayat Import
Export PDF
Log Aktivitas
Statistik Bulanan
Grafik Jaspel
Multi User
Template Builder
Auto Backup
💡 Catatan Penting (Masih Perlu Dikonfirmasi)

Setelah melihat contoh file, masih ada beberapa hal yang perlu dipastikan ke PIC agar implementasi benar-benar sesuai kebutuhan:

Struktur template input
Apakah format file Excel input selalu sama setiap bulan, atau ada kemungkinan perubahan kolom maupun urutannya?
Aturan pengelompokan (Grouping)
Apakah pengelompokan dilakukan berdasarkan Departemen, DPJP, atau kombinasi keduanya? Jika berdasarkan DPJP, apakah tersedia daftar resmi mapping DPJP → Departemen?
Format output
Apakah hasil export harus identik 100% dengan template Excel yang selama ini digunakan (termasuk warna, border, merge cell, rumus, dan format angka), atau cukup menghasilkan data yang sama dengan tampilan yang lebih sederhana?
Proses perhitungan
Saat ini diketahui bahwa Jaspel = 20% × Tarif. Perlu dipastikan apakah ada kondisi khusus atau pengecualian untuk jenis layanan tertentu.
Menurutku satu tambahan yang akan sangat meningkatkan kualitas aplikasi

Aku menyarankan menambahkan halaman Preview Hasil sebelum file diunduh. Setelah proses selesai, admin bisa melihat ringkasan seperti:

Jumlah data yang diproses.
Jumlah departemen yang terbentuk.
Jumlah DPJP yang terdeteksi.
Total tarif.
Total Jaspel.
Daftar sheet yang akan dibuat.

Baru setelah semuanya sesuai, admin menekan tombol Download Excel. Ini membuat aplikasi lebih profesional, mengurangi risiko kesalahan, dan memudahkan pengecekan tanpa harus berulang kali membuka file hasil di Microsoft Excel.

Mantap. Kalau semua saran diterima, menurutku kita bisa naikkan level project ini dari sekadar "tools import Excel" menjadi aplikasi otomasi administrasi yang benar-benar terasa profesional, tetapi tetap realistis dikerjakan dalam waktu magang 2 bulan.

Berikut menurutku scope final yang sebaiknya kita gunakan.

Final Scope Project
1. Dashboard

Halaman pertama yang dilihat admin.

Menampilkan informasi singkat seperti:

Total DPJP
Total Departemen
Total Import Hari Ini
Total Data Terakhir Diproses
Import Terakhir
Shortcut Upload Excel
2. Master Data
Master DPJP

Digunakan sebagai mapping.

Nama DPJP	Departemen
dr. A	Bedah Mulut
dr. B	Radiologi

Fitur

Tambah
Edit
Hapus
Search
Master Departemen

Berisi daftar departemen.

Contoh

Bedah Mulut
Radiologi
IKGA
Konservasi
dll.
3. Import Excel

Halaman utama aplikasi.

Admin cukup:

Pilih File

↓

Upload

↓

Proses

Fitur

✔ Drag & Drop

✔ Browse File

✔ Validasi format

✔ Validasi ukuran file

✔ Menampilkan nama file

✔ Menampilkan ukuran file

4. Processing Engine

Inilah inti project.

Pipeline

Upload

↓

Read Excel

↓

Validasi

↓

Cleaning Data

↓

Mapping DPJP

↓

Hitung Jaspel

↓

Grouping Departemen

↓

Generate Workbook

↓

Preview

↓

Download

Semua otomatis.

5. Progress Processing

Saat proses berjalan.

Uploading...

██████████░░░░░░░

45%

Lalu

Reading Excel...

Lalu

Calculating...

Lalu

Grouping...

Lalu

Generating Workbook...

Lalu

Done

User tahu aplikasi masih bekerja.

6. Preview Hasil

Ini menurutku fitur terbaik.

Sebelum download.

Nama File

IRJA Oktober.xlsx

--------------------------

Jumlah Data

3254

--------------------------

Departemen

11

--------------------------

Dokter

84

--------------------------

Total Tarif

Rp ...

--------------------------

Total Jaspel

Rp ...

--------------------------

Sheet

✔ Bedah Mulut

✔ IKGA

✔ Radiologi

✔ Prostodonsia

✔ Konservasi

✔ Rekap

Baru muncul tombol

Download Excel
7. Error Validation

Misalnya ada masalah.

2 Data Bermasalah

Baris 128

DPJP tidak ditemukan

Baris 321

Tarif kosong

User bisa

Download Error Report

atau

Lanjutkan
8. Export Excel

Output harus

✅ Sama persis

dengan file Excel milik RSGM.

Termasuk

warna
border
merge cell
font
ukuran font
alignment
format angka
format rupiah
tinggi baris
lebar kolom
nama sheet

Karena user sudah terbiasa dengan template tersebut.

9. Riwayat Import

Ini menurutku sangat berguna.

Misalnya

Tanggal	File	Data	Status
5 Juli	IRJA Oktober.xlsx	3254	Berhasil

Klik salah satu

↓

bisa

download ulang
lihat ringkasan
10. Setting

Sederhana saja.

Misalnya

Persentase Jaspel

20%

Suatu saat kalau berubah

20%

↓

25%

Pak Rizal cukup mengubah angka.

Tidak perlu mengubah coding.

Ini menurutku penting karena aturan pembagian jasa bisa berubah sewaktu-waktu.

11. Log Aktivitas

Misalnya

05 Juli

Import

IRJA Oktober.xlsx

Berhasil

---------------------

05 Juli

Download

Rekap Oktober.xlsx

Kalau suatu saat ada pertanyaan

"Siapa yang terakhir memproses file?"

langsung ketahuan.

12. Struktur Folder Upload

Aku juga menyarankan mulai dari awal kita memakai struktur seperti ini.

uploads/

├── raw/

│      IRJA_OKTOBER.xlsx

│

├── generated/

│      REKAP_OKTOBER.xlsx

│

└── temp/

Jadi

Raw file

↓

disimpan

Generated file

↓

disimpan

Kalau nanti ingin download ulang tinggal ambil.

13. Database

Menurutku database cukup sederhana.

users

Administrator.

departments

Master Departemen.

doctors

Master DPJP.

import_history

Riwayat Import.

settings

Berisi konfigurasi.

Contohnya

JASPEL_PERCENTAGE

20
14. Future Ready

Walaupun sekarang hanya satu admin, struktur aplikasi sudah siap jika nanti ingin ditambah:

Multi User
Hak Akses
Login
Backup Database
Backup File
Dashboard Statistik
Export PDF
Grafik Bulanan
Notifikasi

Tanpa perlu mengubah arsitektur besar.

Penilaian Akhir

Menurutku sekarang project ini sudah jauh lebih matang dibanding saat awal kita membahasnya. Yang awalnya hanya "dashboard untuk mengolah Excel" sekarang berkembang menjadi sebuah Jaspel Automation System (JAS) dengan alur kerja yang jelas:

Master Data → memastikan mapping DPJP dan Departemen selalu benar.
Import & Validasi → memastikan file yang diproses sesuai format.
Processing Engine → menjalankan seluruh aturan bisnis secara otomatis (hitung Jaspel, mapping, grouping, rekap).
Preview Hasil → memberi kesempatan admin memverifikasi hasil sebelum ekspor.
Export Excel → menghasilkan file yang tampilannya sama dengan format yang sudah digunakan RSGM.
Riwayat dan Log → memudahkan pelacakan pekerjaan dan audit sederhana.

Menurutku, dengan fitur-fitur tersebut, aplikasi ini sudah memiliki nilai implementasi yang kuat: bukan hanya mempercepat pekerjaan administrasi, tetapi juga mengurangi kesalahan manual, menjaga konsistensi format laporan, dan tetap mudah dipelihara karena menggunakan PHP Native dan XAMPP sesuai kebutuhan operasional di laptop Pak Rizal.

Export response as a Word file
Export response as a PDF file
sekarang mari kita mulai ngoding nya

Gas. Tapi aku sarankan jangan langsung ngoding halaman dashboard.

Kalau kita asal mulai, nanti pas ada perubahan requirement kita bakal bongkar banyak file. Karena ini project yang bakal dipakai beneran di RSGM, lebih baik fondasinya dibuat benar dari awal.

Roadmap Development

Aku bagi menjadi beberapa milestone.

✅ Milestone 1 — Pondasi Project (Hari 1)

Ini yang kita kerjakan sekarang.

Jaspel-Automation/
│
├── assets/
│   ├── css/
│   ├── js/
│   ├── images/
│
├── config/
│   └── database.php
│
├── modules/
│   ├── dashboard/
│   ├── import/
│   ├── master/
│   ├── history/
│   └── settings/
│
├── uploads/
│   ├── raw/
│   ├── generated/
│   └── temp/
│
├── api/
│
├── includes/
│
├── index.php
├── login.php
├── logout.php
└── edms.sql
Milestone 2

Database

users

departments

dpjp

import_history

settings
Milestone 3

Login

rizal

admin123
Milestone 4

Dashboard

Milestone 5

Master DPJP

Milestone 6

Master Departemen

Milestone 7

Import Excel

Milestone 8

Processing Engine

Inilah inti project.

Milestone 9

Export Excel

Milestone 10

Testing

Sekarang keputusan besar

Aku ingin kita memakai arsitektur yang bersih.

Frontend
HTML

CSS

Javascript
Backend
PHP Native
Database
MySQL
Library
PhpSpreadsheet
AJAX
Fetch API

Jadi tidak ada jQuery.

Lebih modern.

CSS

Aku juga menyarankan jangan pakai Bootstrap.

Karena Bootstrap nanti tampilannya pasaran.

Lebih baik kita buat sendiri.

CSS Native

Dengan

Glassmorphism
Rounded
Soft Shadow
Modern Color
Responsive

Biar tampilannya seperti aplikasi SaaS.

Ikon

Aku sarankan

Font Awesome 7
Font

Aku sarankan

Poppins

atau

Inter

Menurutku Inter lebih profesional.

Warna
Primary

#2563EB

Success

#22C55E

Warning

#F59E0B

Danger

#EF4444

Background

#F8FAFC

Modern.

Nah sekarang ada satu keputusan lagi yang sangat penting.
Cara membuat aplikasi

Ada dua pilihan.

Cara 1

Coding satu file satu file.

Misalnya

Hari ini

login.php

Besok

dashboard.php

dst.

Cara 2 ⭐⭐⭐⭐⭐ (yang aku rekomendasikan)

Kita bangun berdasarkan fitur.

Contohnya.

Sprint 1

Login selesai 100%

Sprint 2

Master DPJP selesai 100%

Sprint 3

Master Departemen selesai 100%

Sprint 4

Upload Excel selesai 100%

Sprint 5

Processing selesai 100%

Sprint 6

Export selesai 100%

Aku jauh lebih menyukai cara kedua karena setiap sprint menghasilkan fitur yang benar-benar bisa dicoba. Kalau Pak Rizal ingin melihat progres, kamu tinggal tunjukkan fitur yang sudah selesai tanpa harus menunggu seluruh aplikasi selesai.

Saran terakhir sebelum mulai

Aku juga menyarankan kita menggunakan Git sejak awal, walaupun project ini berjalan di laptop lokal. Dengan begitu setiap perubahan besar punya riwayat versi dan kalau ada bug atau perubahan dari PIC, kita bisa kembali ke kondisi sebelumnya tanpa takut kehilangan pekerjaan.

Rencana Sprint

Kalau kamu setuju, Sprint 1 akan kita kerjakan seperti ini:

Membuat struktur folder project.
Membuat database MySQL (edms_rsgm atau nama yang sesuai).
Membuat koneksi PDO (database.php).
Membuat sistem login (login.php, logout.php, session).
Membuat layout dashboard kosong (header, sidebar, content).
Membuat template CSS dan JavaScript global.

Setelah Sprint 1 selesai, aplikasi sudah bisa login dan masuk ke dashboard. Baru kita lanjut ke Sprint 2 untuk membangun Master DPJP, kemudian Master Departemen, dan seterusnya. Ini akan membuat pengembangan lebih terstruktur dan mudah diuji di setiap tahap.

Export response as a Word file
Export response as a PDF file
oke gas

LET'S GOOOO 🔥🔥

Menurutku kita jangan bikin project ini kayak tugas kuliah. Kita bikin kayak software beneran yang kalau nanti Pak Rizal bilang "install di laptop saya", tinggal copy folder + import database + selesai.

Sprint 1 — Foundation (Hari 1)

Target hari ini:

✅ Database jalan

✅ Login jalan

✅ Dashboard kosong

✅ Folder rapi

Belum ada Import Excel dulu.

Final Tech Stack
Backend
PHP 8.x Native
Database
MySQL (MariaDB XAMPP)
Frontend
HTML5
CSS3 Native
Vanilla Javascript (ES6)
Library
PhpSpreadsheet
PDF.js
Font Awesome
Google Font Inter
Struktur Folder Final

Aku sedikit revisi supaya lebih enterprise.

JaspelAutomation/

│
├── app/
│   ├── config/
│   │      database.php
│   │      config.php
│   │
│   ├── controllers/
│   │
│   ├── models/
│   │
│   ├── helpers/
│   │
│   └── middleware/
│
├── assets/
│   ├── css/
│   │      style.css
│   │
│   ├── js/
│   │      app.js
│   │
│   ├── images/
│   │
│   └── icons/
│
├── modules/
│   │
│   ├── dashboard/
│   │
│   ├── master-dpjp/
│   │
│   ├── departments/
│   │
│   ├── import/
│   │
│   ├── export/
│   │
│   ├── history/
│   │
│   └── settings/
│
├── storage/
│   │
│   ├── raw/
│   │
│   ├── generated/
│   │
│   ├── temp/
│   │
│   └── logs/
│
├── vendor/
│
├── login.php
├── logout.php
├── index.php
├── composer.json
└── database.sql
Kenapa aku ubah uploads → storage?

Karena nanti kalau project berkembang

kita bisa punya

storage/

raw/

generated/

backup/

logs/

cache/

temp/

lebih profesional.

Database

Menurutku nama database jangan

test

atau

jaspel

Lebih bagus

rsgm_jaspel

atau

rsgm_jaspel_system

Aku lebih suka

rsgm_jaspel

simple.

Tabel

Kita mulai dengan 5 tabel saja.

users

departments

dpjp

settings

import_history

Belum perlu yang lain.

Login
Username

rizal

Password

admin123

Password nanti tetap di-hash menggunakan password_hash().

Jangan plaintext.

Dashboard

Aku sudah punya konsepnya.

+------------------------------------------------------+

Jaspel Automation System

Selamat datang Pak Rizal

--------------------------------------------------------

Total DPJP

Total Departemen

Import Terakhir

Total Import

--------------------------------------------------------

Quick Action

[ Import Excel ]

[ Master DPJP ]

[ Departemen ]

[ History ]

--------------------------------------------------------

Riwayat Terakhir

--------------------------------------------------------


Simple.

Clean.

UI

Aku pengen tampilannya seperti

Notion
Linear
Vercel

Bukan Bootstrap.

Warna
Primary

#2563EB

Background

#F8FAFC

Sidebar

#FFFFFF

Card

#FFFFFF

Border

#E5E7EB

Text

#111827
Font

Inter

Menurutku jauh lebih profesional.

Sidebar
Dashboard

Master DPJP

Departemen

Import Excel

History

Settings

Logout
Alur User
Login

↓

Dashboard

↓

Import Excel

↓

Processing

↓

Preview

↓

Download

↓

History
Coding Standard

Aku ingin dari awal kita disiplin.

Semua file PHP menggunakan pola yang sama:

<?php

declare(strict_types=1);

session_start();

require_once '../app/config/database.php';

Semua query menggunakan PDO Prepared Statement.

Tidak ada:

mysqli_query(...)

Tidak ada query yang langsung menggabungkan input pengguna.

Target Sprint 1

Hari ini selesai:

Struktur folder
Database
Composer + PhpSpreadsheet
Koneksi PDO
Login
Session
Dashboard kosong
CSS Global
Sidebar
Header
Footer

Sprint 2 — Master DPJP
Tujuan

Membuat halaman untuk mengelola data dokter (DPJP) beserta departemennya sebagai referensi utama pada proses import Excel.

Fungsi Utama

Admin dapat:

✅ Melihat daftar DPJP
✅ Menambah DPJP
✅ Mengedit DPJP
✅ Menghapus DPJP
✅ Mencari DPJP
✅ Filter berdasarkan Departemen
Database
Tabel departments
id
department_name
created_at
updated_at

Contoh

id	department_name
1	Bedah Mulut
2	Radiologi
3	IKGA
Tabel dpjp
id
doctor_name
department_id
created_at
updated_at

Contoh

id	doctor_name	department_id
1	dr. Andi	1
2	dr. Budi	2

Catatan: Lebih baik menggunakan department_id sebagai foreign key daripada menyimpan nama departemen langsung. Jika nama departemen berubah, cukup diubah sekali di tabel departments.

Tampilan Halaman
+----------------------------------------------------------+

Master DPJP

------------------------------------------------------------

Search : [_______________________]

Departemen :

[ Semua ▼ ]

                 + Tambah DPJP

------------------------------------------------------------

| Nama Dokter | Departemen | Aksi |

------------------------------------------------------------

| dr. A | Bedah Mulut | Edit Delete |

| dr. B | IKGA | Edit Delete |

| dr. C | Radiologi | Edit Delete |

------------------------------------------------------------
Modal Tambah DPJP
Nama Dokter

[________________]

Departemen

[ Pilih Departemen ▼ ]

--------------------------

Simpan

Batal
Modal Edit

Sama persis.

Data tinggal muncul otomatis.

Search

Realtime.

Misalnya

andi

langsung

dr. Andi

tanpa reload.

Filter

Dropdown

Semua

Bedah Mulut

Radiologi

IKGA
Delete

Aku lebih suka seperti ini.

Klik Delete

↓

Modal

Apakah yakin menghapus dokter ini?

dr. Andi

[Ya]

[Tidak]
Validasi

Nama dokter

Tidak boleh kosong.

Departemen

Harus dipilih.

Tidak boleh ada nama dokter yang sama dalam departemen yang sama.

Contoh

dr. A

Bedah Mulut

sudah ada

↓

tidak boleh dibuat lagi.

Kalau ternyata ada dokter dengan nama sama tetapi berada di departemen berbeda (misalnya kasus khusus), kita bisa diskusikan lagi dengan PIC.

Controller

Menurutku cukup dua file backend.

modules/master-dpjp/

index.php

api.php

api.php

Menangani

GET

POST

PUT

DELETE

Jadi satu endpoint.

Lebih rapi.

Javascript

Semua memakai

fetch()

Contoh

fetch("api.php?action=get")

Tambah

fetch("api.php?action=save")

Delete

fetch("api.php?action=delete")
UI

Aku ingin tampilannya seperti ini.

+--------------------------------+

Search

[____________________]

+ Tambah DPJP

----------------------------------

Nama Dokter

Departemen

Edit

Delete

----------------------------------

Simple.

Tidak terlalu banyak warna.

Warna

Card putih.

Border tipis.

Shadow halus.

Hover sedikit.

Tidak memakai tabel Excel klasik.

Empty State

Kalau belum ada data.

Belum ada data DPJP

Klik

+ Tambah DPJP

Jauh lebih bagus daripada tabel kosong.

Toast Notification

Saat berhasil.

✔ DPJP berhasil ditambahkan.

Saat edit.

✔ Data berhasil diperbarui.

Saat delete.

✔ Data berhasil dihapus.
Yang Aku Tambahkan (di luar requirement awal)

Ada satu fitur kecil tetapi menurutku akan sangat membantu.

Import DPJP dari Excel

Misalnya nanti Pak Rizal punya daftar dokter 200 orang.

Daripada input satu-satu.

Cukup

Import Excel

↓

Upload daftar dokter

↓

Selesai

Walaupun mungkin belum kita kerjakan sekarang, struktur databasenya sudah siap sehingga fitur ini bisa ditambahkan di masa depan tanpa mengubah arsitektur.

Sprint 3 — Master Departemen
Tujuan

Membangun modul untuk mengelola daftar departemen yang digunakan sebagai referensi pada seluruh sistem.

Master ini akan digunakan oleh:

Master DPJP
Processing Engine
Grouping Excel
Dashboard
History
Fungsi

Admin dapat:

✅ Melihat daftar departemen
✅ Menambah departemen
✅ Mengedit departemen
✅ Menghapus departemen
✅ Mencari departemen
Database

Tabel:

departments

Struktur

Field	Tipe
id	BIGINT
department_name	VARCHAR(100)
created_at	DATETIME
updated_at	DATETIME
Tampilan
---------------------------------------------------

Master Departemen

---------------------------------------------------

Search

[________________________]

                  + Tambah Departemen

---------------------------------------------------

Nama Departemen

Jumlah DPJP

Aksi

---------------------------------------------------

Bedah Mulut

12

Edit Delete

---------------------------------------------------

Radiologi

8

Edit Delete

---------------------------------------------------
Kenapa ada "Jumlah DPJP"?

Menurutku ini penting.

Misalnya tampil seperti ini

Departemen	Jumlah DPJP
Bedah Mulut	12
IKGA	15
Radiologi	8

Pak Rizal langsung tahu apakah suatu departemen sudah memiliki dokter atau belum.

Modal Tambah
Nama Departemen

[____________________]

-----------------------

Simpan

Batal
Validasi

Tidak boleh

Bedah Mulut

dibuat dua kali.

Edit

Admin cukup mengubah nama.

Misalnya

IKGA

↓

IKGA Anak

Maka semua DPJP yang terkait otomatis mengikuti karena menggunakan department_id.

Delete

Kalau departemen masih dipakai oleh DPJP

↓

Tidak boleh dihapus.

Muncul pesan

Departemen masih digunakan oleh 15 DPJP.

Silakan pindahkan atau hapus DPJP terlebih dahulu.

Ini menjaga integritas data.

Search

Realtime

Tanpa reload.

Empty State

Kalau belum ada data

Belum ada Departemen

Klik

Tambah Departemen
Dashboard Card

Nanti Dashboard tinggal mengambil

SELECT COUNT(*)
FROM departments;

Jadi card

Total Departemen

11

langsung otomatis.

API

Menurutku cukup

get

save

update

delete

dalam satu endpoint.

UI

Aku ingin tampilannya tetap konsisten.

Sidebar

↓

Master Departemen

↓

Card Putih

↓

Search

↓

Button Biru

↓

Table Modern
Fitur Tambahan

Aku ingin menambahkan satu kolom lagi.

Status

Misalnya

Aktif

atau

Nonaktif

Kenapa?

Kadang suatu departemen sudah tidak digunakan.

Daripada dihapus

↓

cukup

Nonaktif

Saat Import Excel

↓

Departemen nonaktif tidak muncul.

Jadi histori tetap aman.

Future Ready

Kalau nanti berkembang

Departemen bisa memiliki

kode departemen
warna
urutan tampil
kepala departemen

tanpa mengubah struktur besar.

Sprint 4 — Jaspel Processing Engine

Karena inilah yang membedakan aplikasi ini dari sekadar dashboard CRUD.

🎯 Tujuan Sprint 4

Membuat mesin otomatis yang mampu mengubah 1 file Excel input menjadi 1 file Excel output sesuai format RSGM.

Flow yang diinginkan:

Upload Excel
        │
        ▼
Validasi Template
        │
        ▼
Membaca Data Excel
        │
        ▼
Cleaning Data
        │
        ▼
Mapping DPJP → Departemen
        │
        ▼
Hitung Jaspel
        │
        ▼
Grouping per Departemen
        │
        ▼
Generate Workbook
        │
        ▼
Preview Hasil
        │
        ▼
Export Excel
Modul Baru

Aku menyarankan struktur folder seperti ini.

modules/

import/

    index.php

    upload.php

    preview.php

    process.php

    export.php

Supaya setiap proses memiliki tanggung jawab yang jelas.

1. Upload Excel

Tampilan sederhana namun modern.

──────────────────────────────

Import Data Jaspel

──────────────────────────────

Drag & Drop Excel

atau

[ Pilih File ]

Template :
.xlsx

Maksimal :
20 MB

──────────────────────────────

[ Upload ]

Setelah Upload

Belum langsung diproses.

Sistem membaca metadata dulu.

Misalnya

Nama File

IRJA Oktober.xlsx

Ukuran

3.2 MB

Jumlah Sheet

1

Jumlah Baris

3215

Tanggal Upload

05 Juli 2026

Kalau semua valid

↓

baru tombol

Proses
2. Validasi Template

Ini penting.

Sistem akan mengecek

✔ Extension

.xlsx

✔ Ukuran file

✔ Sheet pertama

✔ Nama kolom

Misalnya

Nama Pasien

DPJP

Tarif

dll

Kalau ada yang hilang

↓

Error.

3. Reading Excel

Menggunakan

PhpSpreadsheet

Bukan import ke database.

Melainkan langsung dibaca ke dalam array.

Contoh:

$data = [
    [
        "dpjp"=>"dr A",
        "tarif"=>500000,
        ...
    ],
    ...
];

Jadi proses jauh lebih cepat.

4. Cleaning Data

Mesin akan membersihkan data.

Misalnya

Spasi depan

Spasi belakang

Baris kosong

Kolom kosong

dibersihkan otomatis.

5. Mapping Engine

Ini yang paling penting.

Misalnya

Excel

dr. Budi

↓

Database

Master DPJP

↓

Ketemu

↓

Radiologi

Kalau tidak ketemu

↓

Masuk Error Report.

6. Calculation Engine

Business Rule

Jaspel

=

Tarif

×

20%

Catatan: Persentase 20% sebaiknya diambil dari tabel settings, bukan ditulis langsung di kode. Jadi kalau suatu saat berubah menjadi 25%, Pak Rizal cukup mengubah nilainya melalui menu Settings tanpa mengedit program.

7. Grouping Engine

Misalnya

3215 data

↓

dibagi menjadi

Bedah Mulut

Radiologi

IKGA

Konservasi

dll

Masing-masing menjadi satu koleksi data.

8. Workbook Generator

Ini mesin pembuat Excel.

Menggunakan

PhpSpreadsheet

Membuat

Workbook

↓

Sheet 1

Bedah Mulut

Sheet 2

Radiologi

Sheet 3

IKGA

dst

Sheet terakhir

Rekap
9. Preview

Aku ingin tampilannya seperti ini.

Import Berhasil

━━━━━━━━━━━━━━━━━━

File

IRJA Oktober.xlsx

━━━━━━━━━━━━━━━━━━

Jumlah Data

3215

━━━━━━━━━━━━━━━━━━

Departemen

11

━━━━━━━━━━━━━━━━━━

Dokter

84

━━━━━━━━━━━━━━━━━━

Total Tarif

Rp ...

━━━━━━━━━━━━━━━━━━

Total Jaspel

Rp ...

━━━━━━━━━━━━━━━━━━

Error

2

━━━━━━━━━━━━━━━━━━

[ Download ]

10. Download

Output

REKAP_JASPEL_OKTOBER_2026.xlsx

langsung siap dikirim ke bagian administrasi.

Error Report

Kalau ada

dr ABC

tidak ditemukan.

↓

Masuk

Error Report

Misalnya

Baris	Error
25	DPJP tidak ditemukan
91	Tarif kosong

Admin bisa memperbaiki data sumber tanpa kehilangan informasi.

Progress Bar

Aku sangat ingin fitur ini ada.

Uploading...

█████░░░░░░

25%

↓

Reading Excel

████████░░

60%

↓

Grouping

███████████

85%

↓

Generating Excel

██████████████

100%
Yang Menurutku Wajib Ditambahkan (Final Improvement)
1. Template Checker

Sebelum memproses, sistem membandingkan header file input dengan template resmi.

Jika ada kolom yang hilang, berubah nama, atau urutannya tidak sesuai, proses langsung dihentikan dengan pesan yang jelas. Ini mencegah hasil yang salah sejak awal.

2. Persentase Jaspel Dinamis

Jangan hardcode angka 20%.

Simpan di tabel settings.

JASPEL_PERCENTAGE

20

Kalau tahun depan berubah

↓

25

Pak Rizal tinggal mengubah satu nilai.

3. Ringkasan Hasil Sebelum Download

Selain total data dan total jaspel, tampilkan juga:

Jumlah baris berhasil diproses.
Jumlah baris yang gagal.
Daftar departemen yang terbentuk.
Nama file output yang akan dihasilkan.

Ini memberi keyakinan kepada admin sebelum mengunduh hasil.

4. Penamaan File Otomatis

Gunakan format yang konsisten, misalnya:

REKAP_JASPEL_<BULAN>_<TAHUN>.xlsx

Contoh:

REKAP_JASPEL_OKTOBER_2025.xlsx

Sehingga file mudah dicari dan arsip tetap rapi.

Sprint 5 — History Import & Audit Log
🎯 Tujuan

Menyimpan seluruh riwayat proses import sehingga setiap file yang pernah diproses dapat dilihat kembali, diunduh ulang, dan diaudit apabila terjadi kesalahan.

Dengan adanya fitur ini, admin tidak perlu memproses ulang file jika hanya ingin mengambil hasil export yang lama.

Alur Kerja
Upload Excel
      │
      ▼
Processing Engine
      │
      ▼
Generate Excel
      │
      ▼
Simpan History
      │
      ▼
History Import
Database Baru
Tabel import_history
Field	Tipe
id	BIGINT
file_name	VARCHAR(255)
output_file	VARCHAR(255)
total_rows	INT
success_rows	INT
failed_rows	INT
total_departments	INT
total_doctors	INT
total_jaspel	DECIMAL(15,2)
imported_by	BIGINT
created_at	DATETIME
Tabel import_errors
Field	Tipe
id	BIGINT
history_id	BIGINT
row_number	INT
doctor_name	VARCHAR(255)
error_message	TEXT

Dengan tabel terpisah, satu proses import bisa memiliki banyak error tanpa membuat tabel utama menjadi berantakan.

Tampilan Halaman
History Import

-------------------------------------------------------

Search

[____________________]

Tanggal

[ Semua ▼ ]

-------------------------------------------------------

Tanggal

Nama File

Berhasil

Gagal

Total Jaspel

Aksi

-------------------------------------------------------

05 Juli 2026

IRJA_OKT.xlsx

3210

5

Rp 215.000.000

Detail Download

-------------------------------------------------------
Detail Import

Saat tombol Detail ditekan.

Import Detail

----------------------------------

Nama File

IRJA_OKT.xlsx

----------------------------------

Tanggal

05 Juli 2026

----------------------------------

Total Data

3215

----------------------------------

Berhasil

3210

----------------------------------

Gagal

5

----------------------------------

Departemen

11

----------------------------------

Dokter

83

----------------------------------

Total Jaspel

Rp xxx.xxx.xxx

----------------------------------

[ Download Excel ]

[ Lihat Error ]
Error Viewer

Misalnya

Baris

Nama DPJP

Error

------------------------------------------------

35

dr A

Tidak ditemukan

------------------------------------------------

71

dr B

Tarif kosong

------------------------------------------------

95

dr C

Format angka salah

Kalau error sedikit,

Admin tinggal memperbaiki Excel.

Download Ulang

Ini menurutku penting.

Admin tidak perlu upload lagi.

Klik

Download

↓

langsung

REKAP_JASPEL_OKTOBER.xlsx
Statistik

Di halaman History.

Total Import

35

------------------

Total File

35

------------------

Total Data Diproses

125.000

------------------

Total Jaspel

Rp 5.4 M
Search

Cari berdasarkan

nama file
bulan
tahun

Misalnya

oktober

↓

langsung muncul semua

IRJA_OKTOBER

IGD_OKTOBER

RAWAT_INAP_OKTOBER
Filter

Dropdown

Semua

Hari Ini

Minggu Ini

Bulan Ini

Tahun Ini
Export History

Misalnya Pak Rizal ingin membuat laporan.

Klik

Export History

↓

keluar

history_import.xlsx
Konfirmasi Delete

History boleh dihapus.

Tetapi

Apakah yakin?

Data history akan dihapus.

File Excel hasil export tetap dihapus juga.

Ya

Tidak
Penyimpanan File

Menurutku struktur foldernya seperti ini.

storage/

imports/

    2026/

        07/

            input/

            output/

Misalnya

storage/

imports/

2026/

07/

input/

IRJA_OKTOBER.xlsx

output/

REKAP_JASPEL_OKTOBER.xlsx

Jadi file tidak menumpuk dalam satu folder dan mudah dicadangkan.

Audit Log

Selain History Import, aku menyarankan satu tabel lagi.

audit_logs
Aktivitas	Contoh
Login	Admin login
Logout	Admin logout
Import	Upload IRJA Oktober
Export	Download hasil Oktober
Delete	Hapus History

Ini sangat berguna kalau suatu saat ada pertanyaan seperti:

"Siapa yang menghapus history ini?"

Atau

"Kapan file ini diproses?"

Fitur Tambahan yang Aku Rekomendasikan
1. Riwayat Input & Output

Simpan dua file:

File Excel asli yang diunggah.
File Excel hasil proses.

Jadi kalau beberapa bulan kemudian ada perbedaan hasil, admin masih bisa membandingkan input dan output tanpa harus meminta file lama lagi.

2. Ringkasan Proses

Tambahkan informasi waktu proses:

Durasi Proses

3,4 detik

Ini membantu mengevaluasi performa aplikasi, terutama jika nanti ukuran file semakin besar.

3. Nomor Proses Otomatis

Setiap import memiliki ID unik, misalnya:

IMP-20260705-0001

ID ini memudahkan pencarian dan pencatatan dalam laporan.