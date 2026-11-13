<?php

namespace App\Support;

use App\Models\User;

/**
 * Pusat kebijakan akses (role, menu, dan halaman awal) untuk panel admin.
 *
 * Semua keputusan "role ini boleh apa" sebaiknya diambil dari kelas ini agar
 * route, sidebar, dan controller tidak pernah berbeda pendapat.
 */
class Akses
{
    /** Label tampilan setiap nilai kolom users.role. */
    public const ROLE_LABELS = [
        'super_admin' => 'Super Admin',
        'admin' => 'Admin Jenjang',
        'pengasuh' => 'Pengasuh',
        'pembina' => 'Pembina',
        'wali_santri' => 'Wali Santri',
        'alumni' => 'Alumni',
    ];

    /** Halaman pertama (nama route) setelah login, per role. */
    public const DASHBOARD_ROUTE = [
        'super_admin' => 'admin.dashboard',
        'admin' => 'admin.dashboard.jenjang',
        'pengasuh' => 'admin.dashboard.pengasuh',
        'pembina' => 'admin.dashboard.pembina',
        'wali_santri' => 'portal.dashboard',
        'alumni' => 'alumni.dashboard',
    ];

    /**
     * Matriks hak akses per modul.
     *
     * Nilai dipakai dua kali:
     * - route: `role:` + Akses::roles('nama.modul')
     * - tampilan: Akses::bisa($user, 'nama.modul')
     */
    public const MODUL = [
        'dashboard' => ['super_admin', 'admin', 'pengasuh', 'pembina'],
        'profil' => ['super_admin', 'admin', 'pengasuh', 'pembina'],

        // Baca (monitoring) — boleh untuk semua peran panel.
        'pendaftaran.lihat' => ['super_admin', 'admin', 'pengasuh', 'pembina'],
        'santri.lihat' => ['super_admin', 'admin', 'pengasuh', 'pembina'],
        'alumni.lihat' => ['super_admin', 'admin', 'pengasuh', 'pembina'],
        'prestasi.lihat' => ['super_admin', 'admin', 'pengasuh', 'pembina'],
        'jenjang.lihat' => ['super_admin', 'admin', 'pengasuh', 'pembina'],

        // Modul akademik: Super Admin, Admin Jenjang (jenjangnya), dan
        // Pengasuh (monitoring semua jenjang, hanya baca).
        'akademik.lihat' => ['super_admin', 'admin', 'pengasuh'],

        // Tulis (operasional) — hanya Super Admin & Admin Jenjang.
        'pendaftaran.ubah' => ['super_admin', 'admin'],
        'santri.ubah' => ['super_admin', 'admin'],
        'alumni.ubah' => ['super_admin', 'admin'],
        'prestasi.ubah' => ['super_admin', 'admin'],
        'akademik.ubah' => ['super_admin', 'admin'],
        'akademik.terbit' => ['super_admin', 'admin'],
        'akademik.mapel' => ['super_admin', 'admin'],
        'berita' => ['super_admin', 'admin'],
        'galeri' => ['super_admin', 'admin'],

        // Pengelolaan institusional — hanya Super Admin.
        'konten' => ['super_admin'],
        'struktur' => ['super_admin'],
        'info' => ['super_admin'],

        // Halaman Pengguna: Super Admin mengelola semua akun. Admin Jenjang,
        // Pengasuh, dan Pembina hanya boleh mengelola akun WALI SANTRI
        // (Admin Jenjang dibatasi ke jenjangnya — dijaga di controller).
        'pengguna' => ['super_admin', 'admin', 'pengasuh', 'pembina'],
        'sistem' => ['super_admin'],
    ];

    /** Pemetaan awalan email admin jenjang -> slug jenjang resmi. */
    public const EMAIL_JENJANG = [
        'mdta' => 'mdta',
        'ppsw' => 'pps-wustha',
        'mts' => 'mts',
        'mas' => 'ma',
        'pdfw' => 'pdf-wustha',
        'pdfu' => 'pdf-ulya',
        'ma' => 'mahad-aly',
    ];

    /** Normalisasi nilai role dari database/input menjadi kunci resmi. */
    public static function normalisasi(?string $role): ?string
    {
        $nilai = strtolower(trim((string) $role));
        $nilai = str_replace([' ', '-'], '_', $nilai);

        // Peran Spatie (mis. "admin-jenjang", "super-admin") dipetakan ke kunci lama.
        $dariSistem = RoleSistem::legacy($nilai);
        if ($dariSistem && array_key_exists($dariSistem, self::ROLE_LABELS)) {
            return $dariSistem;
        }

        $alias = [
            'superadmin' => 'super_admin',
            'super' => 'super_admin',
            'wali' => 'wali_santri',
            'walisantri' => 'wali_santri',
            'pengurus' => 'pengasuh',
        ];

        $nilai = $alias[$nilai] ?? $nilai;

        return array_key_exists($nilai, self::ROLE_LABELS) ? $nilai : null;
    }

    /** Label role yang aman untuk ditampilkan. */
    public static function label(?string $role): string
    {
        $role = self::normalisasi($role);

        return $role ? self::ROLE_LABELS[$role] : 'Tanpa Peran';
    }

    /** Daftar role untuk middleware `role:` (dipisah koma). */
    public static function roles(string $modul): string
    {
        return implode(',', self::MODUL[$modul] ?? []);
    }

    /**
     * Peran resmi dalam bentuk kunci lama.
     *
     * Utamakan kolom `users.role` (selalu disinkronkan), lalu jatuh ke peran
     * Spatie agar akun yang diubah langsung lewat Spatie tetap dikenali.
     */
    private static function peran(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        return self::normalisasi($user->role)
            ?: RoleSistem::legacy($user->peranSistem());
    }

    /** Apakah user boleh membuka modul tertentu. */
    public static function bisa(?User $user, string $modul): bool
    {
        $role = self::peran($user);

        if (! $role) {
            return false;
        }

        return in_array($role, self::MODUL[$modul] ?? [], true);
    }

    /** Nama route dashboard sesuai role. */
    public static function dashboardRoute(?User $user): string
    {
        $role = self::peran($user);

        return self::DASHBOARD_ROUTE[$role] ?? 'admin.dashboard';
    }

    /** URL dashboard sesuai role. */
    public static function dashboardUrl(?User $user): string
    {
        return route(self::dashboardRoute($user));
    }

    /** Route tujuan setelah login; null berarti role tidak valid. */
    public static function ruteSetelahLogin(?User $user): ?string
    {
        return self::peran($user) ? self::dashboardRoute($user) : null;
    }

    /**
     * Slug jenjang yang menjadi tanggung jawab user.
     *
     * Urutan: kolom users.jenjang -> awalan email (mdta@, mts@, dst.).
     * Hanya Admin Jenjang yang dibatasi per jenjang; role lain null (global).
     */
    public static function jenjangSlug(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        $peran = self::normalisasi($user->role)
            ?: RoleSistem::legacy($user->peranSistem());

        if ($peran !== 'admin') {
            return null;
        }

        $dariKolom = Jenjang::normalisasi($user->jenjang);
        if ($dariKolom) {
            return $dariKolom;
        }

        $awalan = strtolower(strtok((string) $user->email, '@') ?: '');

        return self::EMAIL_JENJANG[$awalan] ?? null;
    }

    /** Data jenjang lengkap (slug, kode, nama, warna, ikon) milik user. */
    public static function jenjangInfo(?User $user): ?array
    {
        $slug = self::jenjangSlug($user);

        return $slug ? Jenjang::cari($slug) : null;
    }

    /** Nama jenjang untuk header; "Pusat / Umum" bila tidak dibatasi. */
    public static function namaJenjang(?User $user): string
    {
        return self::jenjangInfo($user)['nama'] ?? 'Pusat / Umum';
    }

    /** Apakah user adalah Super Admin (menerima variasi penulisan lama). */
    public static function superAdmin(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if (self::normalisasi($user->role) === 'super_admin') {
            return true;
        }

        return method_exists($user, 'hasRole') && $user->hasRole('super-admin');
    }

    /**
     * Apakah data user dibatasi ke satu jenjang.
     * Hanya role admin yang dibatasi; role lain melihat seluruh data.
     */
    public static function dibatasiJenjang(?User $user): bool
    {
        return self::jenjangSlug($user) !== null;
    }

    /**
     * Admin yang belum memiliki jenjang (kolom kosong & email tidak dikenali).
     *
     * Kondisi ini harus diperlakukan sebagai "tidak berhak atas jenjang mana
     * pun" (fail-closed), bukan sebagai admin global.
     */
    public static function adminTanpaJenjang(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $peran = self::normalisasi($user->role)
            ?: RoleSistem::legacy($user->peranSistem());

        return $peran === 'admin' && self::jenjangSlug($user) === null;
    }

    /**
     * Nilai asli kolom jenjang di database yang termasuk jenjang user.
     *
     * @return array<int, string>
     */
    public static function nilaiJenjangDb(?User $user, string $model, string $kolom): array
    {
        $slug = self::jenjangSlug($user);

        if (! $slug) {
            return [];
        }

        return RekapJenjang::nilaiDb($model, $kolom, $slug);
    }

    /** Pastikan satu nilai jenjang milik user (dipakai untuk detail/edit). */
    public static function cocokJenjang(?User $user, ?string $nilai): bool
    {
        $slug = self::jenjangSlug($user);

        if (! $slug) {
            return true; // role global
        }

        return Jenjang::normalisasi($nilai) === $slug;
    }
}
