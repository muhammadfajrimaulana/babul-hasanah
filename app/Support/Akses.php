<?php

namespace App\Support;

use App\Models\User;

/**
 * Pusat kebijakan akses (role, menu, jenjang, dan halaman awal).
 *
 * Enam role final:
 * - super_admin : pengelola tertinggi (pengguna, master data, log)
 * - pengurus    : manajemen & operasional, hanya baca data santri
 * - pembina     : pembinaan harian (hafalan, presensi, pelanggaran, izin)
 * - admin       : Admin Jenjang, terikat tepat satu jenjang
 * - wali_santri : hanya data anaknya sendiri
 * - alumni      : hanya data dirinya sendiri
 */
class Akses
{
    public const ROLE_LABELS = [
        'super_admin' => 'Superadmin',
        'pengurus' => 'Pengurus',
        'pembina' => 'Pembina',
        'admin' => 'Admin Jenjang',
        'wali_santri' => 'Wali Santri',
        'alumni' => 'Alumni',
    ];

    public const DASHBOARD_ROUTE = [
        'super_admin' => 'admin.dashboard',
        'pengurus' => 'admin.dashboard.pengurus',
        'pembina' => 'admin.dashboard.pembina',
        'admin' => 'admin.dashboard.jenjang',
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
        'dashboard' => ['super_admin', 'admin', 'pengurus', 'pembina'],
        'profil' => ['super_admin', 'admin', 'pengurus', 'pembina'],

        // Baca (monitoring).
        'pendaftaran.lihat' => ['super_admin', 'admin', 'pengurus'],
        'santri.lihat' => ['super_admin', 'admin', 'pengurus', 'pembina'],
        'alumni.lihat' => ['super_admin', 'admin', 'pengurus'],
        'prestasi.lihat' => ['super_admin', 'admin', 'pengurus', 'pembina'],
        'jenjang.lihat' => ['super_admin', 'admin', 'pengurus', 'pembina'],
        'akademik.lihat' => ['super_admin', 'admin', 'pengurus', 'pembina'],
        'pembinaan.lihat' => ['super_admin', 'admin', 'pengurus', 'pembina'],
        'izin.lihat' => ['super_admin', 'admin', 'pengurus', 'pembina'],
        'pengumuman.lihat' => ['super_admin', 'admin', 'pengurus', 'pembina'],
        'kelas.lihat' => ['super_admin', 'admin', 'pengurus', 'pembina'],

        // Tulis (operasional).
        'pendaftaran.ubah' => ['super_admin', 'admin'],
        'santri.ubah' => ['super_admin', 'admin'],
        'santri.arsip' => ['super_admin', 'admin'],
        'alumni.ubah' => ['super_admin', 'admin'],
        'prestasi.ubah' => ['super_admin', 'admin'],
        'akademik.ubah' => ['super_admin', 'admin'],
        'akademik.terbit' => ['super_admin', 'admin'],
        'akademik.mapel' => ['super_admin', 'admin'],
        'pembinaan.ubah' => ['super_admin', 'admin', 'pembina'],
        'izin.verifikasi' => ['super_admin', 'admin', 'pembina'],
        'pengumuman.ubah' => ['super_admin', 'admin', 'pengurus'],
        'kelas.ubah' => ['super_admin', 'admin'],
        'guru.ubah' => ['super_admin', 'admin'],

        // Konten website.
        'berita' => ['super_admin', 'admin'],
        'galeri' => ['super_admin', 'admin'],

        // Institusional.
        'konten' => ['super_admin'],
        'struktur' => ['super_admin'],
        'info' => ['super_admin'],

        // Pengguna & sistem.
        'pengguna' => ['super_admin'],
        'sistem' => ['super_admin'],
        'log' => ['super_admin'],
    ];

    /** Normalisasi nilai role dari database/input menjadi kunci resmi. */
    public static function normalisasi(?string $role): ?string
    {
        $nilai = strtolower(trim((string) $role));
        $nilai = str_replace([' ', '-'], '_', $nilai);

        $alias = [
            'superadmin' => 'super_admin',
            'super' => 'super_admin',
            'pengasuh' => 'pengurus',
            'pengurus_besar' => 'pengurus',
            'wali' => 'wali_santri',
            'walisantri' => 'wali_santri',
            'orangtua' => 'wali_santri',
            'admin_jenjang' => 'admin',
            'adminjenjang' => 'admin',
            'alumnus' => 'alumni',
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

    /** Apakah user boleh membuka modul tertentu. */
    public static function bisa(?User $user, string $modul): bool
    {
        $role = self::normalisasi($user?->role);

        if (! $role) {
            return false;
        }

        return in_array($role, self::MODUL[$modul] ?? [], true);
    }

    /** Nama route dashboard sesuai role. */
    public static function dashboardRoute(?User $user): string
    {
        $role = self::normalisasi($user?->role);

        return self::DASHBOARD_ROUTE[$role] ?? 'login';
    }

    /** URL dashboard sesuai role. */
    public static function dashboardUrl(?User $user): string
    {
        return route(self::dashboardRoute($user));
    }

    /** Route tujuan setelah login; null berarti role tidak valid. */
    public static function ruteSetelahLogin(?User $user): ?string
    {
        return self::normalisasi($user?->role) ? self::dashboardRoute($user) : null;
    }

    /** Role yang memakai panel admin. */
    public static function rolePanel(?string $role): bool
    {
        return in_array(self::normalisasi($role), ['super_admin', 'pengurus', 'pembina', 'admin'], true);
    }

    /** Role yang memakai portal (wali/alumni). */
    public static function rolePortal(?string $role): bool
    {
        return in_array(self::normalisasi($role), ['wali_santri', 'alumni'], true);
    }

    /**
     * Slug jenjang yang menjadi tanggung jawab user.
     *
     * Hanya role `admin` yang dibatasi per jenjang. Sumber satu-satunya adalah
     * kolom users.jenjang; fallback email sengaja dihapus agar perubahan email
     * tidak bisa mengubah cakupan data.
     */
    public static function jenjangSlug(?User $user): ?string
    {
        if (self::normalisasi($user?->role) !== 'admin') {
            return null;
        }

        return Jenjang::normalisasi($user?->jenjang ?? null);
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
        if (self::normalisasi($user?->role) === 'admin' && self::jenjangSlug($user) === null) {
            return 'Jenjang Belum Diatur';
        }

        return self::jenjangInfo($user)['nama'] ?? 'Pusat / Umum';
    }

    /** Apakah user adalah Super Admin (menerima variasi penulisan lama). */
    public static function superAdmin(?User $user): bool
    {
        return self::normalisasi($user?->role) === 'super_admin';
    }

    /**
     * Apakah data user dibatasi ke satu jenjang.
     *
     * Admin selalu dianggap dibatasi, termasuk saat jenjangnya belum diatur.
     * Kondisi belum diatur harus fail-closed, bukan menjadi akses global.
     */
    public static function dibatasiJenjang(?User $user): bool
    {
        return self::normalisasi($user?->role) === 'admin';
    }

    /**
     * Admin yang belum memiliki jenjang (kolom kosong / tidak dikenali).
     * Diperlakukan sebagai "tidak berhak atas jenjang mana pun".
     */
    public static function adminTanpaJenjang(?User $user): bool
    {
        return self::normalisasi($user?->role) === 'admin'
            && self::jenjangSlug($user) === null;
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

    /**
     * Pastikan satu nilai jenjang milik user.
     *
     * Fail-closed: admin tanpa jenjang tidak cocok dengan data apa pun.
     */
    public static function cocokJenjang(?User $user, ?string $nilai): bool
    {
        if (self::normalisasi($user?->role) !== 'admin') {
            return true; // role global
        }

        $slug = self::jenjangSlug($user);

        if (! $slug) {
            return false;
        }

        return Jenjang::normalisasi($nilai) === $slug;
    }

    /** Apakah user boleh melihat seluruh jenjang. */
    public static function semuaJenjang(?User $user): bool
    {
        return in_array(self::normalisasi($user?->role), ['super_admin', 'pengurus', 'pembina'], true);
    }
}
