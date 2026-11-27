<?php

namespace Tests\Unit;

use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Menjaga format tanggal agar konsisten memakai locale Bahasa Indonesia.
 * Tidak ada database yang disentuh oleh test ini.
 */
class LocaleTanggalTest extends TestCase
{
    public function test_lokasi_default_aplikasi_adalah_indonesia(): void
    {
        $this->assertSame('id', config('app.locale'));
        $this->assertSame('id', Carbon::getLocale());
    }

    public function test_tanggal_tampil_dengan_nama_bulan_indonesia(): void
    {
        $tanggal = Carbon::parse('2026-12-25');

        $this->assertSame('25 Desember 2026', $tanggal->translatedFormat('d F Y'));
        $this->assertSame('25 Des 2026', $tanggal->translatedFormat('d M Y'));
    }

    public function test_tidak_ada_format_tanggal_tampilan_yang_hardcode(): void
    {
        $terlarang = ["->format('d/m/Y')", "->format('d M Y')", "->format('d M Y, H:i')"];
        $pelanggaran = [];

        foreach ($this->fileBlade(base_path('resources/views')) as $file) {
            $isi = file_get_contents($file);

            foreach ($terlarang as $pola) {
                if (str_contains($isi, $pola)) {
                    $pelanggaran[] = str_replace(base_path().'\\', '', $file).' → '.$pola;
                }
            }
        }

        $this->assertSame([], $pelanggaran, implode(PHP_EOL, $pelanggaran));
    }

    /**
     * @return list<string>
     */
    private function fileBlade(string $direktori): array
    {
        $hasil = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($direktori));

        foreach ($iterator as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                $hasil[] = $file->getPathname();
            }
        }

        return $hasil;
    }
}
