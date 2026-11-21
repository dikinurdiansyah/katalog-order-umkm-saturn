Berikut adalah file `README.md` yang sudah **100% disesuaikan** dengan proyek **Dapur Th Sant** (Sistem Katalog & Pemesanan Makanan UMKM). 

Saya telah mengubah semua referensi "FixPoint/Laptop" menjadi konteks "Makanan/Pemesanan", menyesuaikan nama tabel, fitur, dan alur kerja agar sesuai dengan proyek kamu. 

Tinggal **Copy** dan **Paste** langsung ke file `README.md` di root repository kamu.

---

```markdown
# Dapur Th Sant

<p align="center">
  <img src="public/images/logo.png" alt="Logo Dapur Th Sant" width="140">
</p>

<h1 align="center">Dapur Th Sant</h1>
<p align="center"><strong>Sistem Informasi Katalog & Pemesanan Makanan UMKM</strong></p>

<p align="center">
  Laravel 11 &bull; PHP 8.2+ &bull; MySQL &bull; Blade &bull; Tailwind CSS
</p>

---

## Daftar Isi

1. [Deskripsi Aplikasi](#1-deskripsi-aplikasi)
2. [Latar Belakang Masalah](#2-latar-belakang-masalah)
3. [Tujuan Sistem](#3-tujuan-sistem)
4. [Fitur Utama](#4-fitur-utama)
5. [Aktor dan Hak Akses](#5-aktor-dan-hak-akses)
6. [Teknologi dan Versi](#6-teknologi-dan-versi)
7. [Arsitektur Sistem](#7-arsitektur-sistem)
8. [Struktur Direktori](#8-struktur-direktori)
9. [Persyaratan Instalasi](#9-persyaratan-instalasi)
10. [Instalasi Lokal](#10-instalasi-lokal)
11. [Konfigurasi Environment](#11-konfigurasi-environment)
12. [Konfigurasi Database](#12-konfigurasi-database)
13. [Migration](#13-migration)
14. [Seeder](#14-seeder)
15. [Build Asset](#15-build-asset)
16. [Menjalankan Aplikasi](#16-menjalankan-aplikasi)
17. [Akun Uji Coba](#17-akun-uji-coba)
18. [Pengujian Otomatis](#18-pengujian-otomatis)
19. [Menjalankan Tanpa Internet (Offline LAN)](#19-menjalankan-tanpa-internet-offline-lan)
20. [Tim Pengembang](#20-tim-pengembang)
21. [Struktur Dokumentasi](#21-struktur-dokumentasi)
22. [Status Deployment](#22-status-deployment)

---

## 1. Deskripsi Aplikasi

**Dapur Th Sant** adalah aplikasi web monolitik untuk mengelola operasional UMKM kuliner. Aplikasi menangani alur kerja mulai dari pelanggan melihat katalog menu, memasukkan pesanan ke keranjang, melakukan checkout (tanpa wajib registrasi), mengunggah bukti pembayaran, hingga admin memverifikasi pembayaran, memperbarui status pesanan, dan mencetak struk.

Pelanggan **tidak perlu membuat akun** untuk memesan (Guest Checkout). Setiap pesanan mendapat Order ID unik yang bisa digunakan pelanggan untuk mengecek status pesanan mereka. Admin masuk melalui halaman login khusus.

## 2. Latar Belakang Masalah

Pencatatan pesanan makanan secara manual via chat WhatsApp dan buku catatan menimbulkan masalah berulang di UMKM:

- Pelanggan harus berulang kali menanyakan status pesanan mereka.
- Risiko salah catat pesanan atau lupa mencatat pembayaran.
- Stok bahan atau menu habis tidak terdeteksi secara real-time, menyebabkan kecewa pelanggan.
- Laporan pemasukan bulanan harus dihitung ulang secara manual.

Dapur Th Sant menyatukan seluruh catatan tersebut dalam satu basis data terpusat sehingga setiap pihak melihat data yang sama dan akurat.

## 3. Tujuan Sistem

1. Menyediakan kanal pemesanan daring yang cepat tanpa wajib registrasi akun (Guest Checkout).
2. Memberi identitas unik (Order ID) pada setiap pesanan untuk pengecekan status.
3. Menyimpan data menu, stok, detail pesanan, dan pembayaran secara terpusat.
4. Mencatat pembayaran Transfer/QRIS beserta bukti pembayarannya.
5. Menyediakan laporan riwayat penjualan yang dapat diekspor ke PDF dan Excel.

## 4. Fitur Utama

### Halaman Pelanggan (Guest)
| Fitur | Keterangan |
| --- | --- |
| Katalog Menu | Menampilkan daftar makanan dengan foto, deskripsi, harga, dan status stok |
| Keranjang Belanja | Menambah, mengurangi, atau menghapus item sebelum checkout |
| Checkout & Pembayaran | Formulir data diri singkat, pilihan metode pembayaran, dan unggah bukti transfer (JPG/PNG/WEBP, maks 2 MB) |
| Cek Status Order | Mencari status pesanan berdasarkan Order ID unik |
| Pembatalan | Pembatalan pesanan oleh pelanggan (dengan syarat tertentu) |

### Panel Admin
| Fitur | Keterangan |
| --- |--- |
| Dashboard | Statistik pesanan baru, perlu verifikasi, selesai, dan total pemasukan |
| Kelola Produk | CRUD menu makanan, pengaturan stok, dan aktif/nonaktifkan menu |
| Kelola Pesanan | Menerima pesanan, memverifikasi bukti bayar, mengubah status (Diproses -> Selesai) |
| Cetak Struk | Generate struk pesanan siap cetak setelah pembayaran diverifikasi |
| Laporan Penjualan | Filter rentang tanggal, pencarian, dan ekspor laporan ke PDF/Excel |

## 5. Aktor dan Hak Akses

| Aktor | Login | Hak akses |
| --- | --- | --- |
| Pelanggan | Tidak (Guest) | Lihat katalog, keranjang, checkout, unggah bukti, cek status via Order ID |
| Admin | Ya (`role = ADMIN`) | Seluruh operasional, data master produk, verifikasi pembayaran, laporan, cetak struk |

Pembatasan hak akses dijalankan middleware `role` (`app/Http/Middleware/RoleMiddleware.php`).

## 6. Teknologi dan Versi

| Komponen | Versi | Sumber |
| --- | --- | --- |
| PHP | `^8.2` | `composer.json` |
| Laravel Framework | `^11.0` | `composer.json` |
| MySQL / MariaDB | Database `dapur_th_sant` | `.env`, migration |
| Blade | Template engine bawaan Laravel | `resources/views` |
| barryvdh/laravel-dompdf | `^2.0` | Ekspor PDF laporan & struk |
| PHPUnit | `^10.5` | Pengujian otomatis |
| Composer | 2.x | `composer -V` |

*Catatan: Aplikasi ini menggunakan CDN untuk styling (Tailwind/Bootstrap) atau build Vite minimal, sehingga tidak mewajibkan `npm run build` untuk deployment dasar.*

## 7. Arsitektur Sistem

```
Browser (HTML/CSS/JS dari Blade)
        |  HTTP: GET/POST (session + CSRF)
        v
public/index.php  ->  bootstrap/app.php (routing, middleware, exception handler)
        |
        v
routes/web.php  --(middleware auth, role)-->  Controller
        |                                          |
        |                                          +--> Model / Query Builder --> MySQL
        v
resources/views (Blade)  -->  HTML response
```

- **Model** (`app/Models`) memetakan tabel MySQL (`Product`, `Order`, `OrderItem`, `Payment`, `User`).
- **Controller** (`app/Http/Controllers`) memvalidasi input, menjalankan query, dan membungkus operasi multi-tabel (checkout) dengan `DB::transaction()` untuk mencegah stok minus.
- **View** (`resources/views`) berisi Blade dengan CSS dan JavaScript minimal untuk interaksi UI.
- **Middleware** `role` membatasi area Admin.

## 8. Struktur Direktori

```
dapur-th-sant/
├── app/
│   ├── Http/Controllers/          ProductController, OrderController, AdminController
│   ├── Http/Middleware/           RoleMiddleware
│   ├── Models/                    Product, Order, OrderItem, Payment, User
│   └── Support/                   Helper untuk generate PDF/Excel
├── bootstrap/app.php              Routing, alias middleware, exception handler
├── config/                        Konfigurasi Laravel
├── database/
│   ├── migrations/                Migration tabel users, products, orders, order_items, payments
│   └── seeders/                   DatabaseSeeder, UserSeeder, ProductSeeder
├── docs/                          Dokumentasi Sprint 3 dan Sprint 4
├── public/                        Document root: index.php, .htaccess, images
├── resources/views/               Seluruh tampilan Blade
├── routes/                        web.php, api.php
├── storage/                       Log, cache, dan unggahan (app/public)
├── tests/                         Pengujian fitur dan unit
├── artisan
├── composer.json
├── run_local_offline.bat          Menjalankan aplikasi untuk LAN tanpa internet
└── README.md
```

## 9. Persyaratan Instalasi

| Kebutuhan | Versi minimal |
| --- | --- |
| PHP | 8.2 |
| Ekstensi PHP | `openssl`, `pdo`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo` |
| Composer | 2.x |
| MySQL / MariaDB | 5.7+ / 10.3+ |
| Git | 2.x |

## 10. Instalasi Lokal

```bash
git clone https://github.com/dikinurdiansyah/katalog-order-umkm-saturn.git
cd dapur-th-sant
composer install
copy .env.example .env   # Gunakan 'cp .env.example .env' jika di Git Bash/Linux/Mac
php artisan key:generate
```

Kemudian sesuaikan `.env` (lihat bagian 11 dan 12), buat database, lalu jalankan migration dan seeder.

## 11. Konfigurasi Environment

Nilai minimal yang harus benar pada `.env`:

```env
APP_NAME="Dapur Th Sant"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dapur_th_sant
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
```
*Penting: Gunakan `SESSION_DRIVER=file` dan `CACHE_STORE=file` agar tidak error jika tabel `sessions` atau `cache` belum di-migrate.*

## 12. Konfigurasi Database

```sql
CREATE DATABASE dapur_th_sant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada `.env`, lalu uji:
```bash
php artisan migrate:status
```

## 13. Migration

```bash
php artisan migrate
```
Menjalankan migration untuk tabel: `users`, `products`, `orders`, `order_items`, `payments`.

> Jangan memakai `migrate:fresh` pada database yang sudah berisi data produksi.

## 14. Seeder

```bash
php artisan db:seed
```
`DatabaseSeeder` akan membuat:
- Akun Admin default.
- Beberapa data produk contoh (Dimsum, Bolu, dll) untuk keperluan pengujian.

## 15. Build Asset

Jika proyek menggunakan Vite untuk compile CSS/JS, jalankan:
```bash
npm install
npm run build
```
*(Jika menggunakan CDN untuk Tailwind/Bootstrap, langkah ini dapat dilewati).*

## 16. Menjalankan Aplikasi

```bash
php artisan storage:link
php artisan serve
```
Buka `http://127.0.0.1:8000`. Perintah `storage:link` diperlukan agar berkas bukti pembayaran dapat diakses dari browser.

### Mode offline (tanpa internet)
```bash
run_local_offline.bat
```
Skrip ini akan membersihkan cache, menyiapkan storage, menampilkan IP LAN, dan menjalankan `php artisan serve --host 0.0.0.0 --port 8000` agar bisa diakses perangkat lain dalam satu jaringan WiFi.

## 17. Akun Uji Coba

Akun berikut **hanya untuk pengembangan lokal** dan dibuat oleh `DatabaseSeeder`:

| Email | Role | Password |
| --- | --- | --- |
| `admin@umkm.com` | ADMIN | `password` |
| `owner@umkm.com` | owner | `password` |

*Catatan: Pelanggan tidak memerlukan akun untuk melakukan pemesanan (Guest Checkout).*

## 18. Pengujian Otomatis

```bash
php artisan test
```
Pengujian mencakup: validasi form checkout, pengurangan stok saat transaksi (`DB::transaction`), alur status order, dan hak akses middleware.

## 19. Menjalankan Tanpa Internet (Offline LAN)

Untuk presentasi tanpa internet, jalankan `run_local_offline.bat` pada laptop, lalu buka alamat IP LAN yang ditampilkan skrip dari perangkat lain (HP/Laptop Penguji) pada WiFi/hotspot yang sama. Rincian lengkap ada di `docs/02-deployment/OFFLINE_DEPLOYMENT.md`.

## 20. Tim Pengembang

| Nama | Peran | Tanggung jawab utama |
| --- | --- | --- |
| [Nama Kamu] | Backend | Database, migration, model, controller, validasi, transaksi database, laporan, pengujian |
| [Nama Partner] | Frontend | Layout Blade, form, integrasi route, CSS, responsivitas, tampilan cetak struk |

## 21. Struktur Dokumentasi

```
docs/
├── 01-release/
│   └── BUG_FIXING_LOG.md                     Log perbaikan bug dan status Code Freeze
├── 02-deployment/
│   └── OFFLINE_DEPLOYMENT.md                 Fallback lokal tanpa internet
├── 03-manual/
│   ├── USER_MANUAL.pdf                       Manual pengguna
│   └── TECHNICAL_DOCS.pdf                    Dokumentasi teknis (ERD, Arsitektur)
├── 04-api/
│   └── API_DOCUMENTATION.md                  Data contract route web
├── 05-defense/
│   └── INDIVIDUAL_DEFENSE_CHECKLIST.pdf      Checklist pertanggungjawaban individu
└── sprint-3/                                 Artefak Sprint 3
```

## 22. Status Deployment

**Status:** Siap untuk Deployment / Sudah di-deploy ke [ArenHost / Railway / InfinityFree].
Aplikasi telah dioptimalkan untuk production (`composer install --no-dev --optimize-autoloader`, `php artisan optimize`). Prosedur unggah dan konfigurasi production tersedia di folder `docs/02-deployment/`.

---
```

### 💡 Tips Sebelum Push:
1. Ganti `[Diki Nurdiansyah]` dan `[Fauzan Aufaa]` dengan nama asli kalian.
2. Ganti `https://github.com/dikinurdiansyah/katalog-order-umkm-saturn.git` dengan link repository GitHub kamu yang sebenarnya.
3. Pastikan folder `public/images/` ada dan berisi file `logo.png` (atau hapus bagian `<img>` di bagian atas jika belum ada logo).
4. File ini sudah sangat rapi dan memenuhi standar dokumentasi Sprint 4 UKK RPL. 

