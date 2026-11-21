<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hafalan extends Model
{
    protected $table = 'hafalan';

    public const JENIS = [
        'setoran' => 'Setoran Baru',
        'ziyadah' => 'Ziyadah',
        'murojaah' => 'Murojaah',
    ];

    protected $fillable = [
        'santri_id', 'tanggal', 'jenis', 'surat', 'ayat', 'juz',
        'nilai', 'predikat', 'catatan', 'dicatat_oleh',
    ];

    protected $casts = ['tanggal' => 'date', 'nilai' => 'decimal:2'];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function labelJenis(): string
    {
        return self::JENIS[$this->jenis] ?? $this->jenis;
    }
}
