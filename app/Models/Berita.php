<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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

    /**
     * URL foto utama yang benar-benar tersedia.
     *
     * Mengembalikan null bila kolom `gambar` kosong atau berkasnya sudah tidak
     * ada di disk. Dipakai juga oleh validasi publikasi.
     */
    public function urlGambarTersimpan(): ?string
    {
        $gambar = trim((string) $this->gambar);

        if ($gambar === '') {
            return null;
        }

        if (Str::startsWith($gambar, ['http://', 'https://'])) {
            return $gambar;
        }

        return Storage::disk('public')->exists($gambar)
            ? asset('storage/' . $gambar)
            : null;
    }

    /** Apakah berita sudah memiliki foto utama yang tersimpan. */
    public function punyaGambarTersimpan(): bool
    {
        return $this->urlGambarTersimpan() !== null;
    }

    public function getGambarUrlAttribute(): string
    {
        $tersimpan = $this->urlGambarTersimpan();

        if ($tersimpan !== null) {
            return $tersimpan;
        }

        // Fallback: pakai gambar pertama yang disisipkan di dalam isi berita.
        if (preg_match('/<img[^>]+src\s*=\s*["\']([^"\']+)["\']/i', (string) $this->isi, $cocok)) {
            return $cocok[1];
        }

        // Gambar default hijau bila tidak ada foto yang bisa ditampilkan.
        return asset('assets/img/berita-default.svg');
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
