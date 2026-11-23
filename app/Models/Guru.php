<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'guru';

    protected $fillable = ['nama', 'nip', 'jenis_kelamin', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    /** Mata pelajaran yang diampu guru ini. */
    public function mataPelajaran()
    {
        return $this->hasMany(MataPelajaran::class, 'guru_id');
    }
}
