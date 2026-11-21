# Dapur Th Sant

<p align="center">
  <img src="public/images/logo.png" alt="Logo Dapur Th Sant" width="140">
</p>

<h1 align="center">Dapur Th Sant</h1>
<p align="center"><strong>Sistem Informasi Katalog & Pemesanan Makanan UMKM</strong></p>

<p align="center">
  Laravel 12 &bull; PHP 8.2+ &bull; MySQL &bull; Blade &bull; Tailwind CSS
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
23. [Deployment dengan Docker](#23-deployment-dengan-docker)

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

### Panel Admin
| Fitur | Keterangan |
| --- |--- |
| Dashboard | Statistik pesanan baru, perlu verifikasi, selesai, dan total pemasukan |
| Kelola Produk | CRUD menu makanan, pengaturan stok, dan aktif/nonaktifkan menu |
| Kelola Pesanan | Menerima pesanan, memverifikasi bukti bayar, mengubah status (Diproses -> Selesai) |
| Cetak Struk | Generate struk pesanan siap cetak setelah pembayaran diverifikasi |
| Laporan Penjualan | Rekap penjualan pesanan selesai dan ekspor laporan ke CSV |

## 5. Aktor dan Hak Akses

| Aktor | Login | Hak akses |
| --- | --- | --- |
| Pelanggan | Tidak (Guest) | Lihat katalog, keranjang, checkout, unggah bukti, cek status via Order ID |
| Admin | Ya (`role = ADMIN`) | Operasional, data master produk, verifikasi pembayaran, dan cetak struk |
| Owner | Ya (`role = OWNER`) | Dashboard dan laporan penjualan |

Pembatasan hak akses dijalankan middleware `role` (`app/Http/Middleware/RoleMiddleware.php`).

## 6. Teknologi dan Versi

| Komponen | Versi | Sumber |
| --- | --- | --- |
| PHP | `^8.2` | `composer.json` |
| Laravel Framework | `^12.0` | `composer.json` |
| MySQL / MariaDB | Database `dapur_th_sant` | `.env`, migration |
| Blade | Template engine bawaan Laravel | `resources/views` |
| PHPUnit | `^11.5` | Pengujian otomatis |
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
├── bootstrap/app.php              Routing, alias middleware, exception handler
├── config/                        Konfigurasi Laravel
├── database/
│   ├── migrations/                Migration tabel users, products, orders, order_items, payments
│   └── seeders/                   DatabaseSeeder, UserSeeder, ProductSeeder
├── docs/                          Dokumentasi Sprint 3 dan Sprint 4
├── public/                        Document root: index.php, .htaccess, images
├── resources/views/               Seluruh tampilan Blade
├── routes/                        web.php, api.php
├── storage/                       Log, cache, bukti bayar privat (app/private), aset publik
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

Untuk seed akun awal di production, set `INITIAL_ADMIN_EMAIL`, `INITIAL_ADMIN_PASSWORD`, `INITIAL_OWNER_EMAIL`, dan `INITIAL_OWNER_PASSWORD` di environment server. Gunakan kata sandi unik dan kuat; jangan simpan nilainya di repository. Pendaftaran publik dinonaktifkan.

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
Pada environment `local`/`testing`, `DatabaseSeeder` membuat akun lokal dan data katalog contoh. Di environment lain, seeder tidak membuat katalog dummy; akun awal memerlukan `INITIAL_ADMIN_*` dan `INITIAL_OWNER_*` di environment server.

Jika database production sudah memiliki akun demo lama, login dengan password demo ditolak. Seeder tidak mereset akun yang emailnya sudah ada; siapkan email baru dan kredensial unik minimal 12 karakter melalui variabel `INITIAL_ADMIN_*`/`INITIAL_OWNER_*`, jalankan `php artisan db:seed`, lalu nonaktifkan akun demo lama.

## 15. Build Asset

Jika proyek menggunakan Vite untuk compile CSS/JS, jalankan:
```bash
npm install
npm run build
```

## 16. Menjalankan Aplikasi

```bash
php artisan storage:link
php artisan serve
```
Buka `http://127.0.0.1:8000`. Perintah `storage:link` menyiapkan akses gambar produk/logo publik. Bukti pembayaran disimpan privat dan hanya dapat dilihat admin yang login.

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
| `owner@umkm.com` | OWNER | `password123` |

*Catatan: Pelanggan tidak memerlukan akun untuk melakukan pemesanan (Guest Checkout).*
*Akun lokal di atas tidak boleh digunakan pada production. Jika akun default pernah dibuat di server production, ganti kata sandinya atau nonaktifkan sebelum rilis.*

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

**Status:** Belum diverifikasi pada server production.

Sebelum rilis, pastikan `APP_ENV=production`, `APP_DEBUG=false`, HTTPS aktif, `APP_KEY` unik, kredensial disimpan di environment server (bukan repository), dan backup database serta file private diuji. Pindahkan `public/receipts` dan `storage/app/public/receipts` ke `storage/app/private/receipts`, serta `storage/app/public/bukti_pembayaran` ke `storage/app/private/bukti_pembayaran`; pertahankan nama file agar referensi di database tetap berlaku.

Jika bukti pembayaran pernah di-commit atau dikirim ke repository bersama, penghapusan di commit terbaru tidak menghapus riwayat Git. Bersihkan riwayat secara terencana dan perlakukan bukti tersebut sebagai data yang mungkin sudah terekspos.

## 23. Deployment Docker & Coolify (Workshop 2026/2027)

Dockerfile menggunakan PHP 8.2 + Apache sesuai job sheet, dan membangun asset Vite. Untuk Tugas 1, image memakai SQLite sementara di dalam container sehingga bisa diuji dengan perintah `docker run` tunggal. Untuk deployment Coolify, gunakan database MySQL/MariaDB yang disediakan Coolify dan isi environment variables produksi.

### Tugas 1: build dan uji lokal

Jalankan dari root proyek setelah Docker Desktop aktif:

```bash
docker build -t ukk-rpl-tim-xx:v1 .
docker images
docker run --rm ukk-rpl-tim-xx:v1 php artisan key:generate --show
```

Salin nilai `APP_KEY` yang dicetak. Jalankan container dengan key tersebut:

```bash
docker run -d -p 8080:80 --name app-tim-xx -e APP_KEY="base64:ISI_KEY_DI_SINI" ukk-rpl-tim-xx:v1
docker ps
docker logs app-tim-xx
```

Buka `http://localhost:8080`, lalu ambil screenshot aplikasi dan hasil `docker ps`. Untuk menghentikan serta menghapus container setelah pengujian:

```bash
docker stop app-tim-xx
docker rm app-tim-xx
```

SQLite pada Tugas 1 bersifat sementara dan tidak untuk produksi. Jangan memakai nama container yang sama jika container sebelumnya masih ada.

### Tugas 2: deploy di Coolify

1. Buat Project di Coolify, lalu buat dan jalankan database MySQL/MariaDB.
2. Tambahkan repository GitHub sebagai aplikasi, pilih branch yang digunakan tim (`main` sesuai job sheet; branch lokal saat ini bernama `utama`), dan Build Pack `Dockerfile`. Branch Coolify dan branch yang menerima push harus sama.
3. Masukkan environment variables berikut di konfigurasi aplikasi Coolify (gunakan hostname internal database yang diberikan Coolify):

   ```env
   APP_NAME="Dapur Th Sant"
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:IsiDenganAppKeyYangDibuat
   APP_URL=https://domain-yang-diberikan-sekolah
   DB_CONNECTION=mysql
   DB_HOST=hostname-internal-database-coolify
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=user_database
   DB_PASSWORD=password_database
   SESSION_DRIVER=file
   CACHE_STORE=file
   QUEUE_CONNECTION=sync
   FILESYSTEM_DISK=local
   ```

4. Atur domain pada Coolify sesuai alamat dari sekolah, lalu Deploy. Setelah container berjalan, buka terminal aplikasi Coolify dan jalankan:

   ```bash
   php artisan migrate --force
   ```

   `storage:link` sudah dibuat saat image dibangun. Minta instruktur menyiapkan persistent storage untuk `/var/www/html/storage` agar upload tidak hilang saat redeploy.
5. Jalankan `php artisan db:seed --force` hanya jika kredensial akun awal sudah disiapkan sesuai bagian Seeder. Jangan gunakan akun demo sebagai akun produksi.

### Tugas 3: CI/CD

Workflow tersedia di `.github/workflows/deploy.yml` dan berjalan saat ada push ke branch `main` (sesuai job sheet) atau `utama` (nama branch lokal saat ini). Workflow membangun image untuk memeriksa Dockerfile. Agar job deploy ikut berjalan, tambahkan secret repository bernama `COOLIFY_WEBHOOK_URL` berisi webhook dari aplikasi Coolify (Settings → Secrets and variables → Actions). Tanpa secret tersebut, job build tetap berjalan dan deployment dilewati. Dengan jalur Coolify ini, secret SSH `VPS_HOST`, `VPS_USERNAME`, dan `VPS_SSH_KEY` tidak diperlukan.

`docker-compose.yml` disediakan hanya sebagai opsi menjalankan aplikasi dan MariaDB bersama di komputer sendiri; itu bukan jalur deployment workshop. Jalur workshop tetap build Dockerfile lokal, lalu deploy repository melalui Coolify.

Jangan commit `.env`, `.env.docker`, `APP_KEY`, atau kredensial database. Simpan semua secret produksi hanya di pengaturan Coolify/GitHub Secrets.

---

