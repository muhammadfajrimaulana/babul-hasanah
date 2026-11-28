<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Menjaga doc 03/05/07 dan doc peran tetap sinkron dengan kode.
 *
 * Hanya membaca berkas dokumentasi; tidak menjalankan aplikasi atau database.
 */
class DokumenSinkronTest extends TestCase
{
    public function test_klaim_lama_tidak_muncul_lagi_di_dokumentasi(): void
    {
        $stale = [
            '03-database.md' => [
                'Total file migrasi saat ini: **43**',
                'tidak ada field upload di admin',
                'belum ada UI/controller',
                'Tidak ada relasi Eloquent antara `PendaftaranOnline` dengan `User`/`Santri`',
                '0 hardcode',
            ],
            '05-backend.md' => [
                '**hardcode 0**',
                'Method `settings()` tidak didefinisikan',
                'Implementasi import masih **stub**',
                'file tersebut tidak ada',
                'Validasi 8 field',
                'Tidak mengirim email/notifikasi',
                'Tidak ada validasi MIME/ukuran',
                'form edit 12 field pendaftaran',
            ],
            '07-fitur.md' => [
                'Validasi 12 field',
                'tidak ada email konfirmasi',
                'santri tidak punya email',
                'nomor induk acak',
                'email, unik (dipakai login Portal Wali)',
            ],
            'role-pengguna.md' => [
                '**12 field wajib**',
            ],
        ];

        foreach ($stale as $file => $phrases) {
            $isi = $this->bacaDokumen($file);

            foreach ($phrases as $phrase) {
                $this->assertStringNotContainsString(
                    $phrase,
                    $isi,
                    "Klaim lama masih ada di {$file}: {$phrase}"
                );
            }
        }
    }

    public function test_dokumentasi_memuat_kondisi_kode_saat_ini(): void
    {
        $expected = [
            '03-database.md' => [
                '53',
                '2026_11_28_000005_tambah_soft_deletes_santri',
                'jenis_kelamin',
                'KodeUnik',
            ],
            '05-backend.md' => [
                'GoogleLoginController',
                'KurikulumController',
                'RaporController',
                'ImportSantri',
                'KodeUnik',
            ],
            '07-fitur.md' => [
                'email_santri',
                'PendaftaranMasukMail',
                'KodeUnik::pendaftaran()',
            ],
            'role-pengguna.md' => [
                'email santri',
                '13 field',
            ],
        ];

        foreach ($expected as $file => $phrases) {
            $isi = $this->bacaDokumen($file);

            foreach ($phrases as $phrase) {
                $this->assertStringContainsString($phrase, $isi, "Kondisi kode baru belum ada di {$file}: {$phrase}");
            }
        }
    }

    private function bacaDokumen(string $nama): string
    {
        $path = dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.$nama;

        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }
}
