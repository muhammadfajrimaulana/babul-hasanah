<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TracerStudy extends Model
{
    protected $table = 'tracer_study';

    protected $fillable = [
        'alumni_id', 'kegiatan', 'instansi', 'alamat', 'kontak', 'tahun_mulai', 'catatan',
    ];

    public function alumni()
    {
        return $this->belongsTo(Alumni::class, 'alumni_id');
    }
}
