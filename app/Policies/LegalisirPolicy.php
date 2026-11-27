<?php

namespace App\Policies;

use App\Models\Alumni;
use App\Models\Legalisir;
use App\Models\User;
use App\Support\Akademik;
use App\Support\Akses;

/**
 * Kebijakan legalisir alumni.
 *
 * Admin Jenjang hanya memproses pengajuan alumni pada jenjangnya; alumni
 * hanya mengajukan dan mengunduh hasil miliknya sendiri.
 */
class LegalisirPolicy
{
    public function viewAny(User $user): bool
    {
        return Akses::bisa($user, 'alumni.lihat');
    }

    public function proses(User $user, Legalisir $legalisir): bool
    {
        if (! Akses::bisa($user, 'alumni.ubah')) {
            return false;
        }

        $santriId = $legalisir->alumni?->santri_id;

        return $santriId
            && Akademik::santriQuery($user)->whereKey($santriId)->exists();
    }

    public function update(User $user, Legalisir $legalisir): bool
    {
        return $this->proses($user, $legalisir);
    }

    /** Alumni mengajukan legalisir untuk data dirinya sendiri. */
    public function ajukan(User $user, Alumni $alumni): bool
    {
        return $alumni->user_id !== null && $alumni->user_id === $user->id;
    }

    /** Alumni mengunduh hasil hanya bila statusnya selesai. */
    public function unduh(User $user, Legalisir $legalisir): bool
    {
        return $this->milikAlumni($user, $legalisir)
            && $legalisir->status === 'selesai';
    }

    private function milikAlumni(User $user, Legalisir $legalisir): bool
    {
        $alumniId = $legalisir->alumni_id;

        return $alumniId && $user->alumni?->id === $alumniId;
    }
}
