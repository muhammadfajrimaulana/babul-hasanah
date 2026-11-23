<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'jenjang',
        'kode',
        'nama',
        'guru_id',
        'kelompok',
        'kkm',
        'aktif',
    ];

    protected $casts = [
        'kkm' => 'integer',
        'aktif' => 'boolean',
    ];

    /** Guru pengampu (tanpa akun login) yang namanya tampil di halaman nilai. */
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /** Nilai santri yang memakai mata pelajaran ini. */
    public function nilai()
    {
        return $this->hasMany(NilaiSantri::class, 'mata_pelajaran_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }
}
