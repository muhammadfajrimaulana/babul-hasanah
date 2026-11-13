<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiSantri extends Model
{
    use HasFactory;

    protected $table = 'nilai_santri';

    protected $fillable = [
        'santri_id',
        'mata_pelajaran_id',
        'jenjang',
        'kelas',
        'tahun_ajaran',
        'semester',
        'kategori',
        'nilai',
        'catatan',
        'status',
        'created_by',
        'diperbarui_oleh',
        'diterbitkan_oleh',
        'published_at',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function penerbit()
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh');
    }

    /** Hanya nilai yang sudah terbit (boleh dilihat wali). */
    public function scopeTerbit(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /** Filter satu konteks nilai. */
    public function scopeKonteks(
        Builder $query,
        int $mapelId,
        string $tahun,
        string $semester,
        string $kategori
    ): Builder {
        return $query
            ->where('mata_pelajaran_id', $mapelId)
            ->where('tahun_ajaran', $tahun)
            ->where('semester', $semester)
            ->where('kategori', $kategori);
    }
}
