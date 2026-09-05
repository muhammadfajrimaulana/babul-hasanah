<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Absensi;

class Santri extends Model
{
    use HasFactory;

    // Menyesuaikan dengan nama tabel di database migrasi kita
    protected $table = 'santri';

    protected $fillable = [
        'user_id',
        'nomor_induk',
        'nama_lengkap',
        'jenjang_pendidikan',
        'kelas',
        'alamat',
        'nama_wali',
    ];

    // Relasi ke tabel users (jika santri terhubung dengan akun wali)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke tabel absensi
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'santri_id');
    }
}