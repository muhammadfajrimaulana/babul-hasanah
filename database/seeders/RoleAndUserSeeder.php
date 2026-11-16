<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun inti pesantren (bukan data dummy).
 *
 * Hanya tiga akun resmi yang dibuat:
 * - Super Admin : akses penuh
 * - Pengurus    : monitoring seluruh pesantren (role `pengasuh`)
 * - Pembina     : monitoring asrama & kehadiran
 *
 * Akun admin per jenjang dan akun wali santri dibuat manual lewat menu
 * Pengguna agar tidak ada kredensial contoh di database produksi.
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
                'role' => 'pengasuh',
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
