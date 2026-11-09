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

        // 2. Akun Pengasuh
        User::updateOrCreate(
            ['email' => 'pengasuh@babulhasanah.sch.id'],
            [
                'name' => 'Pengasuh Pesantren',
                'password' => Hash::make('babul2026'),
                'role' => 'pengasuh',
            ]
        );

        // 3. Akun Pembina
        User::updateOrCreate(
            ['email' => 'pembina@babulhasanah.sch.id'],
            [
                'name' => 'Pembina Asrama',
                'password' => Hash::make('babul2026'),
                'role' => 'pembina',
            ]
        );

        // 4. Akun Admin per Jenjang
        User::updateOrCreate(
            ['email' => 'mdta@babulhasanah.sch.id'], // MDTA
            [
                'name' => 'Admin MDTA',
                'password' => Hash::make('babul2026'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'ppsw@babulhasanah.sch.id'], // PPS WUSTHA
            [
                'name' => 'Admin PPS WUSTHA',
                'password' => Hash::make('babul2026'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'mts@babulhasanah.sch.id'], // MTS
            [
                'name' => 'Admin MTS',
                'password' => Hash::make('babul2026'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'mas@babulhasanah.sch.id'], // MAS
            [
                'name' => 'Admin MAS',
                'password' => Hash::make('babul2026'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'pdfw@babulhasanah.sch.id'], // PDF WUSTHA
            [
                'name' => 'Admin PDF WUSTHA',
                'password' => Hash::make('babul2026'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'pdfu@babulhasanah.sch.id'], // PDF ULYA
            [
                'name' => 'Admin PDF ULYA',
                'password' => Hash::make('babul2026'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'ma@babulhasanah.sch.id'], // MAHAD ALY
            [
                'name' => 'Admin Mahad Aly',
                'password' => Hash::make('babul2026'),
                'role' => 'admin',
            ]
        );

        // 5. Akun Wali Santri
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
