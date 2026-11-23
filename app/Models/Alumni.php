<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Alumni extends Model
{
    protected $table = 'alumni';

    protected $fillable = [
        'santri_id', 'user_id', 'nama', 'nisn', 'jenis_kelamin', 'tahun_masuk', 'tahun_lulus', 'nomor_ijazah', 'jenjang',
        'kegiatan', 'instansi', 'pekerjaan', 'no_hp', 'email', 'alamat', 'testimoni', 'foto',
        'nilai_path', 'nilai_nama', 'nilai_diunggah_pada',
    ];

    public const KEGIATAN = ['Kuliah', 'Bekerja', 'Wirausaha', 'Mengabdi', 'Lainnya'];

    public const JENJANG = ['MDTA', 'PPS Wustha', 'MTs', 'MA', 'PDF Wustha', 'PDF Ulya', "Ma'had 'Aly"];

    /** Santri asal data alumni ini (null untuk alumni yang diinput manual). */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    /** Akun login alumni. */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tracer()
    {
        return $this->hasOne(TracerStudy::class, 'alumni_id');
    }

    public function legalisir()
    {
        return $this->hasMany(Legalisir::class, 'alumni_id');
    }

    /** Dokumen final diambil dari data santri asal. */
    public function dokumen()
    {
        return $this->hasManyThrough(
            DokumenSantri::class,
            Santri::class,
            'id',
            'santri_id',
            'santri_id',
            'id'
        );
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/'.$this->foto) : null;
    }

    /** URL publik berkas nilai/transkrip alumni. */
    public function getNilaiUrlAttribute(): ?string
    {
        return $this->nilai_path ? asset('storage/'.$this->nilai_path) : null;
    }

    /** Apakah alumni sudah memiliki berkas nilai/transkrip. */
    public function punyaNilai(): bool
    {
        return filled($this->nilai_path);
    }

    /** Apakah kolom tertentu sudah tersedia (sebelum migrasi dijalankan). */
    public static function siapKolom(string $kolom): bool
    {
        try {
            return Schema::hasColumn('alumni', $kolom);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Apakah kolom unggah nilai sudah tersedia. */
    public static function siapNilai(): bool
    {
        return self::siapKolom('nilai_path');
    }
}
