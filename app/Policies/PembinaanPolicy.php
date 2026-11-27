<?php

namespace App\Policies;

use App\Models\Absensi;
use App\Models\Hafalan;
use App\Models\Pelanggaran;
use App\Models\User;
use App\Support\Akademik;
use App\Support\Akses;
use Illuminate\Database\Eloquent\Model;

/**
 * Kebijakan pembinaan harian (hafalan, pelanggaran, presensi).
 *
 * Satu policy dipakai untuk tiga model karena aturannya identik: hak tulis
 * mengikuti `pembinaan.ubah`, lalu santri yang disentuh harus masih berada
 * dalam kewenangan jenjang user. Pemeriksaan lama di controller tetap dipakai
 * sebagai lapisan kedua.
 */
class PembinaanPolicy
{
    /** Model yang dijangkau policy ini. */
    public const MODEL = [Hafalan::class, Pelanggaran::class, Absensi::class];

    public function viewAny(User $user): bool
    {
        return Akses::bisa($user, 'pembinaan.lihat');
    }

    public function create(User $user): bool
    {
        return Akses::bisa($user, 'pembinaan.ubah');
    }

    public function update(User $user, Model $model): bool
    {
        return $this->create($user) && $this->santri($user, (int) ($model->santri_id ?? 0));
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->update($user, $model);
    }

    /** Cakupan santri untuk input yang belum berupa model tersimpan. */
    public function santri(User $user, int $santriId): bool
    {
        if ($santriId < 1 || ! $this->create($user)) {
            return false;
        }

        return Akademik::santriQuery($user)->whereKey($santriId)->exists();
    }
}
