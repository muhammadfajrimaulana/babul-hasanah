<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Menjaga template .env tetap lengkap dan bebas dari kredensial asli.
 * Pemeriksaan ini hanya membaca berkas, tanpa aplikasi/database.
 */
class EnvExampleTest extends TestCase
{
    private const WAJIB = [
        'APP_NAME="Pondok Pesantren Babul Hasanah"',
        'APP_KEY=',
        'APP_LOCALE=id',
        'APP_MAINTENANCE_DRIVER=file',
        'GOOGLE_CLIENT_ID=',
        'GOOGLE_CLIENT_SECRET=',
        'GOOGLE_REDIRECT_URI=',
        'BCRYPT_ROUNDS=12',
        'DB_CONNECTION=mysql',
        'DB_DATABASE=babul_hasanah',
        'SESSION_DRIVER=database',
        'SESSION_COOKIE=laravel-session',
        'FILESYSTEM_DISK=local',
        'QUEUE_CONNECTION=database',
        'CACHE_STORE=database',
        'MAIL_MAILER=smtp',
        'MAIL_FROM_NAME="Pondok Pesantren Babul Hasanah"',
        'VITE_APP_NAME="${APP_NAME}"',
    ];

    public function test_template_env_memiliki_konfigurasi_wajib(): void
    {
        $isi = $this->baca('.env.example');

        foreach (self::WAJIB as $baris) {
            $this->assertStringContainsString($baris, $isi, "Baris wajib tidak ada: {$baris}");
        }
    }

    public function test_template_env_tidak_menyalin_secret_dari_env_lokal(): void
    {
        $pathEnv = $this->path('.env');

        if (! is_file($pathEnv)) {
            $this->markTestSkipped('.env lokal tidak ada; tidak ada secret untuk dibandingkan.');
        }

        $template = $this->baca('.env.example');
        $aktual = (string) file_get_contents($pathEnv);

        foreach (['APP_KEY', 'GOOGLE_CLIENT_ID', 'GOOGLE_CLIENT_SECRET', 'MAIL_USERNAME', 'MAIL_PASSWORD'] as $kunci) {
            if (! preg_match('/^'.preg_quote($kunci, '/').'=(.*)$/m', $aktual, $cocok)) {
                continue;
            }

            $nilai = trim($cocok[1], "\"' \t");

            if ($nilai !== '') {
                $this->assertStringNotContainsString($nilai, $template, "Template menyalin nilai {$kunci} dari .env");
            }
        }
    }

    private function path(string $nama): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.$nama;
    }

    private function baca(string $nama): string
    {
        $path = $this->path($nama);
        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }
}
