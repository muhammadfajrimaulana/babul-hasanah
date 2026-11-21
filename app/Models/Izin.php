<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    protected $table = 'izin';

    public const MENUNGGU = 'menunggu';
    public const DISETUJUI = 'disetujui';
    public const DITOLAK = 'ditolak';
    public const SELESAI = 'selesai';
    public const TERLAMBAT = 'terlambat';

    public const STATUS = [
        self::MENUNGGU => 'Menunggu',
        self::DISETUJUI => 'Disetujui',
        self::DITOLAK => 'Ditolak',
        self::SELESAI => 'Selesai',
        self::TERLAMBAT => 'Terlambat',
    ];

    protected $fillable = [
        'santri_id', 'user_id', 'alasan', 'tanggal_pulang', 'tanggal_kembali',
        'penjemput', 'status', 'alasan_penolakan', 'kode_izin',
        'disetujui_oleh', 'disetujui_pada', 'kembali_pada',
    ];

    protected $casts = [
        'tanggal_pulang' => 'date',
        'tanggal_kembali' => 'date',
        'disetujui_pada' => 'datetime',
        'kembali_pada' => 'datetime',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function wali()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function labelStatus(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    /** Warna status untuk badge Tailwind. */
    public function kelasStatus(): string
    {
        return match ($this->status) {
            self::DISETUJUI => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            self::DITOLAK => 'bg-rose-100 text-rose-700 border-rose-200',
            self::SELESAI => 'bg-slate-100 text-slate-600 border-slate-200',
            self::TERLAMBAT => 'bg-red-600 text-white border-red-700 animate-pulse',
            default => 'bg-amber-100 text-amber-700 border-amber-200',
        };
    }

    public function scopeAntrean(Builder $query): Builder
    {
        return $query->where('status', self::MENUNGGU);
    }

    /** Ubah izin yang sudah lewat tanggal kembali menjadi TERLAMBAT. */
    public static function tandaiTerlambat(): int
    {
        return static::query()
            ->whereIn('status', [self::DISETUJUI, self::TERLAMBAT])
            ->whereNull('kembali_pada')
            ->whereDate('tanggal_kembali', '<', now()->toDateString())
            ->update(['status' => self::TERLAMBAT]);
    }
}
