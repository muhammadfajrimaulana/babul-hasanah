<?php

namespace Database\Seeders;

use App\Models\Santri;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Data demo khusus untuk mencoba unggah nilai.
 *
 * Membuat 10 santri di jenjang MTs kelas VII A dengan NIS
 * DEMO-NILAI-001 s/d DEMO-NILAI-010, lalu menautkannya ke satu akun wali
 * demo agar halaman portal nilai bisa langsung dicoba.
 *
 * Jalankan sendiri (bukan oleh agen AI):
 *   php artisan db:seed --class=SantriDemoNilaiSeeder
 *
 * Aman dijalankan berulang karena memakai updateOrCreate.
 */
class SantriDemoNilaiSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasTable('santri')) {
            $this->command?->warn('Tabel users/santri belum ada. Jalankan migrasi terlebih dahulu.');

            return;
        }

        $wali = User::updateOrCreate(
            ['email' => 'demo.wali@babulhasanah.test'],
            [
                'name' => 'Wali Demo Nilai',
                'password' => Hash::make('babul2026'),
                'role' => 'wali_santri',
                'phone' => '081200000001',
                'email_verified_at' => now(),
            ]
        );

        $nama = [
            'Ahmad Fauzan Demo',
            'Muhammad Rizky Demo',
            'Abdul Aziz Demo',
            'Hafizh Al-Farizi Demo',
            'Zulkarnain Demo',
            'Fatimah Az-Zahra Demo',
            'Aisyah Putri Demo',
            'Nabila Husna Demo',
            'Salsabila Rahmadani Demo',
            'Zahra Aulia Demo',
        ];

        foreach ($nama as $i => $namaSantri) {
            $nomor = 'DEMO-NILAI-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT);

            Santri::updateOrCreate(
                ['nomor_induk' => $nomor],
                [
                    'user_id' => $wali->id,
                    'nama_lengkap' => $namaSantri,
                    'jenjang_pendidikan' => 'MTs',
                    'kelas' => 'VII A',
                    'alamat' => 'Jl. Demo Nilai No. '.($i + 1).', Padang Lawas',
                    'nama_wali' => $wali->name,
                ]
            );
        }

        $this->command?->info('Demo nilai siap: 10 santri MTs kelas VII A (NIS DEMO-NILAI-001 s/d DEMO-NILAI-010).');
        $this->command?->info('Akun wali demo: demo.wali@babulhasanah.test / babul2026');
    }
}
