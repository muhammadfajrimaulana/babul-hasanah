<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Jenjang;
use App\Support\RoleSistem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Membuat 6 peran resmi Spatie Permission lalu menyinkronkan akun lama.
 *
 * Jalankan sendiri (bukan oleh agen AI):
 *   php artisan db:seed --class=RoleSeeder
 *
 * Aman dijalankan berulang. Akun dengan peran lama (admin-mdta, pengurus,
 * dst.) akan dipindahkan ke peran resmi tanpa menghapus akun atau jenjangnya.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('roles') || ! Schema::hasTable('model_has_roles')) {
            $this->command?->warn('Tabel Spatie belum ada. Jalankan `php artisan migrate` terlebih dahulu.');

            return;
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (RoleSistem::nama() as $nama) {
            Role::findOrCreate($nama, RoleSistem::GUARD);
        }

        User::query()->with('roles')->get()->each(function (User $user) {
            $peran = $user->peranSistem()
                ?: RoleSistem::dariLegacy($user->role, $user->jenjang);

            if (! $peran) {
                return;
            }

            $satuPeran = $user->roles->count() === 1
                && $user->roles->first()->name === $peran;

            $jenjangTarget = $peran === 'admin-jenjang'
                ? Jenjang::normalisasi($user->jenjang)
                : null;

            $kolomCocok = $user->role === RoleSistem::legacy($peran)
                && $user->jenjang === $jenjangTarget;

            if ($satuPeran && $kolomCocok) {
                return;
            }

            // Jenjang lama dipertahankan (dinormalisasi di dalam sinkron).
            RoleSistem::sinkron($user, $peran, $user->jenjang);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info(count(RoleSistem::nama()).' peran siap: '.implode(', ', RoleSistem::nama()));
    }
}
