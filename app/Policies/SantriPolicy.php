<?php

namespace App\Policies;

use App\Models\Santri;
use App\Models\User;
use App\Support\Akses;

/**
 * Kebijakan terpusat untuk data santri.
 *
 * Policy sengaja memanggil App\Support\Akses agar keputusannya identik dengan
 * middleware `role:` dan pemeriksaan jenjang yang sudah dipakai aplikasi.
 */
class SantriPolicy
{
    public function viewAny(User $user): bool
    {
        return Akses::bisa($user, 'santri.lihat');
    }

    public function view(User $user, Santri $santri): bool
    {
        return $this->viewAny($user)
            && Akses::cocokJenjang($user, $santri->jenjang_pendidikan);
    }

    public function create(User $user): bool
    {
        return Akses::bisa($user, 'santri.ubah');
    }

    public function update(User $user, Santri $santri): bool
    {
        return $this->create($user) && $this->view($user, $santri);
    }

    public function delete(User $user, Santri $santri): bool
    {
        return Akses::superAdmin($user) && $this->view($user, $santri);
    }

    /** Pulihkan santri dari tempat sampah (khusus Superadmin). */
    public function restore(User $user, Santri $santri): bool
    {
        return Akses::superAdmin($user) && $this->view($user, $santri);
    }

    /** Pemeriksaan jenjang saat membuat/mengubah data sebelum tersimpan. */
    public function jenjang(User $user, string $jenjang): bool
    {
        return Akses::cocokJenjang($user, $jenjang);
    }
}
