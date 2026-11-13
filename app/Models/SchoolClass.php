<?php

namespace App\Models;

use App\Support\Jenjang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Kelas / rombongan belajar per jenjang.
 *
 * @property int $id
 * @property string $jenjang
 * @property string $name
 * @property string|null $tingkat
 */
class SchoolClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'jenjang',
        'name',
        'tingkat',
        'keterangan',
    ];

    /** Santri/siswa yang duduk di kelas ini (pivot class_student). */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'class_student',
            'class_id',
            'student_id'
        )->withTimestamps();
    }

    /**
     * Siswa yang kolom cache `students.class_id`-nya menunjuk kelas ini.
     * Dipertahankan untuk modul yang belum membaca pivot.
     */
    public function studentsLangsung(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'class_id');
    }

    /** Label lengkap: "MTs — VII A". */
    public function label(): string
    {
        $jenjang = Jenjang::label($this->jenjang) ?: $this->jenjang;

        return trim(($jenjang ? $jenjang.' — ' : '').$this->name);
    }
}
