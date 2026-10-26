<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    protected $table = 'alumni';

    protected $fillable = [
        'nama', 'jenis_kelamin', 'tahun_lulus', 'jenjang', 'kegiatan',
        'instansi', 'no_hp', 'email', 'alamat', 'testimoni', 'foto',
    ];

    public const KEGIATAN = ['Kuliah', 'Bekerja', 'Wirausaha', 'Mengabdi', 'Lainnya'];

    public const JENJANG = ['MDTA', 'PPS Wustha', 'MTs', 'MA', 'PDF Wustha', 'PDF Ulya', "Ma'had 'Aly"];

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }
}
