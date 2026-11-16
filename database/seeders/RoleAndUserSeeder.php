<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun dummy untuk menguji setiap jalur login.
 *
 * Semua akun memakai password: babul2026
 * Kolom `jenjang` wajib diisi untuk role admin agar dashboard-nya terarah.
 */
class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            // 1. Super Admin — akses penuh
            [
                'email' => 'superadmin@babulhasanah.sch.id',
                'name' => 'Super Administrator',
                'role' => 'super_admin',
                'jenjang' => null,
            ],

            // 2. Pengurus / Pengasuh — monitoring seluruh pesantren
            [
                'email' => 'pengurus@babulhasanah.sch.id',
                'name' => 'Pengurus Pesantren',
                'role' => 'pengasuh',
                'jenjang' => null,
            ],

            // 3. Pembina Asrama — monitoring santri & kehadiran
            [
                'email' => 'pembina@babulhasanah.sch.id',
                'name' => 'Pembina Asrama',
                'role' => 'pembina',
                'jenjang' => null,
            ],

            // 4-10. Admin per jenjang — data dibatasi ke jenjang masing-masing
            ['email' => 'mdta@babulhasanah.sch.id', 'name' => 'Admin MDTA', 'role' => 'admin', 'jenjang' => 'mdta'],
            ['email' => 'ppsw@babulhasanah.sch.id', 'name' => 'Admin PPS Wustha', 'role' => 'admin', 'jenjang' => 'pps-wustha'],
            ['email' => 'mts@babulhasanah.sch.id', 'name' => 'Admin MTs', 'role' => 'admin', 'jenjang' => 'mts'],
            ['email' => 'mas@babulhasanah.sch.id', 'name' => 'Admin MA', 'role' => 'admin', 'jenjang' => 'ma'],
            ['email' => 'pdfw@babulhasanah.sch.id', 'name' => 'Admin PDF Wustha', 'role' => 'admin', 'jenjang' => 'pdf-wustha'],
            ['email' => 'pdfu@babulhasanah.sch.id', 'name' => 'Admin PDF Ulya', 'role' => 'admin', 'jenjang' => 'pdf-ulya'],
            ['email' => 'ma@babulhasanah.sch.id', 'name' => "Admin Ma'had 'Aly", 'role' => 'admin', 'jenjang' => 'mahad-aly'],

            // 11. Wali Santri — hanya portal wali
            [
                'email' => 'walisantri@gmail.com',
                'name' => 'Ahmad Sofyan',
                'role' => 'wali_santri',
                'jenjang' => null,
            ],
        ];

        foreach ($akun as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('babul2026'),
                    'role' => $data['role'],
                    'jenjang' => $data['jenjang'],
                ]
            );
        }
    }
}
