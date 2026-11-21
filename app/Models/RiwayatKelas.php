<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatKelas extends Model
{
    protected $table = 'riwayat_kelas';

    public const STATUS = [
        'naik' => 'Naik Kelas',
        'tinggal' => 'Tinggal Kelas',
        'pindah' => 'Pindah Jenjang',
        'keluar' => 'Keluar / Berhenti',
        'lulus' => 'Lulus',
        'dibatalkan' => 'Dibatalkan (Rollback)',
    ];

    protected $fillable = [
        'santri_id', 'kelas_id', 'jenjang', 'kelas', 'kamar',
        'tahun_ajaran', 'semester', 'status', 'keterangan', 'diproses_oleh',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function kelasModel()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function pemroses()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function labelStatus(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }
}
