<?php

namespace Tests\Unit;

use App\Support\GambarPublik;
use PHPUnit\Framework\TestCase;

/**
 * Menjaga normalisasi path gambar agar satu aturan dipakai seluruh modul.
 * Tidak ada aplikasi/database yang dijalankan di sini.
 */
class PathGambarStorageTest extends TestCase
{
    public function test_path_disk_public_diterima_apa_adanya(): void
    {
        $this->assertSame('galeri/foto/a.jpg', GambarPublik::relatifStorage('galeri/foto/a.jpg'));
    }

    public function test_prefix_dan_url_storage_dibersihkan(): void
    {
        $this->assertSame('galeri/foto/a.jpg', GambarPublik::relatifStorage('storage/galeri/foto/a.jpg'));
        $this->assertSame('galeri/foto/a.jpg', GambarPublik::relatifStorage('/storage/galeri/foto/a.jpg?v=2'));
    }

    public function test_path_publik_lama_tetap_dikenali(): void
    {
        $this->assertSame('upload/galeri/a.jpg', GambarPublik::relatifStorage('upload/galeri/a.jpg'));
    }

    public function test_url_eksternal_dan_traversal_ditolak(): void
    {
        $this->assertNull(GambarPublik::relatifStorage('https://youtu.be/dQw4w9WgXcQ'));
        $this->assertNull(GambarPublik::relatifStorage('../rahasia.jpg'));
        $this->assertNull(GambarPublik::relatifStorage(''));
    }
}
