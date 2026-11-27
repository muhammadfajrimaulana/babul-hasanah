<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Menjaga nama aplikasi agar tidak kembali ke branding default Laravel.
 * Test ini hanya membaca konfigurasi, tidak menyentuh database.
 */
class NamaAplikasiTest extends TestCase
{
    public function test_nama_aplikasi_memakai_nama_pesantren(): void
    {
        $this->assertSame('Pondok Pesantren Babul Hasanah', config('app.name'));
    }
}
