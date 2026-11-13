<?php

namespace App\Support;

use App\Models\MataPelajaran;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Aturan bersama modul akademik: daftar kategori, tahun ajaran, predikat,
 * dan scoping santri per jenjang. Semua controller akademik memakai kelas ini
 * agar tampilan admin, transkrip, dan portal wali tidak berbeda aturan.
 */
class Akademik
{
    /** Kategori penilaian yang dipakai di form dan template CSV. */
    public const KATEGORI = [
        'tugas' => 'Tugas Harian',
        'uts' => 'UTS',
        'uas' => 'UAS',
        'hafalan' => 'Setoran Hafalan',
    ];

    public const SEMESTER = [
        'ganjil' => 'Ganjil',
        'genap' => 'Genap',
    ];

    public const STATUS = [
        'draft' => 'Draft',
        'published' => 'Terbit',
    ];

    /** Apakah tabel akademik sudah dimigrasikan. */
    public static function siap(): bool
    {
        try {
            return Schema::hasTable('mata_pelajaran') && Schema::hasTable('nilai_santri');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Tahun ajaran berjalan, mis. 2026/2027 (mulai Juli). */
    public static function tahunAjaranSekarang(): string
    {
        $tahun = (int) now()->year;

        return now()->month >= 7
            ? $tahun.'/'.($tahun + 1)
            : ($tahun - 1).'/'.$tahun;
    }

    public static function semesterSekarang(): string
    {
        return now()->month >= 7 ? 'ganjil' : 'genap';
    }

    /**
     * Pilihan tahun ajaran: tahun berjalan + 4 tahun sebelumnya.
     *
     * @return array<int, string>
     */
    public static function tahunAjaranPilihan(): array
    {
        $mulai = (int) explode('/', self::tahunAjaranSekarang())[0];
        $daftar = [];

        for ($i = 0; $i < 5; $i++) {
            $daftar[] = ($mulai - $i).'/'.($mulai - $i + 1);
        }

        return $daftar;
    }

    public static function labelKategori(?string $kategori): string
    {
        return self::KATEGORI[$kategori] ?? ucfirst((string) $kategori);
    }

    public static function labelSemester(?string $semester): string
    {
        return self::SEMESTER[$semester] ?? ucfirst((string) $semester);
    }

    public static function labelStatus(?string $status): string
    {
        return self::STATUS[$status] ?? ucfirst((string) $status);
    }

    /** Predikat sederhana berbasis skala 0–100. */
    public static function predikat(float|int|string|null $nilai): string
    {
        if ($nilai === null || $nilai === '') {
            return '-';
        }

        $angka = (float) $nilai;

        return match (true) {
            $angka >= 90 => 'A',
            $angka >= 80 => 'B',
            $angka >= 70 => 'C',
            default => 'D',
        };
    }

    /** Format angka gaya Indonesia tanpa memaksa desimal nol. */
    public static function formatNilai(float|int|string|null $nilai): string
    {
        if ($nilai === null || $nilai === '') {
            return '-';
        }

        $angka = (float) $nilai;

        return fmod($angka, 1.0) === 0.0
            ? number_format($angka, 0, ',', '.')
            : number_format($angka, 1, ',', '.');
    }

    /**
     * Query santri yang boleh diakses user.
     *
     * Admin Jenjang dibatasi ke jenjangnya; Super Admin & Pengasuh melihat
     * semua jenjang. Parameter $jenjang mempersempit ke satu jenjang.
     */
    public static function santriQuery(?User $user, ?string $jenjang = null): Builder
    {
        $query = Santri::query();

        // Admin tanpa jenjang tidak boleh melihat data jenjang mana pun.
        if (Akses::adminTanpaJenjang($user)) {
            return $query->whereRaw('1 = 0');
        }

        if ($user && Akses::dibatasiJenjang($user)) {
            $nilai = Akses::nilaiJenjangDb($user, Santri::class, 'jenjang_pendidikan');
            $query->whereIn('jenjang_pendidikan', $nilai ?: ['__tidak_ada__']);
        }

        $slug = Jenjang::normalisasi($jenjang);

        if ($slug) {
            $nilai = RekapJenjang::nilaiDb(Santri::class, 'jenjang_pendidikan', $slug);
            $query->whereIn('jenjang_pendidikan', $nilai ?: ['__tidak_ada__']);
        }

        return $query;
    }

    /** @return Collection<int, string> */
    public static function daftarKelas(?User $user, ?string $jenjang = null): Collection
    {
        return self::santriQuery($user, $jenjang)
            ->whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');
    }

    /** Query mata pelajaran sesuai jenjang user. */
    public static function mapelQuery(?User $user): Builder
    {
        $query = MataPelajaran::query();

        if (Akses::adminTanpaJenjang($user)) {
            return $query->whereRaw('1 = 0');
        }

        $slug = $user ? Akses::jenjangSlug($user) : null;

        if ($slug) {
            $query->where('jenjang', $slug);
        }

        return $query;
    }
}
