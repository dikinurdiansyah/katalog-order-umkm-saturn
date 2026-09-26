<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Admin & Owner UMKM
        DB::table('users')->insert([
            [
                'name' => 'Siti Rahma',
                'email' => 'admin@umkm.local',
                'password_hash' => Hash::make('password123'),
                'role' => 'ADMIN',
                'phone_number' => '085220112233',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pak Haji Rusdi',
                'email' => 'owner@umkm.local',
                'password_hash' => Hash::make('password123'),
                'role' => 'OWNER',
                'phone_number' => '081234567890',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 2. Kategori Produk
        $kopiId = DB::table('categories')->insertGetId([
            'name' => 'Kopi & Minuman Herbal',
            'slug' => 'kopi-minuman-herbal',
            'created_at' => now()
        ]);

        $snackId = DB::table('categories')->insertGetId([
            'name' => 'Camilan Khas Alamendah',
            'slug' => 'camilan-khas-alamendah',
            'created_at' => now()
        ]);

        // 3. Produk Katalog
        DB::table('products')->insert([
            [
                'category_id' => $kopiId,
                'name' => 'Kopi Arabika Rancabali 250gr',
                'slug' => 'kopi-arabika-rancabali-250gr',
                'description' => 'Biji kopi bubuk arabika lokal asli perkebunan Ciwidey.',
                'price' => 35000.00,
                'stock' => 25,
                'image_url' => 'kopi-arabika.jpg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => $snackId,
                'name' => 'Keripik Stroberi Renyah 100gr',
                'slug' => 'keripik-stroberi-renyah-100gr',
                'description' => 'Keripik olahan sari buah stroberi asli petani lokal Alamendah.',
                'price' => 15000.00,
                'stock' => 40,
                'image_url' => 'keripik-stroberi.jpg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 4. Voucher Kode Promo
        DB::table('promos')->insert([
            'code' => 'PROMO10',
            'discount_type' => 'FIXED',
            'discount_amount' => 10000.00,
            'min_order_amount' => 50000.00,
            'is_active' => true,
            'valid_until' => '2026-12-31',
            'created_at' => now()
        ]);
    }
}
