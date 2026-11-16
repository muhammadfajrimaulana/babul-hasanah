<?php

namespace App\Models;

use App\Support\Jenjang;
use App\Support\Ppdb;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Data siswa (tabel `students`).
 *
 * Berbeda dengan tabel lama `santri`, tabel ini adalah skema Modul 1 dan
 * dipakai bertahap oleh modul berikutnya. Modul PPDB & import Excel menulis
 * ke tabel ini.
 *
 * @property int $id
 * @property string $nisn
 * @property string $name
 * @property string|null $parent_email
 * @property string $status
 * @property int|null $class_id
 * @property int|null $pendaftaran_online_id
 * @property int|null $santri_id
 */
class Student extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * `calon` = diterima PPDB namun belum diplot ke kelas.
     * `graduated` = lulus dan akun wali dialihkan menjadi alumni.
     */
    public const STATUS = ['calon', 'aktif', 'lulus', 'graduated', 'keluar', 'pindah'];

    protected $fillable = [
        'nisn',
        'name',
        'parent_email',
        'status',
        'user_id',
        'class_id',
        'jenjang',
        'pendaftaran_online_id',
        'santri_id',
        'graduated_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'graduated_at' => 'datetime',
        ];
    }

    /** Akun wali santri yang memantau siswa ini. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Kelas aktif (kolom cache dari pivot class_student). */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /** Seluruh kelas yang pernah diikuti (pivot class_student). */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolClass::class,
            'class_student',
            'student_id',
            'class_id'
        )->withTimestamps();
    }

    /** Arsip PPDB yang menjadi sumber data siswa ini. */
    public function pendaftaranOnline(): BelongsTo
    {
        return $this->belongsTo(PendaftaranOnline::class, 'pendaftaran_online_id');
    }

    /** Pasangan baris pada tabel lama `santri` (portal wali). */
    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    /** Data alumni hasil proses kelulusan. */
    public function alumni(): HasOne
    {
        return $this->hasOne(Alumni::class, 'student_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /** Filter berdasarkan satu slug jenjang resmi. */
    public function scopeJenjang($query, ?string $slug)
    {
        $slug = Jenjang::normalisasi($slug);

        if (! $slug) {
            return $query;
        }

        return $query->whereIn('jenjang', Ppdb::variasiJenjang($slug));
    }

    public function labelStatus(): string
    {
        return match ($this->status) {
            'calon' => 'Calon Santri',
            'aktif' => 'Aktif',
            'lulus' => 'Lulus',
            'graduated' => 'Lulus / Alumni',
            'keluar' => 'Keluar',
            'pindah' => 'Pindah',
            default => ucfirst((string) $this->status),
        };
    }
}
