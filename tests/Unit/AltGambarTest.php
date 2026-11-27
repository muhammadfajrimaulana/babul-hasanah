<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Menjaga agar gambar data tetap punya teks alt yang deskriptif.
 * Pemeriksaan ini membaca source Blade, bukan database dan tidak menjalankan aplikasi.
 */
class AltGambarTest extends TestCase
{
    /**
     * @var array<string, list<string>>
     */
    private const ALTEKSPEKTASI = [
        'dashboard/alumni/index.blade.php' => ['alt="Foto {{ $a->nama }}"'],
        'dashboard/alumni/show.blade.php' => ['alt="Foto {{ $alumni->nama }}"'],
        'dashboard/alumni/_form.blade.php' => ['alt="Foto {{ $alumni->nama }}"'],
        'dashboard/berita/index.blade.php' => ['alt="Gambar {{ $berita->judul }}"'],
        'dashboard/berita/_form.blade.php' => [
            'alt="Pratinjau gambar utama"',
            'alt="Pratinjau gambar yang akan dipotong"',
        ],
        'layouts/admin-navbar.blade.php' => ['alt="Foto profil {{ auth()->user()->name }}"'],
        'dashboard/index.blade.php' => ['alt="Foto profil {{ $user->name }}"'],
        'dashboard/profil.blade.php' => ['alt="Foto profil {{ $user->name }}"'],
    ];

    public function test_tidak_ada_alt_kosong_pada_gambar_data(): void
    {
        $file = array_keys(self::ALTEKSPEKTASI);
        $file[] = 'pages/galeri.blade.php';

        foreach ($file as $path) {
            $this->assertStringNotContainsString(
                'alt=""',
                $this->baca($path),
                $path.' masih memiliki alt kosong.'
            );
        }
    }

    public function test_alt_gambar_data_deskriptif(): void
    {
        foreach (self::ALTEKSPEKTASI as $path => $alt) {
            $isi = $this->baca($path);

            foreach ($alt as $potongan) {
                $this->assertStringContainsString($potongan, $isi, $path.' belum punya alt deskriptif.');
            }
        }

        $this->assertStringContainsString(
            ':alt="t.judul ? t.judul :',
            $this->baca('pages/galeri.blade.php')
        );
    }

    private function baca(string $path): string
    {
        $isi = file_get_contents(dirname(__DIR__, 2).'/resources/views/'.$path);

        return is_string($isi) ? $isi : '';
    }
}
