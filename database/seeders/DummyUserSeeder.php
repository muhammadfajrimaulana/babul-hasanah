<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\AkunPengguna;
use App\Support\Jenjang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder akun dummy sistem panel admin Pondok Pesantren Babul Hasanah.
 *
 * Semua akun internal memakai kata sandi default `babul2026` dan langsung
 * berstatus AKTIF. Akun wali santri dummy dibuat PENDING untuk memperagakan
 * alur verifikasi: kata sandi dibuat otomatis dan dikirim melalui email
 * setelah Admin Jenjang menyetujui pendaftaran.
 *
 * Jalankan dengan: php artisan db:seed --class=DummyUserSeeder
 *
 * Aman dijalankan berulang (updateOrCreate).
 */
class DummyUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = AkunPengguna::passwordDefault();
        $hash = Hash::make($password);

        // ------------------------------------------------------------------
        // 1. Akun panel internal (langsung aktif)
        // ------------------------------------------------------------------
        $internal = [
            [
                'email' => 'superadmin@babulhasanah.sch.id',
                'name' => 'Super Admin',
                'role' => 'super-admin',
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
                'name' => 'Pembina',
                'role' => 'pembina',
                'jenjang' => null,
            ],
        ];

        foreach ($internal as $akun) {
            $user = User::withTrashed()->updateOrCreate(
                ['email' => $akun['email']],
                [
                    'name' => $akun['name'],
                    'password' => $hash,
                    'status' => 'active',
                    'jenjang' => $akun['jenjang'],
                ]
            );

            if ($user->trashed()) {
                $user->restore();
            }

            $user->forceFill(['email_verified_at' => now()])->save();
            $user->setPeran($akun['role'], $akun['jenjang']);
        }

        // ------------------------------------------------------------------
        // 2. Admin Jenjang — 7 akun, masing-masing satu jenjang
        // ------------------------------------------------------------------
        // Email mengikuti App\Support\Akses::EMAIL_JENJANG:
        // mdta, ppsw, mts, mas, pdfw, pdfu, ma.
        $jenjangAdmin = [
            'mdta' => ['email' => 'mdta', 'nama' => 'Admin MDTA'],
            'pps-wustha' => ['email' => 'ppsw', 'nama' => 'Admin PPS Wustha'],
            'mts' => ['email' => 'mts', 'nama' => 'Admin MTs'],
            'ma' => ['email' => 'mas', 'nama' => 'Admin MA'],
            'pdf-wustha' => ['email' => 'pdfw', 'nama' => 'Admin PDF Wustha'],
            'pdf-ulya' => ['email' => 'pdfu', 'nama' => 'Admin PDF Ulya'],
            'mahad-aly' => ['email' => 'ma', 'nama' => "Admin Ma'had 'Aly"],
        ];

        foreach ($jenjangAdmin as $slug => $data) {
            $user = User::withTrashed()->updateOrCreate(
                ['email' => $data['email'].'@babulhasanah.sch.id'],
                [
                    'name' => $data['nama'],
                    'password' => $hash,
                    'status' => 'active',
                    'jenjang' => $slug,
                ]
            );

            if ($user->trashed()) {
                $user->restore();
            }

            $user->forceFill(['email_verified_at' => now()])->save();
            $user->setPeran('admin-jenjang', $slug);
        }

        // ------------------------------------------------------------------
        // 3. Akun portal
        // ------------------------------------------------------------------
        // Wali santri: PENDING — menunggu verifikasi Admin Jenjang. Kata sandi
        // akan dibuat ulang & dikirim via email saat pendaftaran disetujui.
        $wali = User::withTrashed()->updateOrCreate(
            ['email' => 'walisantri@babulhasanah.sch.id'],
            [
                'name' => 'Wali Santri Contoh',
                'password' => $hash,
                'status' => 'pending',
                'jenjang' => null,
            ]
        );

        if ($wali->trashed()) {
            $wali->restore();
        }

        $wali->forceFill(['email_verified_at' => null])->save();
        $wali->setPeran('walisantri');

        // Wali santri demo AKTIF — hanya untuk melihat tampilan portal tanpa
        // melewati alur verifikasi. Tidak ditautkan ke santri mana pun supaya
        // tidak mengambil data wali lain; portal akan menampilkan keadaan kosong.
        $waliDemo = User::withTrashed()->updateOrCreate(
            ['email' => 'walidemo@babulhasanah.sch.id'],
            [
                'name' => 'Wali Santri Demo',
                'password' => $hash,
                'status' => 'active',
                'jenjang' => null,
            ]
        );

        if ($waliDemo->trashed()) {
            $waliDemo->restore();
        }

        $waliDemo->forceFill(['email_verified_at' => now()])->save();
        $waliDemo->setPeran('walisantri');

        // Alumni: langsung aktif.
        $alumni = User::withTrashed()->updateOrCreate(
            ['email' => 'alumni@babulhasanah.sch.id'],
            [
                'name' => 'Alumni Contoh',
                'password' => $hash,
                'status' => 'active',
                'jenjang' => null,
            ]
        );

        if ($alumni->trashed()) {
            $alumni->restore();
        }

        $alumni->forceFill(['email_verified_at' => now()])->save();
        $alumni->setPeran('alumni');

        $this->command?->info('Akun dummy berhasil dibuat/diperbarui.');
        $this->command?->info('Kata sandi akun internal: '.$password);
        $this->command?->info('Akun wali santri dummy berstatus PENDING sampai diverifikasi.');
        $this->command?->info('Akun wali demo aktif: walidemo@babulhasanah.sch.id / '.$password);

        $jenjangAktif = collect($jenjangAdmin)
            ->map(fn ($data, $slug) => $data['email'].'@babulhasanah.sch.id ('.(Jenjang::label($slug) ?: $slug).')')
            ->implode(', ');

        $this->command?->info('Admin jenjang: '.$jenjangAktif);
    }
}
