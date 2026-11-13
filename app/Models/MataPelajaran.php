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
        'kelompok',
        'kkm',
        'aktif',
    ];

    protected $casts = [
        'kkm' => 'integer',
        'aktif' => 'boolean',
    ];

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
