<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Resolusi gambar publik dengan fallback yang pasti ada.
 *
 * Banyak halaman lama mengarah ke public/images/program dan
 * public/images/pengurus yang belum tersedia. Helper ini mencegah
 * <img> rusak/putih dengan menggantinya ke aset bawaan yang memang ada.
 */
class GambarPublik
{
    public const FALLBACK_BANNER = 'assets/img/banner/banner.png';
    public const FALLBACK_PIMPINAN = 'assets/img/banner/pimpinan.jpg';
    public const FALLBACK_LOGO = 'assets/img/logo/logo.png';

    /** Cek file relatif terhadap folder public/, aman dari path traversal. */
    public static function ada(?string $path): bool
    {
        $path = trim((string) $path);

        if ($path === '' || str_contains($path, '..')) {
            return false;
        }

        return is_file(public_path(ltrim($path, '/')));
    }

    /** Banner jenjang: pakai file resmi bila ada, otherwise banner bawaan. */
    public static function banner(?string $file): string
    {
        $file = trim((string) $file);
        $path = $file === '' ? null : 'images/program/'.$file;

        return self::ada($path) ? asset($path) : asset(self::FALLBACK_BANNER);
    }

    /** Foto ketua jenjang: pakai file resmi bila ada, otherwise foto pimpinan. */
    public static function ketua(?string $file): string
    {
        $file = trim((string) $file);
        $path = $file === '' ? null : 'images/pengurus/'.$file;

        return self::ada($path) ? asset($path) : asset(self::FALLBACK_PIMPINAN);
    }

    /**
     * Normalisasi nilai kolom gambar menjadi path relatif pada disk `public`.
     *
     * Menerima path baru ("galeri/foto/a.jpg"), prefix "storage/", URL
     * "/storage/...", maupun path publik lama ("upload/galeri/a.jpg").
     * URL eksternal dan path traversal ditolak.
     */
    public static function relatifStorage(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        if (preg_match('#/storage/(.+)$#', $path, $cocok)) {
            $relative = $cocok[1];
        } elseif (preg_match('#^[a-z][a-z0-9+.-]*://#i', $path)) {
            return null;
        } else {
            $relative = (string) preg_replace('#^storage/#', '', $path);
        }

        // Buang query string (mis. ?v=2) dan slash awal.
        $relative = ltrim((string) strtok($relative, '?'), '/');

        return $relative === '' || str_contains($relative, '..') ? null : $relative;
    }

    /**
     * URL gambar yang benar-benar ada.
     *
     * Mengutamakan disk `public` (path "storage/..."), lalu jatuh ke berkas
     * publik lama yang masih tersimpan di folder `public/`. Null bila tidak ada
     * berkas, sehingga pemanggil bisa memakai fallback.
     */
    public static function dariStorage(?string $path): ?string
    {
        $relative = self::relatifStorage($path);

        if ($relative === null) {
            return null;
        }

        if (self::ada('storage/'.$relative)) {
            return asset('storage/'.$relative);
        }

        return self::ada($relative) ? asset($relative) : null;
    }

    /**
     * Hapus berkas dari disk `public` bila pathnya memang milik disk tersebut.
     * Path publik lama sengaja tidak dihapus agar data legacy tidak berubah.
     */
    public static function hapus(?string $path): bool
    {
        $relative = self::relatifStorage($path);

        if ($relative === null || ! Storage::disk('public')->exists($relative)) {
            return false;
        }

        Storage::disk('public')->delete($relative);

        return true;
    }

    /**
     * Gambar dari disk public/storage dengan fallback.
     * Berguna untuk konten yang diunggah admin lewat dashboard.
     */
    public static function storageAtau(?string $path, ?string $fallback = null): string
    {
        return self::dariStorage($path) ?? asset($fallback ?? self::FALLBACK_LOGO);
    }
}
