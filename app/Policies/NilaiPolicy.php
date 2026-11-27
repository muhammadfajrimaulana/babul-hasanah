<?php

namespace App\Policies;

use App\Models\NilaiSantri;
use App\Models\User;
use App\Support\Akses;

/**
 * Kebijakan terpusat untuk nilai akademik santri.
 *
 * Hak input, hak publikasi, dan batas jenjang sengaja dipisah agar Admin
 * Jenjang tidak bisa menerbitkan/mengubah jenjang lain lewat URL langsung.
 */
class NilaiPolicy
{
    public function viewAny(User $user): bool
    {
        return Akses::bisa($user, 'akademik.lihat');
    }

    public function create(User $user): bool
    {
        return Akses::bisa($user, 'akademik.ubah');
    }

    public function update(User $user, NilaiSantri $nilai): bool
    {
        return $this->create($user)
            && Akses::cocokJenjang($user, $nilai->jenjang);
    }

    public function delete(User $user, NilaiSantri $nilai): bool
    {
        return $this->create($user);
    }

    public function terbitkan(User $user): bool
    {
        return Akses::bisa($user, 'akademik.terbit');
    }

    /**
     * Buka nilai published menjadi draft.
     *
     * Superadmin dan Admin Jenjang sama-sama boleh, tetapi tetap dibatasi
     * pada jenjang masing-masing dan wajib menyertakan alasan (dicek di
     * controller). Aksi ini selalu dicatat di log aktivitas.
     */
    public function bukaKembali(User $user, NilaiSantri $nilai): bool
    {
        return $this->create($user)
            && Akses::cocokJenjang($user, $nilai->jenjang);
    }

    /** Pemeriksaan jenjang untuk konteks input nilai (belum berupa model). */
    public function jenjang(User $user, string $jenjang): bool
    {
        return Akses::cocokJenjang($user, $jenjang);
    }
}
