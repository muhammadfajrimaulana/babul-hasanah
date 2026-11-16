<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Nilai siswa (tabel `grades`).
 */
class Grade extends Model
{
    use HasFactory, SoftDeletes;

    public const KATEGORI = [
        'tugas' => 'Tugas',
        'uts' => 'UTS',
        'uas' => 'UAS',
        'hafalan' => 'Hafalan',
    ];

    public const SEMESTER = [
        'ganjil' => 'Ganjil',
        'genap' => 'Genap',
    ];

    public const STATUS = [
        'draft' => 'Draft',
        'published' => 'Terbit',
    ];

    protected $fillable = [
        'student_id',
        'subject_id',
        'class_id',
        'academic_year_id',
        'tahun_ajaran',
        'semester',
        'kategori',
        'nilai',
        'catatan',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /** Filter satu konteks nilai: mapel + tahun ajaran + semester. */
    public function scopeKonteks($query, int $subjectId, string $tahun, string $semester)
    {
        return $query
            ->where('subject_id', $subjectId)
            ->where('tahun_ajaran', $tahun)
            ->where('semester', $semester);
    }

    public function labelStatus(): string
    {
        return self::STATUS[$this->status] ?? ucfirst((string) $this->status);
    }

    public function predikat(): string
    {
        $nilai = (float) $this->nilai;

        return match (true) {
            $nilai >= 90 => 'A',
            $nilai >= 80 => 'B',
            $nilai >= 70 => 'C',
            $nilai >= 60 => 'D',
            default => 'E',
        };
    }
}
