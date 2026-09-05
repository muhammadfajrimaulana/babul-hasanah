<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'santri_id',
        'jenis_kegiatan',
        'keterangan',
        'status_kehadiran',
        'tanggal',
    ];

    // Relasi balik ke santri
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }
}