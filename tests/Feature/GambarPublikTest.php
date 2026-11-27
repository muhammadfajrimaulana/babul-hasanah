<?php

namespace Tests\Feature;

use App\Support\DataJenjang;
use App\Support\GambarPublik;
use Tests\TestCase;

class GambarPublikTest extends TestCase
{
    public function test_tujuh_banner_jenjang_memakai_file_yang_tersedia(): void
    {
        foreach (DataJenjang::SLUGS as $slug) {
            $data = DataJenjang::untuk($slug);
            $file = $data['banner'];

            $this->assertTrue(
                GambarPublik::ada('images/program/'.$file),
                'Banner '.$file.' untuk '.$slug.' tidak ditemukan.'
            );
            $this->assertStringContainsString($file, GambarPublik::banner($file));
        }
    }

    public function test_tujuh_foto_ketua_memakai_file_yang_tersedia(): void
    {
        foreach (DataJenjang::SLUGS as $slug) {
            $data = DataJenjang::untuk($slug);
            $file = $data['ketua']['foto'];

            $this->assertTrue(
                GambarPublik::ada('images/pengurus/'.$file),
                'Foto '.$file.' untuk '.$slug.' tidak ditemukan.'
            );
            $this->assertStringContainsString($file, GambarPublik::ketua($file));
        }
    }

    public function test_fallback_dipakai_bila_file_tidak_ada(): void
    {
        $this->assertStringContainsString(
            GambarPublik::FALLBACK_BANNER,
            GambarPublik::banner('tidak-tersedia.jpg')
        );
        $this->assertStringContainsString(
            GambarPublik::FALLBACK_PIMPINAN,
            GambarPublik::ketua('tidak-tersedia.jpg')
        );
    }
}
