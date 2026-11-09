<?php

namespace App\Support;

use App\Models\Alumni;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use Illuminate\Support\Facades\Schema;

/**
 * Rekapitulasi data santri, pendaftar, dan alumni per jenjang.
 *
 * Nilai jenjang di database bisa berbeda penulisan (mis. "MDTA" vs
 * "Madrasah Diniyah Takmiliyah"), sehingga semuanya dinormalisasi lebih dulu
 * lewat App\Support\Jenjang.
 */
class RekapJenjang
{
    /**
     * Ringkasan seluruh 7 jenjang.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function ringkasan(): array
    {
        $hasil = [];

        foreach (Jenjang::semua() as $jenjang) {
            $hasil[$jenjang['slug']] = [
                'slug' => $jenjang['slug'],
                'kode' => $jenjang['kode'],
                'nama' => $jenjang['nama'],
                'deskripsi' => $jenjang['deskripsi'],
                'ikon' => $jenjang['ikon'],
                'warna' => $jenjang['warna'],
                'santri' => 0,
                'pendaftar' => 0,
                'alumni' => 0,
                'diterima' => 0,
                'menunggu' => 0,
                'ditolak' => 0,
            ];
        }

        self::akumulasi(Santri::class, 'jenjang_pendidikan', $hasil, 'santri');
        self::akumulasi(Alumni::class, 'jenjang', $hasil, 'alumni');
        self::akumulasiPendaftar($hasil);

        return array_values($hasil);
    }

    /**
     * Nilai asli di database yang termasuk dalam satu jenjang.
     *
     * @return array<int, string>
     */
    public static function nilaiDb(string $model, string $kolom, string $slug): array
    {
        if (! self::siap($model)) {
            return [];
        }

        try {
            return $model::query()
                ->whereNotNull($kolom)
                ->distinct()
                ->pluck($kolom)
                ->filter(fn ($nilai) => Jenjang::normalisasi($nilai) === $slug)
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /** @return array<int, array{label: string, jml: int}> */
    public static function santriPerKelas(array $nilaiJenjang): array
    {
        if (empty($nilaiJenjang) || ! self::siap(Santri::class)) {
            return [];
        }

        try {
            return Santri::query()
                ->whereIn('jenjang_pendidikan', $nilaiJenjang)
                ->whereNotNull('kelas')
                ->selectRaw('kelas as label, COUNT(*) as jml')
                ->groupBy('kelas')
                ->orderBy('kelas')
                ->get()
                ->map(fn ($r) => [
                    'label' => (string) $r->label,
                    'jml' => (int) $r->jml,
                ])
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /** @return array<int, array{label: string, jml: int, warna: string}> */
    public static function statusPendaftar(array $nilaiJenjang): array
    {
        if (empty($nilaiJenjang) || ! self::siap(PendaftaranOnline::class)) {
            return [];
        }

        try {
            $baris = PendaftaranOnline::query()
                ->whereIn('jenjang_tujuan', $nilaiJenjang)
                ->selectRaw('status_pendaftaran as status, COUNT(*) as jml')
                ->groupBy('status_pendaftaran')
                ->get();
        } catch (\Throwable $e) {
            return [];
        }

        $warna = [
            'diterima' => '#12a67a',
            'menunggu' => '#b45309',
            'ditolak' => '#e11d48',
        ];

        return $baris->map(function ($r) use ($warna) {
            $status = strtolower(trim((string) $r->status));
            $label = match (true) {
                in_array($status, ['diterima', 'accepted'], true) => 'Diterima',
                in_array($status, ['ditolak', 'rejected'], true) => 'Ditolak',
                $status === '' => 'Belum diverifikasi',
                default => ucfirst($status),
            };
            $kunci = match (true) {
                in_array($status, ['diterima', 'accepted'], true) => 'diterima',
                in_array($status, ['ditolak', 'rejected'], true) => 'ditolak',
                default => 'menunggu',
            };

            return [
                'label' => $label,
                'jml' => (int) $r->jml,
                'warna' => $warna[$kunci],
            ];
        })->all();
    }

    public static function siap(string $model): bool
    {
        try {
            return class_exists($model) && Schema::hasTable((new $model)->getTable());
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** @param array<string, array<string, mixed>> $hasil */
    private static function akumulasi(string $model, string $kolom, array &$hasil, string $kunci): void
    {
        if (! self::siap($model)) {
            return;
        }

        try {
            $baris = $model::query()
                ->selectRaw("{$kolom} as jenjang, COUNT(*) as jml")
                ->whereNotNull($kolom)
                ->groupBy($kolom)
                ->get();
        } catch (\Throwable $e) {
            return;
        }

        foreach ($baris as $r) {
            $slug = Jenjang::normalisasi($r->jenjang);

            if ($slug && isset($hasil[$slug])) {
                $hasil[$slug][$kunci] += (int) $r->jml;
            }
        }
    }

    /** @param array<string, array<string, mixed>> $hasil */
    private static function akumulasiPendaftar(array &$hasil): void
    {
        if (! self::siap(PendaftaranOnline::class)) {
            return;
        }

        try {
            $baris = PendaftaranOnline::query()
                ->selectRaw('jenjang_tujuan as jenjang, status_pendaftaran as status, COUNT(*) as jml')
                ->whereNotNull('jenjang_tujuan')
                ->groupBy('jenjang_tujuan', 'status_pendaftaran')
                ->get();
        } catch (\Throwable $e) {
            return;
        }

        foreach ($baris as $r) {
            $slug = Jenjang::normalisasi($r->jenjang);

            if (! $slug || ! isset($hasil[$slug])) {
                continue;
            }

            $jml = (int) $r->jml;
            $status = strtolower(trim((string) $r->status));
            $hasil[$slug]['pendaftar'] += $jml;

            if (in_array($status, ['diterima', 'accepted'], true)) {
                $hasil[$slug]['diterima'] += $jml;
            } elseif (in_array($status, ['ditolak', 'rejected'], true)) {
                $hasil[$slug]['ditolak'] += $jml;
            } else {
                $hasil[$slug]['menunggu'] += $jml;
            }
        }
    }
}
