<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment(['local', 'testing'])) {
            $email = env('INITIAL_OWNER_EMAIL');
            $password = env('INITIAL_OWNER_PASSWORD');

            if (blank($email) || blank($password)) {
                if (!User::where('role', 'OWNER')->exists()) {
                    throw new RuntimeException(
                        'Set INITIAL_OWNER_EMAIL and INITIAL_OWNER_PASSWORD before seeding the first production owner.'
                    );
                }

                return;
            }

            if (strlen($password) < 12) {
                throw new RuntimeException('INITIAL_OWNER_PASSWORD must contain at least 12 characters.');
            }

            $owner = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'Owner Dapur',
                    'password_hash' => Hash::make($password),
                    'role' => 'OWNER',
                    'phone_number' => null,
                ]
            );

            if (!$owner->isOwner()) {
                throw new RuntimeException('INITIAL_OWNER_EMAIL is already assigned to a non-owner account.');
            }

            return;
        }

        User::updateOrCreate(
            ['email' => 'owner@umkm.com'],
            [
                'name' => 'Owner UMKM (Local)',
                'password_hash' => Hash::make('password123'),
                'role' => 'OWNER',
                'phone_number' => '081234567890',
            ]
        );
    }
}
