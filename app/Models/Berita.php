<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Berita extends Model
{
    protected $fillable = [
        'judul', 'slug', 'kategori', 'ringkasan', 'isi',
        'gambar', 'penulis', 'status', 'published_at', 'views',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public const KATEGORI = [
        'Pengumuman', 'Kegiatan', 'Prestasi', 'Akademik',
        'Pendidikan', 'Fasilitas', 'Pesantren', 'Ekstrakurikuler',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where(function ($q) {
                         // Berita tanpa tanggal terbit dianggap langsung tayang.
                         $q->whereNull('published_at')
                           ->orWhere('published_at', '<=', now());
                     });
    }

    /**
     * Urutan berita terbaru: tanggal terbit, fallback ke tanggal dibuat, lalu id.
     * Ini memastikan berita yang baru diunggah selalu muncul paling atas
     * (termasuk saat `published_at` kosong atau beberapa berita bertanggal sama).
     */
    public function scopeTerbaru($query)
    {
        return $query->orderByRaw('COALESCE(published_at, created_at) DESC')
                     ->orderByDesc('id');
    }

    /** Tanggal yang aman dipakai di tampilan (tidak pernah null). */
    public function getTanggalTerbitAttribute()
    {
        return $this->published_at ?? $this->created_at;
    }

    /** Perkiraan waktu baca dalam menit (asumsi 200 kata/menit). */
    public function getMenitBacaAttribute(): int
    {
        $kata = str_word_count(strip_tags((string) $this->isi));

        return max(1, (int) ceil($kata / 200));
    }

    /** Ditandai "Baru" bila terbit dalam 3 hari terakhir. */
    public function getBaruAttribute(): bool
    {
        return $this->tanggal_terbit !== null
            && $this->tanggal_terbit->gt(now()->subDays(3));
    }

    public function getGambarUrlAttribute(): string
    {
        return $this->gambar
            ? asset('storage/' . $this->gambar)
            : asset('assets/img/banner/banner.png');
    }

    public function getRingkasanOtomatisAttribute(): string
    {
        return $this->ringkasan ?: Str::limit(strip_tags($this->isi), 160);
    }

    public static function buatSlug(string $judul): string
    {
        $slug = Str::slug($judul);
        $asli = $slug;
        $i = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $asli . '-' . $i++;
        }

        return $slug;
    }
}