<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class InfoHarian extends Model
{
    protected $table = 'info_harian';

    /** kategori => ikon Font Awesome */
    public const KATEGORI = [
        'Pengumuman' => 'fa-bullhorn',
        'Jadwal'     => 'fa-calendar-day',
        'Agenda'     => 'fa-people-group',
        'Libur'      => 'fa-mug-hot',
    ];

    protected $fillable = [
        'jenis', 'kategori', 'judul', 'isi', 'jam',
        'tanggal', 'berlaku_sampai', 'penting', 'aktif', 'urutan',
    ];

    protected $casts = [
        'tanggal'        => 'date',
        'berlaku_sampai' => 'date',
        'penting'        => 'boolean',
        'aktif'          => 'boolean',
        'urutan'         => 'integer',
    ];

    /* ---------------------------------------------------------- Scope */

    public function scopeInfo(Builder $q): Builder   { return $q->where('jenis', 'info'); }
    public function scopeJadwal(Builder $q): Builder { return $q->where('jenis', 'jadwal'); }
    public function scopeAktif(Builder $q): Builder  { return $q->where('aktif', true); }

    /** Belum lewat tanggal berlaku (kosong = selalu berlaku). */
    public function scopeBerlaku(Builder $q): Builder
    {
        return $q->where(fn ($w) => $w->whereNull('berlaku_sampai')->orWhereDate('berlaku_sampai', '>=', today()));
    }

    /** Sudah memasuki tanggal tayang. */
    public function scopeSudahTerbit(Builder $q): Builder
    {
        return $q->where(fn ($w) => $w->whereNull('tanggal')->orWhereDate('tanggal', '<=', today()));
    }

    /* ------------------------------------------------------- Accessor */

    public function getIkonAttribute(): string
    {
        return self::KATEGORI[$this->kategori] ?? 'fa-circle-info';
    }

    public function getKedaluwarsaAttribute(): bool
    {
        return $this->berlaku_sampai !== null
            && $this->berlaku_sampai->copy()->endOfDay()->isPast();
    }

    /** "Hari ini", "Kemarin", atau "28 Okt 2026". */
    public function getWaktuLabelAttribute(): string
    {
        if (! $this->tanggal) {
            return '';
        }

        return match (true) {
            $this->tanggal->isToday()     => 'Hari ini',
            $this->tanggal->isYesterday() => 'Kemarin',
            default => $this->tanggal->locale('id')->translatedFormat('d M Y'),
        };
    }
}
