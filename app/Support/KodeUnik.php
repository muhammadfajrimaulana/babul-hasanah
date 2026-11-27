<?php

namespace App\Support;

use App\Models\Izin;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Pembuat kode unik untuk identifier yang dipakai aplikasi.
 *
 * Format sengaja dipertahankan agar data lama, pencarian, dan test tetap cocok.
 * Yang diperkuat adalah pengecekan tabrakan: nomor induk juga memeriksa santri
 * yang dihapus lunak, karena unique index database tetap memakai baris tersebut.
 */
final class KodeUnik
{
    private const MAKSIMAL_PERCOBAAN = 50;

    /** Contoh: PPDB-2026-A1B2C. */
    public static function pendaftaran(?string $tahun = null): string
    {
        $tahun ??= date('Y');

        for ($i = 0; $i < self::MAKSIMAL_PERCOBAAN; $i++) {
            $kode = 'PPDB-'.$tahun.'-'.Str::upper(Str::random(5));

            if (! PendaftaranOnline::where('no_pendaftaran', $kode)->exists()) {
                return $kode;
            }
        }

        throw new RuntimeException('Gagal membuat nomor pendaftaran unik.');
    }

    /**
     * Contoh: SANTRI-2026-A1B2.
     *
     * @param  (callable(string): bool)|null  $tersedia  Pengecek tambahan, misalnya daftar nomor
     *                                                   pada file import yang sedang diproses.
     */
    public static function nomorInduk(?string $tahun = null, ?callable $tersedia = null): string
    {
        $tahun ??= date('Y');

        $tersedia ??= static function (string $kode): bool {
            // withTrashed() WAJIB: baris soft delete masih menempati unique index.
            return ! Santri::withTrashed()->where('nomor_induk', $kode)->exists();
        };

        for ($i = 0; $i < self::MAKSIMAL_PERCOBAAN; $i++) {
            $kode = 'SANTRI-'.$tahun.'-'.Str::upper(Str::random(4));

            if ($tersedia($kode)) {
                return $kode;
            }
        }

        throw new RuntimeException('Gagal membuat nomor induk unik.');
    }

    /** Contoh: IZIN-A1B2C3D4. */
    public static function kodeIzin(): string
    {
        for ($i = 0; $i < self::MAKSIMAL_PERCOBAAN; $i++) {
            $kode = 'IZIN-'.Str::upper(Str::random(8));

            if (! Izin::where('kode_izin', $kode)->exists()) {
                return $kode;
            }
        }

        throw new RuntimeException('Gagal membuat kode izin unik.');
    }
}
