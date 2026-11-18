<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Dokumen rapor nilai dalam format PDF.
 *
 * Dipakai Admin Jenjang untuk mengunggah rapor per santri dan ditampilkan
 * pada Portal Wali Santri (pratinjau + unduh).
 */
class DokumenNilai extends Model
{
    use HasFactory;

    protected $table = 'dokumen_nilai';

    protected $fillable = [
        'santri_id',
        'jenjang',
        'kelas',
        'tahun_ajaran',
        'semester',
        'judul',
        'file_path',
        'file_name',
        'file_size',
        'status',
        'diunggah_oleh',
        'published_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'published_at' => 'datetime',
    ];

    /** Apakah tabel dokumen nilai sudah dimigrasikan. */
    public static function siap(): bool
    {
        try {
            return Schema::hasTable('dokumen_nilai');
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function pengunggah()
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }

    /** Hanya dokumen berstatus terbit (boleh dilihat wali). */
    public function scopeTerbit(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /** Ukuran file dalam format yang mudah dibaca. */
    public function ukuranManusia(): string
    {
        $ukuran = (int) $this->file_size;

        if ($ukuran >= 1024 * 1024) {
            return number_format($ukuran / (1024 * 1024), 1, ',', '.').' MB';
        }

        if ($ukuran >= 1024) {
            return number_format($ukuran / 1024, 0, ',', '.').' KB';
        }

        return $ukuran.' B';
    }

    /** Hapus berkas fisik saat data dokumen dihapus. */
    protected static function booted(): void
    {
        static::deleting(function (DokumenNilai $dokumen) {
            if (filled($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
            }
        });
    }
}
