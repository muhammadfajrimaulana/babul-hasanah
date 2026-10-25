<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrestasiSantri extends Model
{
    protected $table = 'prestasi_santri';

    protected $fillable = [
        'judul', 'nama', 'kelas', 'kategori', 'tingkat',
        'wilayah', 'tahun', 'peringkat', 'label', 'deskripsi',
    ];

    protected $casts = [
        'tahun'     => 'integer',
        'peringkat' => 'integer',
    ];
}
