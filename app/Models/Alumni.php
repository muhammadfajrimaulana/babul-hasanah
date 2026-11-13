<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Alumni extends Model
{
    protected $table = 'alumni';

    protected $fillable = [
        'student_id', 'user_id', 'nama', 'jenis_kelamin', 'tahun_lulus', 'jenjang', 'kegiatan',
        'instansi', 'no_hp', 'email', 'alamat', 'testimoni', 'foto',
    ];

    public const KEGIATAN = ['Kuliah', 'Bekerja', 'Wirausaha', 'Mengabdi', 'Lainnya'];

    public const JENJANG = ['MDTA', 'PPS Wustha', 'MTs', 'MA', 'PDF Wustha', 'PDF Ulya', "Ma'had 'Aly"];

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/'.$this->foto) : null;
    }

    /** Data santri asal (skema baru). */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /** Akun login alumni. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tracer(): HasOne
    {
        return $this->hasOne(TracerStudy::class, 'alumni_id');
    }
}
