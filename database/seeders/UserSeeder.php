<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Budi Santoso (QC Staff)',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'department' => 'Quality Control (QC)',
                'pic' => 'Budi Santoso',
            ],
            [
                'name' => 'Siti Rahma (Machining Staff)',
                'email' => 'user2@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'department' => 'Produksi Machining',
                'pic' => 'Siti Rahma',
            ],
            [
                'name' => 'Admin Tool Crib',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'department' => 'Tool Crib & Inventory',
                'pic' => 'Ahmad Fauzi',
            ],
            [
                'name' => 'Teknisi Kalibrasi Joko',
                'email' => 'teknisi@example.com',
                'password' => Hash::make('password'),
                'role' => 'teknisi',
                'department' => 'Maintenance & Calibration',
                'pic' => 'Joko Susilo',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
