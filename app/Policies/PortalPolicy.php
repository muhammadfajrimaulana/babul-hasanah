<?php

namespace App\Policies;

use App\Models\Alumni;
use App\Models\DokumenNilai;
use App\Models\DokumenSantri;
use App\Models\Izin;
use App\Models\Legalisir;
use App\Models\Santri;
use App\Models\User;

/**
 * Kebijakan kepemilikan data pada Portal Wali Santri dan Portal Alumni.
 *
 * Ability didaftarkan dengan Gate::define di AppServiceProvider karena tidak
 * terikat pada satu model_policy. Semua keputusan memakai tautan relasi yang
 * sama dengan pemeriksaan abort_unless lama, bukan sekadar parameter URL.
 */
class PortalPolicy
{
    /** Wali hanya boleh membuka data anak yang tertaut ke akunnya. */
    public function lihatAnak(User $user, Santri $santri): bool
    {
        return $user->memilikiSantri((int) $santri->getKey());
    }

    /** Variasi untuk input berupa id sebelum model dimuat. */
    public function lihatAnakId(User $user, int $santriId): bool
    {
        return $santriId > 0 && $user->memilikiSantri($santriId);
    }

    /** Rapor PDF anak wali harus sudah published. */
    public function lihatDokumen(User $user, DokumenNilai $dokumen): bool
    {
        return $this->lihatAnakId($user, (int) $dokumen->santri_id)
            && $dokumen->status === 'published';
    }

    /** Ijazah/transkrip anak wali juga harus sudah published. */
    public function lihatDokumenSantri(User $user, DokumenSantri $dokumen): bool
    {
        return $this->lihatAnakId($user, (int) $dokumen->santri_id)
            && $dokumen->status === 'published';
    }

    /** Wali mengajukan izin untuk anak sendiri. */
    public function ajukanIzin(User $user, int $santriId): bool
    {
        return $this->lihatAnakId($user, $santriId);
    }

    /** Alumni hanya mengakses data dirinya sendiri. */
    public function lihatAlumni(User $user, Alumni $alumni): bool
    {
        return $alumni->user_id !== null && $alumni->user_id === $user->id;
    }

    /** Berkas dokumen alumni harus menjadi milik santri alumni bersangkutan. */
    public function lihatDokumenAlumni(User $user, DokumenSantri $dokumen, Alumni $alumni): bool
    {
        return $this->lihatAlumni($user, $alumni)
            && (int) $dokumen->santri_id === (int) $alumni->santri_id
            && $dokumen->status === 'published';
    }

    /** Pengajuan legalisir hanya untuk alumni yang bersangkutan. */
    public function ajukanLegalisir(User $user, Alumni $alumni): bool
    {
        return $this->lihatAlumni($user, $alumni);
    }

    /** Hasil legalisir hanya boleh diunduh alumni pemiliknya dan bila selesai. */
    public function unduhLegalisir(User $user, Legalisir $legalisir, Alumni $alumni): bool
    {
        return $this->lihatAlumni($user, $alumni)
            && (int) $legalisir->alumni_id === (int) $alumni->id
            && $legalisir->status === 'selesai';
    }

    /** Riwayat izin anak wali. */
    public function lihatIzin(User $user, Izin $izin): bool
    {
        return $this->lihatAnakId($user, (int) $izin->santri_id);
    }
}
