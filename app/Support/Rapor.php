<?php

namespace App\Support;

use App\Models\NilaiSantri;
use App\Models\RaporSantri;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Hitung ulang rekap rapor (rata-rata & peringkat) dari nilai yang sudah
 * diterbitkan. Catatan wali kelas dan status terbit tidak ditimpa saat
 * perhitungan dijalankan ulang.
 */
class Rapor
{
    public static function siap(): bool
    {
        try {
            return Schema::hasTable('nilai_santri') && Schema::hasTable('rapor_santri');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @return array{dibuat:int,diperbarui:int,dilewati:int}
     */
    public static function hitungUlang(
        string $tahun,
        string $semester,
        ?User $operator = null,
        ?string $jenjang = null,
        ?string $kelas = null
    ): array {
        $slug = $jenjang ? Jenjang::normalisasi($jenjang) : null;

        $nilai = NilaiSantri::query()
            ->where('status', 'published')
            ->where('tahun_ajaran', $tahun)
            ->where('semester', $semester)
            ->when($slug, fn ($q) => $q->where('jenjang', $slug))
            ->when($kelas, fn ($q) => $q->where('kelas', $kelas))
            ->get(['santri_id', 'jenjang', 'kelas', 'mata_pelajaran_id', 'nilai']);

        $rekap = $nilai->groupBy('santri_id')->map(function ($grup) {
            $rataMapel = $grup->groupBy('mata_pelajaran_id')
                ->map(fn ($g) => round((float) $g->avg('nilai'), 2));

            return [
                'jenjang' => (string) $grup->first()->jenjang,
                'kelas'   => (string) $grup->first()->kelas,
                'rata'    => round((float) $rataMapel->avg(), 2),
            ];
        });

        // Peringkat dihitung per jenjang + kelas. Nilai sama mendapat peringkat sama.
        $rekap->groupBy(fn ($data) => $data['jenjang'].'|'.$data['kelas'])
            ->each(function ($grup) use ($rekap) {
                $jumlah = $grup->count();
                $peringkat = 0;
                $posisi = 0;
                $sebelumnya = null;

                $grup->sortByDesc('rata')->each(function ($data, $santriId) use (
                    $rekap, $jumlah, &$peringkat, &$posisi, &$sebelumnya
                ) {
                    $posisi++;

                    if ($sebelumnya === null || abs(((float) $data['rata']) - $sebelumnya) > 0.001) {
                        $peringkat = $posisi;
                    }

                    $sebelumnya = (float) $data['rata'];

                    $baris = $rekap->get($santriId);
                    $baris['peringkat'] = $peringkat;
                    $baris['jumlah'] = $jumlah;
                    $rekap->put($santriId, $baris);
                });
            });

        $hasil = ['dibuat' => 0, 'diperbarui' => 0, 'dilewati' => 0];

        foreach ($rekap as $santriId => $data) {
            try {
                DB::transaction(function () use ($santriId, $data, $tahun, $semester, $operator, &$hasil) {
                    $row = RaporSantri::firstOrNew([
                        'santri_id' => $santriId,
                        'tahun_ajaran' => $tahun,
                        'semester' => $semester,
                    ]);

                    $baru = ! $row->exists;

                    $row->fill([
                        'jenjang'         => $data['jenjang'],
                        'kelas'           => $data['kelas'],
                        'rata_rata'       => $data['rata'],
                        'peringkat'       => $data['peringkat'],
                        'jumlah_santri'   => $data['jumlah'],
                        'diperbarui_oleh' => $operator?->id,
                    ]);

                    if ($baru) {
                        $row->created_by = $operator?->id;
                        $row->status = 'draft';
                    }

                    $row->save();

                    $hasil[$baru ? 'dibuat' : 'diperbarui']++;
                });
            } catch (\Throwable $e) {
                $hasil['dilewati']++;
            }
        }

        return $hasil;
    }

    public static function untukPortal(Santri $anak, string $tahun, string $semester): ?RaporSantri
    {
        if (! self::siap()) {
            return null;
        }

        return RaporSantri::query()
            ->where('santri_id', $anak->id)
            ->where('tahun_ajaran', $tahun)
            ->where('semester', $semester)
            ->published()
            ->first();
    }
}
