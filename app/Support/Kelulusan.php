<?php

namespace App\Support;

use App\Models\Alumni;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Penanda kelulusan santri dan penghubungnya ke Data Alumni.
 *
 * Satu santri yang ditandai lulus otomatis dibuatkan satu baris data alumni
 * (relasi alumni.santri_id). Menjalankan ulang tidak membuat data ganda
 * karena selalu memakai santri_id sebagai kunci.
 */
class Kelulusan
{
    /** Apakah kolom kelulusan pada tabel santri sudah tersedia. */
    public static function siap(): bool
    {
        try {
            return Schema::hasColumn('santri', 'status_kelulusan')
                && Schema::hasColumn('santri', 'tahun_lulus');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Apakah tabel alumni sudah punya kolom penghubung santri_id. */
    public static function siapTautan(): bool
    {
        try {
            return self::siap() && Schema::hasColumn('alumni', 'santri_id');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Tandai santri lulus lalu sinkronkan data alumni.
     */
    public static function luluskan(Santri $santri, ?int $tahun = null): ?Alumni
    {
        $tahun = self::tahunValid($tahun) ?? (int) now()->year;

        $santri->forceFill([
            'status_kelulusan' => Santri::STATUS_LULUS,
            'tahun_lulus' => $tahun,
        ])->save();

        if (! self::siapTautan()) {
            return null;
        }

        return self::sinkronAlumni(
            $santri->fresh(['user', 'pendaftaranOnline']),
            $tahun
        );
    }

    /**
     * Batalkan kelulusan santri.
     *
     * Data alumni yang sebelumnya dibuat otomatis ikut dihapus agar tidak
     * tertinggal sebagai alumni yang tidak lagi sesuai dengan data santri.
     */
    public static function batalkan(Santri $santri): void
    {
        $santri->forceFill([
            'status_kelulusan' => Santri::STATUS_AKTIF,
            'tahun_lulus' => null,
        ])->save();

        if (self::siapTautan()) {
            Alumni::query()->where('santri_id', $santri->id)->delete();
        }
    }

    /**
     * Buat atau perbarui data alumni milik satu santri.
     *
     * Hanya data dasar yang disinkronkan; kegiatan, instansi, testimoni, dan
     * foto yang sudah diisi admin tidak pernah ditimpa.
     */
    public static function sinkronAlumni(Santri $santri, ?int $tahun = null): Alumni
    {
        $tahun = self::tahunValid($tahun)
            ?? self::tahunValid($santri->tahun_lulus)
            ?? (int) now()->year;

        // Utamakan relasi santri_id. Jika belum ada, coba tautkan data alumni
        // lama yang namanya sama agar tidak membuat baris ganda.
        /** @var Alumni|null $alumni */
        $alumni = Alumni::query()->where('santri_id', $santri->id)->first();

        if (! $alumni) {
            $alumni = Alumni::query()
                ->whereNull('santri_id')
                ->whereRaw('LOWER(nama) = ?', [mb_strtolower((string) $santri->nama_lengkap)])
                ->orderByDesc('tahun_lulus')
                ->first();
        }

        if (! $alumni) {
            $alumni = new Alumni;
        }

        $alumni->santri_id = $santri->id;
        $alumni->nama = $santri->nama_lengkap;
        $alumni->jenjang = self::labelJenjang($santri->jenjang_pendidikan);
        $alumni->tahun_lulus = $tahun;

        if (blank($alumni->alamat)) {
            $alumni->alamat = $santri->alamat;
        }

        if (blank($alumni->email)) {
            $alumni->email = $santri->user?->email ?: $santri->pendaftaranOnline?->email;
        }

        if (blank($alumni->no_hp)) {
            $alumni->no_hp = $santri->user?->phone ?: $santri->pendaftaranOnline?->no_hp_aktif;
        }

        // NISN & jenis kelamin hanya ada pada arsip PPDB, jadi ikut disalin
        // bila kolomnya sudah tersedia dan belum diisi admin.
        if (Alumni::siapKolom('nisn') && blank($alumni->nisn)) {
            $alumni->nisn = $santri->pendaftaranOnline?->nisn;
        }

        if (blank($alumni->jenis_kelamin)) {
            $kelamin = $santri->pendaftaranOnline?->jenis_kelamin;

            if (in_array($kelamin, ['L', 'P'], true)) {
                $alumni->jenis_kelamin = $kelamin;
            }
        }

        $alumni->save();

        return $alumni;
    }

    /**
     * Santri lulus yang belum memiliki data alumni (dibatasi jenjang user).
     *
     * @return Collection<int, Santri>
     */
    public static function santriLulusBelumAlumni(?User $user): Collection
    {
        if (! self::siapTautan()) {
            return new Collection;
        }

        $query = Santri::query()
            ->with(['user', 'pendaftaranOnline'])
            ->where('status_kelulusan', Santri::STATUS_LULUS)
            ->whereDoesntHave('alumni');

        if (Akses::dibatasiJenjang($user)) {
            $query->whereIn(
                'jenjang_pendidikan',
                Akses::nilaiJenjangDb($user, Santri::class, 'jenjang_pendidikan') ?: ['__tidak_ada__']
            );
        }

        return $query->orderByDesc('tahun_lulus')->orderBy('nama_lengkap')->get();
    }

    /**
     * Jumlah santri berstatus lulus pada jenjang user.
     */
    public static function jumlahLulus(?User $user): int
    {
        if (! self::siap()) {
            return 0;
        }

        $query = Santri::query()->where('status_kelulusan', Santri::STATUS_LULUS);

        if (Akses::dibatasiJenjang($user)) {
            $query->whereIn(
                'jenjang_pendidikan',
                Akses::nilaiJenjangDb($user, Santri::class, 'jenjang_pendidikan') ?: ['__tidak_ada__']
            );
        }

        return $query->count();
    }

    /** Label jenjang yang seragam dengan daftar jenjang pada Data Alumni. */
    public static function labelJenjang(?string $jenjang): ?string
    {
        $slug = Jenjang::normalisasi($jenjang);

        if (! $slug) {
            return $jenjang;
        }

        foreach (Alumni::JENJANG as $label) {
            if (Jenjang::normalisasi($label) === $slug) {
                return $label;
            }
        }

        return Jenjang::label($jenjang);
    }

    private static function tahunValid(mixed $tahun): ?int
    {
        if (! is_numeric($tahun)) {
            return null;
        }

        $tahun = (int) $tahun;

        return $tahun >= 1998 && $tahun <= now()->year + 1 ? $tahun : null;
    }
}
