<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tagihan administrasi santri (tabel `tagihan_santri`).
 */
class TagihanSantri extends Model
{
    use HasFactory;

    public const STATUS = [
        'belum' => 'Belum dibayar',
        'sebagian' => 'Dibayar sebagian',
        'lunas' => 'Lunas',
    ];

    protected $table = 'tagihan_santri';

    protected $fillable = [
        'student_id',
        'santri_id',
        'jenis',
        'periode',
        'nominal',
        'dibayar',
        'jatuh_tempo',
        'status',
        'dibayar_at',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'dibayar' => 'decimal:2',
            'jatuh_tempo' => 'date',
            'dibayar_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function sisa(): float
    {
        return max(0.0, (float) $this->nominal - (float) $this->dibayar);
    }

    public function scopeLunas(Builder $query): Builder
    {
        return $query->where('status', 'lunas');
    }

    public function labelStatus(): string
    {
        return self::STATUS[$this->status] ?? ucfirst((string) $this->status);
    }
}
