<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

/**
 * Definisi resmi 6 peran sistem (Spatie Permission).
 *
 * Empat peran panel mengikuti diagram alur admin:
 * Super Admin, Admin Jenjang, Pengasuh, dan Pembina. Dua peran portal
 * melengkapi modul yang sudah ada: Wali Santri dan Alumni.
 *
 * Admin Jenjang adalah SATU peran. Jenjang yang ditangani disimpan di
 * `users.jenjang` (salah satu slug dari App\Support\Jenjang), bukan dipecah
 * menjadi peran terpisah seperti admin-mdta/admin-mts.
 *
 * Spatie adalah sumber kebenaran peran. Kolom lama `users.role` tetap diisi
 * (disinkronkan) agar route, sidebar, dan modul lama tetap berjalan.
 *
 *   name (Spatie)   -> legacy (users.role)
 *   super-admin     -> super_admin
 *   admin-jenjang   -> admin
 *   pengasuh        -> pengasuh
 *   pembina         -> pembina
 *   walisantri      -> wali_santri
 *   alumni          -> alumni
 *
 * Jangan menulis daftar peran di tempat lain; gunakan kelas ini.
 */
class RoleSistem
{
    public const GUARD = 'web';

    /**
     * @var array<string, array{label:string, legacy:string}>
     */
    public const ROLES = [
        'super-admin' => ['label' => 'Super Admin', 'legacy' => 'super_admin'],
        'admin-jenjang' => ['label' => 'Admin Jenjang', 'legacy' => 'admin'],
        'pengasuh' => ['label' => 'Pengasuh', 'legacy' => 'pengasuh'],
        'pembina' => ['label' => 'Pembina', 'legacy' => 'pembina'],
        'walisantri' => ['label' => 'Wali Santri', 'legacy' => 'wali_santri'],
        'alumni' => ['label' => 'Alumni', 'legacy' => 'alumni'],
    ];

    /** @return array<string, string> name => label (urutan sesuai konstanta). */
    public static function daftar(): array
    {
        return array_map(fn (array $r) => $r['label'], self::ROLES);
    }

    /** @return array<int, string> */
    public static function nama(): array
    {
        return array_keys(self::ROLES);
    }

    /** @return array<int, string> Nama peran admin jenjang. */
    public static function admin(): array
    {
        return ['admin-jenjang'];
    }

    /**
     * Normalisasi penulisan peran menjadi nama resmi Spatie.
     *
     * Menerima variasi lama:
     * - `superadmin`, `Super Admin`, `super_admin` -> `super-admin`
     * - `pengurus`, `Pengurus Pesantren` -> `pengasuh`
     * - `admin`, `admin-mdta`, `admin-mts`, dst. -> `admin-jenjang`
     * - `wali`, `wali santri`, `wali_santri` -> `walisantri`
     */
    public static function normalisasi(?string $role): ?string
    {
        $nilai = strtolower(trim((string) $role));
        $nilai = str_replace([' ', '_'], '-', $nilai);
        $nilai = preg_replace('/-+/', '-', $nilai) ?: '';

        $alias = [
            'superadmin' => 'super-admin',
            'super-admin' => 'super-admin',
            'pengurus' => 'pengasuh',
            'pengurus-pesantren' => 'pengasuh',
            'wali-santri' => 'walisantri',
            'wali' => 'walisantri',
            'santri' => 'walisantri',
            'admin-jenjang' => 'admin-jenjang',
        ];

        $nilai = $alias[$nilai] ?? $nilai;

        // Semua nama admin lama (admin, admin-mdta, admin-mts, ...) dan
        // penulisan "admin jenjang" menjadi satu peran resmi.
        if ($nilai === 'admin' || str_starts_with($nilai, 'admin-')) {
            $nilai = 'admin-jenjang';
        }

        return array_key_exists($nilai, self::ROLES) ? $nilai : null;
    }

    /** Nama peran lama (users.role) dari nama Spatie. */
    public static function legacy(?string $role): ?string
    {
        $role = self::normalisasi($role);

        return $role ? self::ROLES[$role]['legacy'] : null;
    }

    /** Label ramah-baca untuk nama Spatie maupun nama lama. */
    public static function label(?string $role): string
    {
        $role = self::normalisasi($role);

        return $role ? self::ROLES[$role]['label'] : 'Tanpa Peran';
    }

    /**
     * Tebak peran Spatie dari data lama (kolom role + jenjang).
     * Dipakai untuk menyinkronkan akun yang dibuat sebelum Spatie dipasang.
     */
    public static function dariLegacy(?string $legacy, ?string $jenjang = null): ?string
    {
        $legacy = strtolower(str_replace([' ', '-'], '_', trim((string) $legacy)));

        if (in_array($legacy, ['super_admin', 'superadmin', 'super'], true)) {
            return 'super-admin';
        }

        if (in_array($legacy, ['wali_santri', 'walisantri', 'wali', 'santri'], true)) {
            return 'walisantri';
        }

        if (in_array($legacy, ['pengasuh', 'pengurus'], true)) {
            return 'pengasuh';
        }

        if ($legacy === 'pembina') {
            return 'pembina';
        }

        if ($legacy === 'alumni') {
            return 'alumni';
        }

        // Admin lama dibatasi per jenjang, tetapi sekarang memakai satu peran.
        // Parameter $jenjang tetap diterima agar pemanggil lama tidak rusak.
        if ($legacy === 'admin' || str_starts_with($legacy, 'admin_')) {
            return 'admin-jenjang';
        }

        return null;
    }

    /** @return array<int, string> Peran lama yang setara dengan daftar peran Spatie. */
    public static function legacyDari(array $roles): array
    {
        $hasil = [];

        foreach ($roles as $role) {
            $legacy = self::legacy($role);
            if ($legacy) {
                $hasil[] = $legacy;
            }
        }

        return array_values(array_unique($hasil));
    }

    /** Query semua akun yang berperan Super Admin (Spatie maupun kolom lama). */
    public static function superAdminQuery(): Builder
    {
        return User::query()->where(function (Builder $q) {
            $q->whereHas('roles', fn (Builder $r) => $r->where('name', 'super-admin'))
                ->orWhere('role', 'super_admin');
        });
    }

    public static function jumlahSuperAdmin(): int
    {
        return self::superAdminQuery()->count();
    }

    /**
     * Tetapkan peran Spatie sekaligus menyinkronkan kolom lama.
     * Menjadi satu-satunya cara resmi mengubah peran pengguna.
     *
     * Untuk Admin Jenjang, jenjang disimpan di `users.jenjang`. Bila argumen
     * `$jenjang` null, nilai lama dipertahankan (tidak menghapus jenjang yang
     * sudah diisi). Peran non-admin selalu mengosongkan jenjang.
     */
    public static function sinkron(User $user, ?string $role, ?string $jenjang = null): void
    {
        $role = self::normalisasi($role);

        if (! $role) {
            return;
        }

        Role::findOrCreate($role, self::GUARD);

        $user->syncRoles([$role]);

        $legacy = self::ROLES[$role]['legacy'];

        $slugJenjang = $legacy === 'admin'
            ? Jenjang::normalisasi($jenjang ?? $user->jenjang)
            : null;

        $user->forceFill([
            'role' => $legacy,
            'jenjang' => $slugJenjang,
        ])->saveQuietly();
    }
}
