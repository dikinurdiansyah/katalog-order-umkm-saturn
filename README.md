# 🍽️ Dapur Th Sant - Sistem Informasi Katalog & Pemesanan Makanan UMKM

<div align="center">

[![Release](https://img.shields.io/badge/Release-v1.0.0--rc-0D9488?style=for-the-badge)](https://github.com/dikinurdiansyah/katalog-order-umkm-saturn/releases)
[![Build Status](https://img.shields.io/badge/Build-Passing-10B981?style=for-the-badge)](https://github.com/dikinurdiansyah/katalog-order-umkm-saturn/actions)
[![License](https://img.shields.io/badge/License-MIT-slate?style=for-the-badge)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com/)

**Proyek Uji Kompetensi Keahlian (UKK) RPL 2026/2027**  
**SMK Budi Bakti Ciwidey**  
**Tema:** Local Digital Solution untuk UMKM Kuliner

</div>

---

## 📋 Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Arsitektur Sistem](#arsitektur-sistem)
- [Panduan Instalasi](#panduan-instalasi)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Penggunaan](#penggunaan)
- [API Documentation](#api-documentation)
- [Database Schema](#database-schema)
- [Testing](#testing)
- [Deployment](#deployment)
- [Kontribusi](#kontribusi)
- [Tim Pengembang](#tim-pengembang)
- [Lisensi](#lisensi)
- [Kontak](#kontak)

---

## 🎯 Tentang Proyek

**Dapur Th Sant** adalah sistem informasi katalog dan pemesanan makanan yang dirancang khusus untuk membantu UMKM kuliner dalam mengelola pesanan secara digital. Proyek ini dikembangkan sebagai bagian dari Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak tahun 2026/2027.

### Latar Belakang

Proses pemesanan di UMKM Dapur Th Sant sebelumnya masih menggunakan pencatatan manual via chat WhatsApp dan buku catatan, yang menyebabkan:
- ❌ Salah catat pesanan
- ❌ Lupa konfirmasi pembayaran
- ❌ Stok menipis tidak terdeteksi real-time
- ❌ Rekap penjualan manual yang rentan error
- ❌ Kehilangan potensi pendapatan saat pesanan ramai

### Solusi

Dapur Th Sant hadir sebagai solusi digital terintegrasi yang menyediakan:
- ✅ Katalog menu digital dengan foto & harga transparan
- ✅ Keranjang belanja & checkout instan
- ✅ Upload bukti transfer (Bank/QRIS/E-Wallet)
- ✅ Admin dashboard untuk verifikasi pesanan
- ✅ Cetak struk thermal otomatis
- ✅ Laporan penjualan harian/mingguan/bulanan
- ✅ Guest checkout (tanpa wajib login)

---

## ✨ Fitur Utama

### 🛒 Untuk Pelanggan
- **Katalog Menu Digital** - Tampilan produk yang menarik dengan foto, deskripsi, dan harga
- **Keranjang Belanja** - Tambah/hapus item, atur jumlah, lihat total harga real-time
- **Checkout Mudah** - Form identitas sederhana, pilihan metode pembayaran
- **Upload Bukti Bayar** - Upload foto struk transfer dengan validasi format
- **Tracking Pesanan** - Pantau status pesanan (Menunggu Verifikasi → Diproses → Selesai)
- **Guest Checkout** - Pesan tanpa perlu membuat akun

### 👨‍🍳 Untuk Admin/Pengelola
- **Dashboard Overview** - Ringkasan pesanan, pendapatan, stok menipis
- **Manajemen Produk** - CRUD produk, kelola stok, upload foto
- **Verifikasi Pesanan** - Lihat bukti bayar, approve/reject pesanan
- **Update Status** - Ubah status pesanan (Pending → Processing → Completed)
- **Cetak Struk** - Generate struk thermal (58mm/80mm) untuk setiap pesanan
- **Laporan Penjualan** - Rekap transaksi dengan export PDF/Excel
- **Manajemen Kategori** - Kelola kategori produk

### 🔒 Keamanan
- Password hashing dengan Bcrypt
- CSRF protection di semua form
- SQL injection prevention (Eloquent ORM)
- File upload validation (whitelist MIME type)
- Role-based access control (RBAC)
- HTTPS enforcement di production

---

## 🛠️ Tech Stack

### Backend
- **PHP 8.2** - Bahasa pemrograman utama
- **Laravel 11** - Framework MVC modern
- **MySQL/MariaDB** - Database relasional
- **Eloquent ORM** - Object-relational mapping

### Frontend
- **Blade Templating** - Template engine Laravel
- **Tailwind CSS** - Utility-first CSS framework
- **Vanilla JavaScript** - Interaktivitas frontend
- **FontAwesome** - Icon library

### Tools & Libraries
- **Composer** - Dependency manager PHP
- **NPM** - Package manager JavaScript
- **Vite** - Build tool modern
- **Intervention Image** - Image processing
- **Barryvdh DomPDF** - PDF generation

### Deployment
- **ArenHost** - Shared hosting (Production)
- **Railway** - Cloud PaaS (Staging)
- **Git & GitHub** - Version control

---

## 🏗️ Arsitektur Sistem

### Arsitektur MVC

```
┌─────────────┐     ┌──────────────┐     ┌─────────────┐
│   Browser   │────▶│  Controller  │────▶│    Model    │
│  (Client)   │◀────│   (Logic)    │◀────│  (Database) │
└─────────────┘     └──────────────┘     └─────────────┘
                           │
                           ▼
                    ┌──────────────┐
                    │    View      │
                    │   (Blade)    │
                    └──────────────┘
```

### Alur Pemesanan

```
Pelanggan → Katalog → Keranjang → Checkout → Upload Bukti → Menunggu Verifikasi
                                                                    ↓
Admin ← Cetak Struk ← Selesai ← Diproses ← Verifikasi Bukti ← Admin
```

---

## 📦 Panduan Instalasi

### Prasyarat Sistem

Sebelum memulai, pastikan Anda memiliki:
- PHP ≥ 8.2
- Composer
- MySQL/MariaDB ≥ 10.4
- Node.js & NPM (opsional, untuk compile asset)
- Git

### Langkah Instalasi

1. **Clone repository**
```bash
git clone https://github.com/dikinurdiansyah/katalog-order-umkm-saturn.git
cd katalog-order-umkm-saturn
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Konfigurasi database**

Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dapurthsant_db
DB_USERNAME=root
DB_PASSWORD=
```

5. **Jalankan migrasi dan seeder**
```bash
php artisan migrate:fresh --seed
```

6. **Buat storage symlink**
```bash
php artisan storage:link
```

7. **Jalankan development server**
```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

---

## ⚙️ Konfigurasi Environment

### File `.env.example`

```env
APP_NAME="Dapur Th Sant"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dapurthsant_db
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@dapurthsant.my.id"
MAIL_FROM_NAME="${APP_NAME}"
```

### Konfigurasi Production

Untuk deployment production, ubah konfigurasi berikut:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dapurthsant.my.id
```

---

## 🚀 Penggunaan

### Akun Default

Setelah menjalankan seeder, gunakan kredensial berikut untuk testing:

| Role | Email | Password |
|------|-------|----------|
| **Pelanggan** | pelanggan@dapurthsant.my.id | password123 |
| **Admin** | admin@dapurthsant.my.id | password123 |

### Alur Penggunaan

#### Sebagai Pelanggan:
1. Buka website `https://dapurthsant.my.id`
2. Browse katalog menu
3. Tambah item ke keranjang
4. Klik "Checkout"
5. Isi data diri (nama, nomor WA, alamat)
6. Pilih metode pembayaran
7. Upload bukti transfer
8. Tunggu verifikasi admin
9. Lihat status pesanan di "Riwayat Pesanan"

#### Sebagai Admin:
1. Login dengan akun admin
2. Lihat dashboard untuk overview
3. Buka menu "Antrean Pesanan"
4. Verifikasi bukti pembayaran
5. Update status pesanan
6. Cetak struk jika diperlukan
7. Lihat laporan penjualan

---

## 📡 API Documentation

### Base URL
```
https://dapurthsant.my.id/api
```

### Authentication

#### POST /api/login
Login pengguna

**Request Body:**
```json
{
  "email": "pelanggan@dapurthsant.my.id",
  "password": "password123"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "pelanggan@dapurthsant.my.id",
      "role": "customer"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

### Products

#### GET /api/products
Mendapatkan daftar semua produk

**Query Parameters:**
- `category` (optional): Filter berdasarkan kategori
- `search` (optional): Pencarian berdasarkan nama
- `page` (optional): Nomor halaman (default: 1)

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Dimsum Ayam (10 pcs)",
      "description": "Dimsum ayam premium dengan bumbu rahasia",
      "price": 15000,
      "stock": 50,
      "category": "Snack",
      "image": "https://dapurthsant.my.id/storage/products/dimsum.jpg"
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 5,
    "total_items": 50
  }
}
```

### Orders

#### POST /api/orders
Membuat pesanan baru (checkout)

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    },
    {
      "product_id": 3,
      "quantity": 1
    }
  ],
  "customer_name": "Budi Santoso",
  "customer_phone": "081234567890",
  "delivery_address": "Jl. Merdeka No. 123, Ciwidey"
}
```

**Response 201:**
```json
{
  "success": true,
  "message": "Pesanan berhasil dibuat",
  "data": {
    "order_code": "ORD-20261115-001",
    "total_amount": 45000,
    "status": "pending",
    "items": [
      {
        "product_name": "Dimsum Ayam (10 pcs)",
        "quantity": 2,
        "price": 15000,
        "subtotal": 30000
      }
    ]
  }
}
```

### Payments

#### POST /api/orders/{id}/payment
Upload bukti pembayaran

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
- `payment_proof`: File gambar (JPG/PNG, max 2MB)

**Response 200:**
```json
{
  "success": true,
  "message": "Bukti pembayaran berhasil diupload",
  "data": {
    "order_code": "ORD-20261115-001",
    "payment_proof": "https://dapurthsant.my.id/storage/payments/bukti1.jpg",
    "status": "pending_verification"
  }
}
```

### Reports (Admin Only)

#### GET /api/reports/sales
Laporan penjualan

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `period`: daily, weekly, monthly
- `date_from`: 2026-11-01
- `date_to`: 2026-11-30

**Response 200:**
```json
{
  "success": true,
  "data": {
    "period": "monthly",
    "total_transactions": 150,
    "total_revenue": 2250000,
    "top_products": [
      {
        "product_name": "Dimsum Ayam (10 pcs)",
        "quantity_sold": 80,
        "revenue": 1200000
      }
    ],
    "daily_breakdown": [
      {
        "date": "2026-11-01",
        "transactions": 5,
        "revenue": 75000
      }
    ]
  }
}
```

---

## 🗄️ Database Schema

### Tabel Utama

#### users
```sql
- id (PK)
- name
- email (unique)
- password (hashed)
- role (customer/admin)
- phone
- address
- created_at
- updated_at
```

#### products
```sql
- id (PK)
- name
- description
- price (decimal)
- stock (integer)
- category
- image
- is_available (boolean)
- created_at
- updated_at
```

#### orders
```sql
- id (PK)
- user_id (FK)
- order_code (unique)
- total_amount (decimal)
- status (pending/processing/completed/rejected)
- payment_proof
- notes
- created_at
- updated_at
```

#### order_items
```sql
- id (PK)
- order_id (FK)
- product_id (FK)
- quantity
- price (decimal)
- subtotal (decimal)
- created_at
- updated_at
```

#### payments
```sql
- id (PK)
- order_id (FK)
- amount (decimal)
- payment_method
- status
- proof_image
- verified_at
- created_at
- updated_at
```

### Relasi Database

```
users (1) ────< (N) orders
orders (1) ────< (N) order_items
products (1) ────< (N) order_items
orders (1) ────< (1) payments
```

---

## 🧪 Testing

### Menjalankan Tests

```bash
# Jalankan semua tests
php artisan test

# Jalankan dengan coverage
php artisan test --coverage

# Jalankan specific test
php artisan test --filter=OrderTest
```

### Test Coverage

- ✅ Unit Tests - Model & Logic
- ✅ Feature Tests - API Endpoints
- ✅ Browser Tests - User Flows
- ✅ Integration Tests - Database

---

## 🚢 Deployment

### Production (ArenHost)

1. Upload file ke `public_html`
2. Set Document Root = `/public_html/public`
3. Konfigurasi `.env` production
4. Jalankan commands:
```bash
composer install --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

### Staging (Railway)

1. Connect GitHub repository
2. Set environment variables
3. Auto-deploy on push

### Offline Fallback

Untuk mode offline (LAN):
```bash
php artisan serve --host 0.0.0.0 --port 8000
```

Akses via: `http://[IP-LAPTOP]:8000`

---

## 🤝 Kontribusi

Kami sangat menghargai kontribusi dari komunitas! Berikut cara berkontribusi:

1. **Fork** repository ini
2. **Create branch** fitur baru (`git checkout -b feature/AmazingFeature`)
3. **Commit** perubahan (`git commit -m 'Add some AmazingFeature'`)
4. **Push** ke branch (`git push origin feature/AmazingFeature`)
5. **Open Pull Request**

### Coding Standards

- Gunakan PSR-12 untuk PHP
- Gunakan ESLint untuk JavaScript
- Write tests untuk fitur baru
- Update dokumentasi jika diperlukan

---

## 👥 Tim Pengembang

| Nama | Role | NISN | Kontribusi |
|------|------|------|------------|
| **Diki Nurdiansyah** | Backend Developer & DB Admin | [NISN] | Arsitektur database, API controller, logika bisnis, deployment |
| **[Nama Partner]** | Frontend Developer & UI/UX | [NISN] | Desain antarmuka, integrasi view, user manual, testing |

**Pembimbing UKK:** [Nama Guru Pembimbing], S.Kom.

**SMK Budi Bakti Ciwidey**  
**Tahun Ajaran 2026/2027**

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

```
MIT License

Copyright (c) 2026 Diki Nurdiansyah

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 📞 Kontak

- **Email:** support@dapurthsant.my.id
- **WhatsApp:** 08xx-xxxx-xxxx
- **Website:** [https://dapurthsant.my.id](https://dapurthsant.my.id)
- **GitHub Issues:** [Laporkan Bug](https://github.com/dikinurdiansyah/katalog-order-umkm-saturn/issues)

---

## 🗺️ Roadmap

- [ ] Integrasi WhatsApp API untuk notifikasi otomatis
- [ ] Payment gateway (Midtrans/Xendit) untuk pembayaran otomatis
- [ ] Aplikasi mobile (Android/iOS)
- [ ] Multi-bahasa (Indonesia/English)
- [ ] Fitur loyalty program & voucher diskon
- [ ] Integrasi dengan platform delivery (GoFood/GrabFood)
- [ ] Real-time order tracking dengan maps
- [ ] Analytics dashboard untuk admin

---

## 🙏 Acknowledgments

- Laravel Framework
- Tailwind CSS
- FontAwesome
- SMK Budi Bakti Ciwidey
- Pembimbing UKK RPL 2026/2027

---

<div align="center">

**Dibuat dengan ❤️ untuk UKK RPL 2026/2027**

*"Teknologi sederhana, dampak luar biasa"*

</div>
