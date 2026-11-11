<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== 1. USERS (Admin & Owner) =====
        User::updateOrCreate(
            ['email' => 'admin@umkm.com'],
            [
                'name' => 'Admin Dapur',
                'password_hash' => Hash::make('password'),
                'role' => 'ADMIN',
                'phone_number' => '081234567890',
            ]
        );

        User::updateOrCreate(
            ['email' => 'owner@umkm.com'],
            [
                'name' => 'Owner Dapur',
                'password_hash' => Hash::make('password'),
                'role' => 'OWNER',
                'phone_number' => '081234567891',
            ]
        );

        // ===== 2. CATEGORIES (Spesifik Dimsum) =====
        $dimsumKukus = Category::firstOrCreate(['name' => 'Dimsum Kukus'], ['slug' => 'dimsum-kukus']);
        $dimsumGoreng = Category::firstOrCreate(['name' => 'Dimsum Goreng'], ['slug' => 'dimsum-goreng']);
        $frozenPack = Category::firstOrCreate(['name' => 'Frozen Pack'], ['slug' => 'frozen-pack']);
        $minumanSegar = Category::firstOrCreate(['name' => 'Minuman Segar'], ['slug' => 'minuman-segar']);

        // ===== 3. PRODUCTS (Data Realistis) =====
        // ✅ PERBAIKAN: Semua 'category_id' diganti menjadi 'id_category'
        $products = [
            // Dimsum Kukus
            ['id_category' => $dimsumKukus->id, 'name' => 'Dimsum Ayam Original', 'price' => 15000, 'stock' => 50, 'description' => 'Dimsum kukus lembut dengan isian daging ayam pilihan dan wortel segar.'],
            ['id_category' => $dimsumKukus->id, 'name' => 'Dimsum Ayam Udang Premium', 'price' => 18000, 'stock' => 45, 'description' => 'Perpaduan daging ayam dan udang segar, tekstur lebih kenyal dan gurih.'],
            ['id_category' => $dimsumKukus->id, 'name' => 'Siomay Bandung Spesial', 'price' => 15000, 'stock' => 40, 'description' => 'Siomay khas Bandung dengan bumbu kacang yang gurih dan manis.'],

            // Dimsum Goreng
            ['id_category' => $dimsumGoreng->id, 'name' => 'Dimsum Mentai Lumer', 'price' => 22000, 'stock' => 30, 'description' => 'Dimsum goreng renyah dengan lelehan saus mentai premium di atasnya. Best Seller!'],
            ['id_category' => $dimsumGoreng->id, 'name' => 'Dimsum Keju Mozarella', 'price' => 20000, 'stock' => 8, 'description' => 'Dimsum goreng dengan isian keju mozarella yang bisa ditarik panjang.'], // ⚠️ Stok menipis untuk demo
            ['id_category' => $dimsumGoreng->id, 'name' => 'Lumpia Udang Crispy', 'price' => 18000, 'stock' => 35, 'description' => 'Kulit lumpia super renyah dengan isian udang cincang yang melimpah.'],

            // Frozen Pack
            ['id_category' => $frozenPack->id, 'name' => 'Frozen Pack Dimsum Mix (20 pcs)', 'price' => 45000, 'stock' => 100, 'description' => 'Paket hemat 20 pcs isi campur (Ayam, Udang, Mentai). Tinggal kukus 15 menit.'],
            ['id_category' => $frozenPack->id, 'name' => 'Frozen Pack Siomay (20 pcs)', 'price' => 40000, 'stock' => 5, 'description' => 'Siomay beku siap masak. Praktis untuk stok di rumah.'], // ⚠️ Stok menipis untuk demo

            // Minuman
            ['id_category' => $minumanSegar->id, 'name' => 'Es Teh Manis Jumbo', 'price' => 5000, 'stock' => 100, 'description' => 'Teh manis segar ukuran jumbo, pelepas dahaga terbaik.'],
            ['id_category' => $minumanSegar->id, 'name' => 'Es Jeruk Peras Murni', 'price' => 8000, 'stock' => 80, 'description' => 'Jeruk peras asli tanpa pemanis buatan, kaya vitamin C.'],
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(
                ['name' => $data['name']], // Cek berdasarkan nama agar tidak duplikat
                array_merge($data, [
                    'slug' => Str::slug($data['name']),
                    'is_active' => true,
                ])
            );
        }

        // ===== 4. PROMOS =====
        Promo::updateOrCreate(
            ['code' => 'DISKON10'],
            [
                'discount_type' => 'PERCENT',
                'discount_amount' => 10,
                'min_order_amount' => 50000,
                'is_active' => true,
                'valid_until' => '2026-12-31',
            ]
        );

        Promo::updateOrCreate(
            ['code' => 'HEMAT20K'],
            [
                'discount_type' => 'FIXED',
                'discount_amount' => 20000,
                'min_order_amount' => 100000,
                'is_active' => true,
                'valid_until' => '2026-12-31',
            ]
        );

        $this->command->info('✅ Data dummy (User, Kategori, Produk, Promo) berhasil di-seed!');
    }
}
