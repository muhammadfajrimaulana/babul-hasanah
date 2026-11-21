<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Legalisir extends Model
{
    protected $table = 'legalisir';

    public const STATUS = [
        'menunggu' => 'Menunggu',
        'diproses' => 'Diproses',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
    ];

    protected $fillable = [
        'alumni_id', 'jenis_dokumen', 'jumlah', 'status',
        'file_hasil', 'catatan', 'diproses_oleh',
    ];

    public function alumni()
    {
        return $this->belongsTo(Alumni::class, 'alumni_id');
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
