<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tahun ajaran (tabel `academic_years`).
 *
 * Dipakai untuk mengikat nilai dan penempatan kelas pada tahun ajaran asalnya.
 * Contoh nama: `2026/2027`.
 */
class AcademicYear extends Model
{
    use HasFactory;

    protected $table = 'academic_years';

    protected $fillable = [
        'nama',
        'mulai',
        'selesai',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'mulai' => 'date',
            'selesai' => 'date',
            'aktif' => 'boolean',
        ];
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'academic_year_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    /** Tahun ajaran aktif, atau yang terbaru bila belum ada yang ditandai aktif. */
    public static function berjalan(): ?self
    {
        return static::query()->aktif()->orderByDesc('nama')->first()
            ?: static::query()->orderByDesc('nama')->first();
    }

    /** Cari berdasarkan nama, buat bila belum ada (idempoten). */
    public static function untuk(string $nama): self
    {
        $nama = trim($nama);
        $mulai = (int) strtok($nama, '/');
        $mulai = $mulai > 0 ? $mulai : (int) now()->year;

        return static::query()->firstOrCreate(
            ['nama' => $nama],
            [
                'mulai' => $mulai.'-07-01',
                'selesai' => ($mulai + 1).'-06-30',
                'aktif' => false,
            ]
        );
    }

    /** Tahun kelulusan yang masuk akal dari nama tahun ajaran. */
    public function tahunLulus(): int
    {
        preg_match('/(\d{4})\s*$/', (string) $this->nama, $cocok);

        return isset($cocok[1]) ? (int) $cocok[1] : (int) now()->year;
    }

    public function label(): string
    {
        return 'Tahun Ajaran '.$this->nama;
    }
}
