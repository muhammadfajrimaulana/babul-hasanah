<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun inti pesantren (bukan data dummy).
 *
 * Hanya tiga akun resmi yang dibuat:
 * - Super Admin : akses penuh (role `super_admin`)
 * - Pengurus    : manajemen & operasional pesantren (role `pengurus`)
 * - Pembina     : pembinaan harian asrama (role `pembina`)
 *
 * Akun Admin Jenjang, Wali Santri, dan Alumni dibuat melalui alur aplikasi
 * (menu Pengguna / penerimaan PPDB / kelulusan) agar jenjang dan relasi anak
 * selalu valid serta tidak ada kredensial contoh di database produksi.
 * Password awal hanya dipakai saat akun pertama kali dibuat, sehingga
 * menjalankan seeder ulang tidak mereset password yang sudah diganti.
 */
class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            [
                'email' => 'superadmin@babulhasanah.sch.id',
                'name' => 'Super Administrator',
                'role' => 'super_admin',
                'jenjang' => null,
            ],
            [
                'email' => 'pengurus@babulhasanah.sch.id',
                'name' => 'Pengurus Pesantren',
                'role' => 'pengurus',
                'jenjang' => null,
            ],
            [
                'email' => 'pembina@babulhasanah.sch.id',
                'name' => 'Pembina Asrama',
                'role' => 'pembina',
                'jenjang' => null,
            ],
        ];

        foreach ($akun as $data) {
            $user = User::firstOrNew(['email' => $data['email']]);

            if (! $user->exists) {
                $user->password = Hash::make('babul2026');
            }

            $user->name = $data['name'];
            $user->role = $data['role'];
            $user->jenjang = $data['jenjang'];
            $user->save();
        }
    }
}
