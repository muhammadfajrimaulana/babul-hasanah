<?php

namespace App\Support;

use App\Models\PrestasiSantri;
use App\Models\Santri;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Pembatasan data prestasi per jenjang.
 *
 * Tabel `prestasi_santri` hanya menyimpan nama santri dan teks kelas bebas
 * (contoh "XI MA"); tidak ada kolom jenjang. Karena itu keanggotaan jenjang
 * ditentukan dari relasi nama ke data santri (`santri`/`students`) pada
 * jenjang yang ditangani admin.
 *
 * Aturan:
 * - Super Admin / Pengasuh / Pembina (peran global) melihat semua prestasi.
 * - Admin Jenjang hanya melihat prestasi milik santri pada jenjangnya.
 * - Admin tanpa jenjang tidak melihat data apa pun (fail-closed).
 */
class PrestasiJenjang
{
    /** Query prestasi yang terlihat oleh pengguna. */
    public static function query(?User $user): Builder
    {
        $query = PrestasiSantri::query();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if (Akses::adminTanpaJenjang($user)) {
            return $query->whereRaw('1 = 0');
        }

        if (! Akses::dibatasiJenjang($user)) {
            return $query;
        }

        $namaSantri = self::namaSantriJenjang($user);

        if ($namaSantri === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('nama', $namaSantri);
    }

    /** Apakah satu baris prestasi berada dalam wewenang pengguna. */
    public static function cocok(?User $user, PrestasiSantri $prestasi): bool
    {
        if (! $user) {
            return false;
        }

        if (Akses::adminTanpaJenjang($user)) {
            return false;
        }

        if (! Akses::dibatasiJenjang($user)) {
            return true;
        }

        $nama = self::normalisasiNama($prestasi->nama);

        if ($nama === '') {
            return false;
        }

        foreach (self::namaSantriJenjang($user) as $kandidat) {
            if (self::normalisasiNama($kandidat) === $nama) {
                return true;
            }
        }

        return false;
    }

    /**
     * Apakah nama santri termasuk jenjang pengguna (dipakai saat validasi
     * penyimpanan agar data baru tidak "hilang" dari daftar admin).
     */
    public static function cocokNama(?User $user, ?string $nama): bool
    {
        if (! $user) {
            return false;
        }

        if (Akses::adminTanpaJenjang($user)) {
            return false;
        }

        if (! Akses::dibatasiJenjang($user)) {
            return true;
        }

        $cari = self::normalisasiNama($nama);

        if ($cari === '') {
            return false;
        }

        foreach (self::namaSantriJenjang($user) as $kandidat) {
            if (self::normalisasiNama($kandidat) === $cari) {
                return true;
            }
        }

        return false;
    }

    /**
     * Nama santri pada jenjang pengguna (dari `santri` dan `students`).
     *
     * @return array<int, string>
     */
    private static function namaSantriJenjang(User $user): array
    {
        $nama = collect();

        $nilaiSantri = Akses::nilaiJenjangDb($user, Santri::class, 'jenjang_pendidikan');

        if ($nilaiSantri !== []) {
            $nama = $nama->merge(
                Santri::query()
                    ->whereIn('jenjang_pendidikan', $nilaiSantri)
                    ->whereNotNull('nama_lengkap')
                    ->pluck('nama_lengkap')
            );
        }

        $nilaiStudent = Akses::nilaiJenjangDb($user, Student::class, 'jenjang');

        if ($nilaiStudent !== []) {
            $nama = $nama->merge(
                Student::query()
                    ->whereIn('jenjang', $nilaiStudent)
                    ->whereNotNull('name')
                    ->pluck('name')
            );
        }

        return $nama->map(fn ($n) => trim((string) $n))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private static function normalisasiNama(?string $nama): string
    {
        $nama = strtolower(trim((string) $nama));
        $nama = preg_replace('/\s+/', ' ', $nama);

        return trim((string) $nama);
    }
}
