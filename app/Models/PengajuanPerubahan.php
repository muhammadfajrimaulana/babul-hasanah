<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanPerubahan extends Model
{
    protected $table = 'pengajuan_perubahan';

    public const STATUS = [
        'menunggu' => 'Menunggu',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
    ];

    protected $fillable = [
        'santri_id', 'user_id', 'bidang', 'nilai_lama', 'nilai_baru',
        'alasan', 'status', 'diproses_oleh', 'catatan_admin',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function wali()
    {
        return $this->belongsTo(User::class, 'user_id');
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
