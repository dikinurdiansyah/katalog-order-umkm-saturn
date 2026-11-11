<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Owner UMKM',
            'email' => 'owner@umkm.com',
            'password_hash' => Hash::make('password123'), // Sesuai nama kolom di DB
            'role' => 'OWNER',
            'phone_number' => '081234567890'
        ]);
    }
}
