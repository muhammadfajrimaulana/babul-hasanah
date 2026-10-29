<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class StrukturOrganisasi extends Model
{
    protected $table = 'struktur_organisasi';

    protected $fillable = [
        'kategori', 'parent_id', 'nama', 'jabatan', 'kode',
        'keterangan', 'ikon', 'foto', 'urutan', 'aktif',
    ];

    protected $casts = [
        'aktif'  => 'boolean',
        'urutan' => 'integer',
    ];

    public const KATEGORI = [
        'bagan'   => 'Bagan Kepengurusan',
        'jenjang' => 'Ketua Jenjang',
        'bidang'  => 'Bidang Pelayanan',
    ];

    /** Pilihan ikon (Font Awesome 6 Free) yang tampil di form admin. */
    public const IKON = [
        'fa-user-tie'        => 'Pimpinan (berjas)',
        'fa-user'            => 'Umum',
        'fa-user-graduate'   => 'Pendidikan / lulusan',
        'fa-chalkboard-user' => 'Pengajar',
        'fa-pen-nib'         => 'Sekretaris / administrasi',
        'fa-wallet'          => 'Bendahara / keuangan',
        'fa-book-open'       => 'Kurikulum / belajar',
        'fa-book-quran'      => 'Al-Qur’an / tahfidz',
        'fa-mosque'          => 'Ibadah / keagamaan',
        'fa-shield-heart'    => 'Keamanan & disiplin',
        'fa-language'        => 'Bahasa',
        'fa-house-chimney'   => 'Sarana & kebersihan',
        'fa-kit-medical'     => 'Kesehatan',
        'fa-bullhorn'        => 'Humas & media',
        'fa-handshake'       => 'Kerja sama',
        'fa-people-group'    => 'Kesantrian',
        'fa-utensils'        => 'Dapur / konsumsi',
        'fa-futbol'          => 'Olahraga',
        'fa-laptop-code'     => 'Teknologi',
        'fa-scale-balanced'  => 'Hukum / peraturan',
    ];

    /* ------------------------------------------------------------ Relasi */

    public function induk(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function anak(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('urutan')->orderBy('id');
    }

    /* ------------------------------------------------------------ Scope */

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('aktif', true);
    }

    public function scopeDiKategori(Builder $q, string $kategori): Builder
    {
        return $q->where('kategori', $kategori);
    }

    public function scopeUrut(Builder $q): Builder
    {
        return $q->orderBy('urutan')->orderBy('id');
    }

    /* ------------------------------------------------------------ Aksesor */

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    /* ------------------------------------------------------------ Bagan */

    /**
     * Pohon untuk halaman publik: hanya yang aktif. Setiap node punya relasi "anak"
     * yang sudah terisi, jadi cukup 1 query. Bawahan dari atasan yang nonaktif ikut tersembunyi.
     */
    public static function baganTree(): Collection
    {
        $semua = static::diKategori('bagan')->aktif()->urut()->get();
        $peta  = $semua->groupBy(fn ($n) => $n->parent_id ?? 0);

        $pasang = function ($node) use (&$pasang, $peta) {
            $anak = $peta->get($node->id, collect());
            foreach ($anak as $a) {
                $pasang($a);
            }
            $node->setRelation('anak', $anak->values());
        };

        $akar = $peta->get(0, collect());
        foreach ($akar as $a) {
            $pasang($a);
        }

        return $akar->values();
    }

    /**
     * Daftar datar berurutan (induk lalu anaknya) untuk dashboard admin.
     * Tiap item membawa atribut "level" (0 = paling atas). Termasuk yang nonaktif.
     */
    public static function baganBerurutan(): Collection
    {
        $semua = static::diKategori('bagan')->urut()->get();
        $peta  = $semua->groupBy(fn ($n) => $n->parent_id ?? 0);
        $hasil = collect();

        $telusur = function ($parentId, $level) use (&$telusur, $peta, $hasil) {
            foreach ($peta->get($parentId, collect()) as $n) {
                $n->setAttribute('level', $level);
                $hasil->push($n);
                $telusur($n->id, $level + 1);
            }
        };
        $telusur(0, 0);

        return $hasil;
    }

    /** ID semua bawahan (anak, cucu, ...) dari sebuah node. Dipakai agar atasan tidak berputar. */
    public static function idKeturunan(int $id): array
    {
        $ids = [];
        $ambil = false;
        $levelAwal = 0;

        foreach (static::baganBerurutan() as $n) {
            if ($n->id === $id) {
                $ambil = true;
                $levelAwal = $n->level;
                continue;
            }
            if ($ambil) {
                if ($n->level > $levelAwal) {
                    $ids[] = $n->id;
                } else {
                    break;
                }
            }
        }

        return $ids;
    }
}
