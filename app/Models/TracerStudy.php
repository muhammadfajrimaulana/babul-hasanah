<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Jawaban Tracer Study alumni (tabel `tracer_study`).
 */
class TracerStudy extends Model
{
    use HasFactory;

    protected $table = 'tracer_study';

    protected $fillable = [
        'user_id',
        'student_id',
        'alumni_id',
        'nama',
        'tahun_lulus',
        'jenjang',
        'kegiatan',
        'instansi',
        'jabatan',
        'bidang',
        'mulai_tahun',
        'no_hp',
        'email',
        'alamat',
        'testimoni',
        'bersedia_dihubungi',
    ];

    protected function casts(): array
    {
        return [
            'tahun_lulus' => 'integer',
            'mulai_tahun' => 'integer',
            'bersedia_dihubungi' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class, 'alumni_id');
    }
}
