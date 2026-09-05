<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Super Admin (Akses Full)
        User::updateOrCreate(
            ['email' => 'superadmin@babulhasanah.sch.id'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('babul2026'),
                'role' => 'super_admin',
            ]
        );

        // 2. Akun Admin / Pengurus (Akses Operasional Pesantren)
        User::updateOrCreate(
            ['email' => 'admin@babulhasanah.sch.id'],
            [
                'name' => 'Admin Pesantren',
                'password' => Hash::make('babul2026'),
                'role' => 'admin', // Role admin biasa
            ]
        );

        // 3. Akun Wali Santri
        User::updateOrCreate(
            ['email' => 'walisantri@gmail.com'],
            [
                'name' => 'Ahmad Sofyan',
                'password' => Hash::make('babul2026'),
                'role' => 'wali_santri',
            ]
        );
    }
}