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
                     ->where('published_at', '<=', now());
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