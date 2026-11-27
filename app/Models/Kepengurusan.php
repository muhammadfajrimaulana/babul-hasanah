<?php

namespace App\Models;

use App\Support\GambarPublik;
use Illuminate\Database\Eloquent\Model;

class Kepengurusan extends Model
{
    protected $table = 'kepengurusan';

    protected $fillable = ['nama', 'jabatan', 'foto', 'urutan'];

    protected $casts = [
        'urutan' => 'integer',
    ];

    /**
     * URL foto yang aman untuk halaman profil.
     *
     * Foto lama berada di `public/upload/kepengurusan/` dan kolom DB berisi
     * path relatif terhadap public. Foto baru diunggah ke disk `public`
     * sehingga pathnya `kepengurusan/nama.png`. Keduanya bisa dibaca di sini
     * tanpa mengubah data lama atau menyentuh folder upload lama.
     */
    public function getFotoUrlAttribute(): ?string
    {
        $foto = trim((string) $this->foto);

        if ($foto === '') {
            return null;
        }

        $dariStorage = GambarPublik::dariStorage($foto);

        if ($dariStorage) {
            return $dariStorage;
        }

        // Hanya path legacy yang dikenal yang boleh dijadikan asset publik.
        return str_starts_with(ltrim($foto, '/'), 'upload/') ? asset($foto) : null;
    }
}
