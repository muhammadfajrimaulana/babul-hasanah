<?php

namespace App\Support;

use App\Mail\WaliSantriPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Pusat logika akun pengguna panel & portal.
 *
 * Dipakai oleh PenggunaController (CRUD manual) dan Ppdb (verifikasi PPDB)
 * agar aturan password, status, dan pengiriman email selalu sama:
 *
 * - Akun internal (Super Admin, Admin Jenjang, Pengasuh, Pembina) memakai
 *   password default `babul2026` bila admin tidak mengisi password sendiri.
 * - Akun Wali Santri yang mendaftar mandiri berstatus `pending` tanpa email.
 * - Saat Admin Jenjang memverifikasi, sistem membuat password acak,
 *   mengaktifkan akun, lalu mengirimkannya lewat email.
 */
class AkunPengguna
{
    /** Password default akun internal. */
    public const PASSWORD_DEFAULT = 'babul2026';

    /** Label status akun untuk tampilan. */
    public const STATUS = [
        'active' => 'Aktif',
        'pending' => 'Pending',
    ];

    /** Password acak yang aman untuk akun wali santri. */
    public static function passwordAcak(): string
    {
        return Str::password(12, symbols: false);
    }

    /** Password default akun internal. */
    public static function passwordDefault(): string
    {
        return self::PASSWORD_DEFAULT;
    }

    /**
     * Peran yang boleh dibuat oleh pengguna aktif.
     *
     * - Super Admin  : semua peran.
     * - Pengasuh/Pembina : Admin Jenjang & Wali Santri.
     * - Admin Jenjang: hanya Wali Santri pada jenjangnya.
     *
     * @return array<string, string> name => label
     */
    public static function roleBolehDibuat(?User $aktor): array
    {
        if (! $aktor) {
            return [];
        }

        if (Akses::superAdmin($aktor)) {
            return RoleSistem::daftar();
        }

        return match ($aktor->peranSistem()) {
            'pengasuh', 'pembina' => array_intersect_key(
                RoleSistem::daftar(),
                array_flip(['admin-jenjang', 'walisantri'])
            ),
            'admin-jenjang' => ['walisantri' => RoleSistem::label('walisantri')],
            default => [],
        };
    }

    /** Apakah aktor boleh membuat peran tertentu (nama Spatie). */
    public static function bisaBuat(?User $aktor, ?string $role): bool
    {
        $role = RoleSistem::normalisasi($role);

        return $role !== null && array_key_exists($role, self::roleBolehDibuat($aktor));
    }

    /**
     * Aktifkan akun, buat password baru, lalu kirim email.
     *
     * Dipakai saat verifikasi PPDB maupun tombol "Verifikasi" di manajemen
     * pengguna. Password lama tidak pernah dikirim ulang; selalu password baru.
     *
     * @return array{password:string, email_terkirim:bool}
     */
    public static function aktifkan(
        User $user,
        ?string $passwordBaru = null,
        bool $kirimEmail = true,
        ?bool $akunBaru = null,
    ): array {
        $password = $passwordBaru ?: self::passwordAcak();

        $data = [
            'password' => $password, // cast `hashed` pada model User
            'status' => 'active',
            'email_verified_at' => now(),
        ];

        if (Schema::hasColumn('users', 'verified_at')) {
            $data['verified_at'] = now();
        }

        if (Schema::hasColumn('users', 'password_generated_at')) {
            $data['password_generated_at'] = now();
        }

        $user->forceFill($data)->save();

        $terkirim = $kirimEmail
            ? self::kirimPassword($user, $password, $akunBaru ?? false)
            : false;

        return ['password' => $password, 'email_terkirim' => $terkirim];
    }

    /** Kirim kredensial portal ke email wali. Tidak pernah melempar exception. */
    public static function kirimPassword(User $user, string $password, bool $akunBaru = true): bool
    {
        try {
            Mail::to($user->email)->send(new WaliSantriPasswordMail($user, $password, $akunBaru));

            return true;
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim email password wali santri.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /** Super Admin terakhir tidak boleh dihapus / diturunkan perannya. */
    public static function superAdminTerakhir(User $user): bool
    {
        return Akses::superAdmin($user) && RoleSistem::jumlahSuperAdmin() <= 1;
    }

    /** Label status akun yang ramah dibaca. */
    public static function labelStatus(?string $status): string
    {
        return self::STATUS[$status ?: ''] ?? 'Tidak diketahui';
    }
}
