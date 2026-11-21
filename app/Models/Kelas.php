<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['jenjang', 'nama', 'tingkat', 'tahun_ajaran', 'wali_kelas_id', 'aktif'];

    protected $casts = ['aktif' => 'boolean', 'tingkat' => 'integer'];

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function santri()
    {
        return $this->hasMany(Santri::class, 'kelas_id');
    }
}
