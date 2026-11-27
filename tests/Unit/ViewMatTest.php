<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Menjaga agar view scaffold lama yang sudah tidak dipakai tidak muncul lagi.
 * Pemeriksaan ini hanya membaca filesystem, tanpa aplikasi/database.
 */
class ViewMatTest extends TestCase
{
    public function test_view_scaffold_lama_tidak_ada_lagi(): void
    {
        $dasar = dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'views';

        foreach (['welcome.blade.php', 'layouts'.DIRECTORY_SEPARATOR.'admin-menu-struktur.blade.php'] as $relatif) {
            $this->assertFileDoesNotExist($dasar.DIRECTORY_SEPARATOR.$relatif);
        }
    }
}
