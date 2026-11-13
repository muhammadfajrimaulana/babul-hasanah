<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder awal aplikasi — hanya data master, tanpa data contoh/dummy.
 *
 * Yang dibuat:
 * - Peran sistem (Spatie Permission) dan sinkronisasi akun lama.
 * - Daftar program/jenjang pendidikan.
 * - Master mata pelajaran per jenjang.
 *
 * Akun pengguna, santri, nilai, berita, galeri, dan konten halaman TIDAK
 * dibuat di sini. Semua data tersebut diisi melalui panel admin atau
 * ditambahkan oleh pengelola sesuai data nyata pesantren.
 *
 * Seeder tambahan untuk akun dummy bisa dipanggil dengan:
 *   php artisan db:seed --class=DummyUserSeeder
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ProgramSeeder::class,
            MataPelajaranSeeder::class,
        ]);

        // Jalankan seeder akun dummy (password default: babul2026)
        $this->call(DummyUserSeeder::class);
    }
}
