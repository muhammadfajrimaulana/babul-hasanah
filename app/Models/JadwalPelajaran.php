<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'jenjang', 'kelas', 'tahun_ajaran', 'semester', 'hari',
        'jam_mulai', 'jam_selesai', 'mata_pelajaran_id', 'guru_id',
        'ruang', 'urutan', 'created_by',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public const HARI = [
        'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu',
    ];

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function labelWaktu(): string
    {
        return substr((string) $this->jam_mulai, 0, 5).' – '.substr((string) $this->jam_selesai, 0, 5);
    }
}
