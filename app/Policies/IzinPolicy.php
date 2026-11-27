<?php

namespace App\Policies;

use App\Models\Izin;
use App\Models\User;
use App\Support\Akademik;
use App\Support\Akses;

/**
 * Kebijakan izin pulang.
 *
 * Dua sisi dibedakan tegas: wali hanya mengajukan untuk anak sendiri,
 * sedang Pembina/Admin Jenjang memverifikasi izin pada cakupan jenjangnya.
 */
class IzinPolicy
{
    public function viewAny(User $user): bool
    {
        return Akses::bisa($user, 'izin.lihat');
    }

    public function verifikasi(User $user, Izin $izin): bool
    {
        if (! Akses::bisa($user, 'izin.verifikasi')) {
            return false;
        }

        return Akademik::santriQuery($user)->whereKey($izin->santri_id)->exists();
    }

    public function update(User $user, Izin $izin): bool
    {
        return $this->verifikasi($user, $izin);
    }

    /** Wali mengajukan izin untuk anak yang tertaut ke akunnya. */
    public function ajukan(User $user, int $santriId): bool
    {
        return $santriId > 0 && $user->memilikiSantri($santriId);
    }

    /** Wali melihat riwayat izin anaknya sendiri. */
    public function lihatPortal(User $user, Izin $izin): bool
    {
        return $user->memilikiSantri((int) $izin->santri_id);
    }
}
