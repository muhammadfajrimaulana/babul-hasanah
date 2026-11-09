<?php

namespace App\Support;

/**
 * Daftar resmi 7 jenjang pendidikan Pondok Pesantren Babul Hasanah.
 *
 * Slug sengaja disamakan dengan halaman publik `/jenjang/{slug}` agar
 * tampilan admin dan halaman publik memakai identitas yang sama.
 */
class Jenjang
{
    public const DAFTAR = [
        [
            'slug' => 'mdta',
            'kode' => 'MDTA',
            'nama' => 'Madrasah Diniyah Takmiliyah',
            'deskripsi' => 'Pendidikan diniyah dasar: bacaan Al-Qur’an, ibadah, akhlak, dan pengantar bahasa Arab.',
            'ikon' => 'fa-book-quran',
            'warna' => '#12a67a',
            'alias' => [
                'MDTA',
                'Madrasah Diniyah Takmiliyah',
                'Madrasah Diniyah Takmiliyah (MDTA)',
                'Diniyah',
            ],
        ],
        [
            'slug' => 'pps-wustha',
            'kode' => 'PPS Wustha',
            'nama' => 'PPS Wustha',
            'deskripsi' => 'Pendalaman kitab kuning tingkat menengah pertama dengan kurikulum pesantren salafiyah.',
            'ikon' => 'fa-award',
            'warna' => '#2563eb',
            'alias' => [
                'PPS Wustha',
                'PPS WUSTHA',
                'Pondok Pesantren Salafiyah Wustha',
                'PPS',
            ],
        ],
        [
            'slug' => 'mts',
            'kode' => 'MTs',
            'nama' => 'MTs',
            'deskripsi' => 'Madrasah Tsanawiyah dengan perpaduan kurikulum nasional dan kepesantrenan.',
            'ikon' => 'fa-school',
            'warna' => '#8b5cc6',
            'alias' => [
                'MTs',
                'MTS',
                'Madrasah Tsanawiyah',
            ],
        ],
        [
            'slug' => 'ma',
            'kode' => 'MA',
            'nama' => 'MA',
            'deskripsi' => 'Madrasah Aliyah yang menyiapkan santri melanjutkan ke perguruan tinggi atau Ma’had ‘Aly.',
            'ikon' => 'fa-graduation-cap',
            'warna' => '#b45309',
            'alias' => [
                'MA',
                'MAS',
                'Madrasah Aliyah',
            ],
        ],
        [
            'slug' => 'pdf-wustha',
            'kode' => 'PDF Wustha',
            'nama' => 'Diniyah Formal Wustha',
            'deskripsi' => 'Pendidikan Diniyah Formal jenjang Wustha dengan kajian kitab turats terstruktur.',
            'ikon' => 'fa-book',
            'warna' => '#0e9f8e',
            'alias' => [
                'Diniyah Formal Wustha',
                'PDF Wustha',
                'PDF WUSTHA',
                'PDF (Diniyah Formal) Wustha',
                'Pendidikan Diniyah Formal Wustha',
            ],
        ],
        [
            'slug' => 'pdf-ulya',
            'kode' => 'PDF Ulya',
            'nama' => 'Diniyah Formal Ulya',
            'deskripsi' => 'Kajian lanjutan fiqih, tafsir, hadits, serta latihan bahtsul masail tingkat atas.',
            'ikon' => 'fa-book-open',
            'warna' => '#e11d48',
            'alias' => [
                'Diniyah Formal Ulya',
                'PDF Ulya',
                'PDF ULYA',
                'Pendidikan Diniyah Formal Ulya',
            ],
        ],
        [
            'slug' => 'mahad-aly',
            'kode' => "Ma'had 'Aly",
            'nama' => "Ma'had 'Aly",
            'deskripsi' => 'Pendidikan tinggi pesantren bagi santri yang ingin mendalami ilmu kitab secara lanjut.',
            'ikon' => 'fa-user-graduate',
            'warna' => '#17402f',
            'alias' => [
                "Ma'had 'Aly",
                "MA'HAD 'ALY",
                'Ma’had ‘Aly',
                'Mahad Aly',
            ],
        ],
    ];

    /** @return array<int, array<string, mixed>> */
    public static function semua(): array
    {
        return self::DAFTAR;
    }

    /** @return array<string, mixed>|null */
    public static function cari(?string $slug): ?array
    {
        foreach (self::DAFTAR as $jenjang) {
            if ($jenjang['slug'] === $slug) {
                return $jenjang;
            }
        }

        return null;
    }

    /**
     * Ubah nilai jenjang dari database (bisa beda penulisan) menjadi slug resmi.
     */
    public static function normalisasi(?string $nilai): ?string
    {
        $bersih = self::bersihkan($nilai);

        if ($bersih === '') {
            return null;
        }

        foreach (self::DAFTAR as $jenjang) {
            foreach ($jenjang['alias'] as $alias) {
                if (self::bersihkan($alias) === $bersih) {
                    return $jenjang['slug'];
                }
            }
        }

        return null;
    }

    /** Nama tampilan resmi dari sebuah nilai jenjang. */
    public static function label(?string $nilai): string
    {
        $slug = self::normalisasi($nilai);

        return $slug ? (self::cari($slug)['nama'] ?? $nilai) : (string) $nilai;
    }

    private static function bersihkan(?string $nilai): string
    {
        $nilai = str_replace(['’', '‘', '`'], "'", (string) $nilai);
        $nilai = strtolower($nilai);
        $nilai = preg_replace('/[^a-z0-9]+/', ' ', $nilai);

        return trim((string) $nilai);
    }
}
