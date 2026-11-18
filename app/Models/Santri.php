<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;

    // Menyesuaikan dengan nama tabel di database migrasi kita
    protected $table = 'santri';

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_LULUS = 'lulus';

    protected $fillable = [
        'user_id',
        'pendaftaran_online_id',
        'nomor_induk',
        'nama_lengkap',
        'jenjang_pendidikan',
        'kelas',
        'status_kelulusan',
        'tahun_lulus',
        'alamat',
        'nama_wali',
    ];

    // Relasi ke tabel users (jika santri terhubung dengan akun wali)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Arsip pendaftaran online yang menjadi sumber data santri ini
    public function pendaftaranOnline()
    {
        return $this->belongsTo(PendaftaranOnline::class, 'pendaftaran_online_id');
    }

    // Relasi ke tabel absensi
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'santri_id');
    }

    /** Nilai akademik santri (semua kategori & semester). */
    public function nilai()
    {
        return $this->hasMany(NilaiSantri::class, 'santri_id');
    }

    /** Data alumni yang dibuat saat santri ditandai lulus. */
    public function alumni()
    {
        return $this->hasOne(Alumni::class, 'santri_id');
    }

    /** Apakah santri sudah dinyatakan lulus. */
    public function getSudahLulusAttribute(): bool
    {
        return ($this->status_kelulusan ?? self::STATUS_AKTIF) === self::STATUS_LULUS;
    }
}
