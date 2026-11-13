<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Mata pelajaran per jenjang (tabel `subjects`).
 */
class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subjects';

    protected $fillable = [
        'jenjang',
        'kode',
        'nama',
        'kelompok',
        'kkm',
    ];

    protected function casts(): array
    {
        return [
            'kkm' => 'integer',
        ];
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'subject_id');
    }
}
