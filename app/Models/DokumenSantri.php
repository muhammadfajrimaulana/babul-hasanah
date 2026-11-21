<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DokumenSantri extends Model
{
    protected $table = 'dokumen_santri';

    public const JENIS = [
        'ijazah' => 'Ijazah',
        'skl' => 'Surat Keterangan Lulus',
        'transkrip' => 'Transkrip Nilai Final',
        'sertifikat_tahfidz' => 'Sertifikat Tahfidz',
        'sertifikat_kegiatan' => 'Sertifikat Kegiatan',
    ];

    protected $fillable = [
        'santri_id', 'jenis', 'file_path', 'file_name', 'file_size',
        'status', 'diunggah_oleh',
    ];

    protected $casts = ['file_size' => 'integer'];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function pengunggah()
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }

    public function labelJenis(): string
    {
        return self::JENIS[$this->jenis] ?? $this->jenis;
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

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
}
