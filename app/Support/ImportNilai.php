<?php

namespace App\Support;

use App\Models\NilaiSantri;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Import nilai santri dari CSV tanpa dependency package.
 *
 * Kolom wajib: nomor_induk (atau santri_id) dan nilai. Kolom konteks
 * (jenjang, kelas, mapel, tahun, semester, kategori) boleh dikosongkan pada
 * CSV maka akan memakai konteks yang dipilih di halaman nilai; boleh juga
 * ditimpa per baris untuk mengimpor beberapa kelas/mapel sekaligus.
 *
 * Baris yang tidak valid dilewati satu per satu dan dilaporkan, sehingga
 * berkas berisi ratusan nilai tetap bisa diproses sekaligus.
 */
class ImportNilai
{
    /** @var array<string, array<int, string>> */
    private const KOLOM = [
        'santri_id'          => ['santri_id', 'id_santri'],
        'nomor_induk'        => ['nomor_induk', 'nis', 'nip', 'nomor_induk_santri'],
        'nama_santri'        => ['nama_santri', 'nama'],
        'mata_pelajaran_id'  => ['mata_pelajaran_id', 'mapel_id', 'id_mapel'],
        'kode_mapel'         => ['kode_mapel', 'kode', 'mapel_kode'],
        'jenjang'            => ['jenjang', 'jenjang_pendidikan'],
        'kelas'              => ['kelas'],
        'tahun_ajaran'       => ['tahun_ajaran', 'tahun'],
        'semester'           => ['semester'],
        'kategori'           => ['kategori'],
        'nilai'              => ['nilai', 'nilai_santri', 'skor'],
        'catatan'            => ['catatan', 'keterangan'],
    ];

    /**
     * Header resmi untuk berkas template.
     *
     * @return array<int, string>
     */
    public static function kolomTemplate(): array
    {
        return array_keys(self::KOLOM);
    }

    /**
     * Proses berkas CSV.
     *
     * @param  array{jenjang:string,kelas:string,tahun:string,semester:string,kategori:string,mapel:\App\Models\MataPelajaran|null}  $konteks
     * @return array{berhasil:int,gagal:int,dikembalikan:int,errors:array<int,string>}
     */
    public static function proses(string $path, ?User $operator, array $konteks): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new \RuntimeException('Berkas CSV tidak dapat dibaca.');
        }

        $hasil = [
            'berhasil'     => 0,
            'gagal'        => 0,
            'dikembalikan' => 0,
            'errors'       => [],
        ];

        try {
            $barisPertama = fgets($handle);
            if ($barisPertama === false || trim($barisPertama) === '') {
                throw new \RuntimeException('Berkas CSV kosong.');
            }

            // CSV dari Excel Windows memakai titik koma; dari tooling lain koma.
            $pemisah = substr_count($barisPertama, ';') > substr_count($barisPertama, ',') ? ';' : ',';
            rewind($handle);

            $header = fgetcsv($handle, 0, $pemisah);
            if (! $header) {
                throw new \RuntimeException('Baris header CSV tidak terbaca.');
            }

            $peta = [];
            foreach (self::KOLOM as $kunci => $alias) {
                $aliasBersih = array_map([self::class, 'bersihkan'], $alias);

                foreach ($header as $indeks => $nama) {
                    if (in_array(self::bersihkan((string) $nama), $aliasBersih, true)) {
                        $peta[$kunci] = $indeks;
                        break;
                    }
                }
            }

            if (! array_key_exists('nilai', $peta)
                || (! array_key_exists('nomor_induk', $peta) && ! array_key_exists('santri_id', $peta))) {
                throw new \RuntimeException('Header wajib tidak ditemukan. Sertakan nomor_induk (atau santri_id) dan nilai.');
            }

            // Muat satu kali daftar santri & mapel yang boleh diakses operator.
            $santri = Akademik::santriQuery($operator)
                ->select('id', 'nomor_induk', 'nama_lengkap', 'jenjang_pendidikan', 'kelas')
                ->get();

            $santriByNomor = $santri->keyBy(fn ($s) => strtoupper(trim((string) $s->nomor_induk)));
            $santriById = $santri->keyBy('id');

            $mapel = Akademik::mapelQuery($operator)
                ->where('aktif', true)
                ->get(['id', 'jenjang', 'kode', 'nama']);

            $mapelById = $mapel->keyBy('id');
            $mapelByKode = $mapel->keyBy(fn ($m) => Jenjang::normalisasi($m->jenjang).'|'.strtoupper(trim((string) $m->kode)));

            $baris = 1;
            while (($row = fgetcsv($handle, 0, $pemisah)) !== false) {
                $baris++;

                $kosong = collect($row)->every(fn ($nilai) => trim((string) $nilai) === '');
                if ($kosong) {
                    continue;
                }

                $data = [];
                foreach ($peta as $kunci => $indeks) {
                    $nilai = trim((string) ($row[$indeks] ?? ''));
                    $data[$kunci] = $nilai === '' ? null : $nilai;
                }

                $nilaiMentah = str_replace(',', '.', (string) ($data['nilai'] ?? ''));
                $data['nilai'] = $nilaiMentah === '' ? null : $nilaiMentah;

                $validator = Validator::make($data, [
                    'nilai'   => ['required', 'numeric', 'min:0', 'max:100'],
                    'catatan' => ['nullable', 'string', 'max:255'],
                ]);

                if ($validator->fails()) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: ".$validator->errors()->first();
                    continue;
                }

                if (! empty($data['santri_id'])) {
                    $santriDipilih = $santriById->get((int) $data['santri_id']);
                } else {
                    $kunciNomor = strtoupper(trim((string) ($data['nomor_induk'] ?? '')));
                    $santriDipilih = $santriByNomor->get($kunciNomor);
                }

                if (! $santriDipilih) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: santri tidak ditemukan atau di luar kewenangan Anda.";
                    continue;
                }

                if (! empty($data['nama_santri'])
                    && strcasecmp(trim((string) $data['nama_santri']), trim((string) $santriDipilih->nama_lengkap)) !== 0) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: nama santri tidak cocok dengan nomor induk.";
                    continue;
                }

                $jenjang = ! empty($data['jenjang'])
                    ? Jenjang::normalisasi($data['jenjang'])
                    : ($konteks['jenjang'] ?? null);

                if (! $jenjang || ! Akses::cocokJenjang($operator, $jenjang)) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: jenjang tidak dikenali atau di luar wewenang Anda.";
                    continue;
                }

                if (Jenjang::normalisasi($santriDipilih->jenjang_pendidikan) !== $jenjang) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: jenjang santri tidak cocok dengan konteks nilai.";
                    continue;
                }

                $kelas = trim((string) ($data['kelas'] ?? $konteks['kelas'] ?? ''));
                $kelasSantri = trim((string) $santriDipilih->kelas);
                if ($kelas === '' || strcasecmp($kelas, $kelasSantri) !== 0) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: kelas {$kelas} tidak cocok dengan kelas santri (".($kelasSantri ?: '-').').';
                    continue;
                }

                if (! empty($data['mata_pelajaran_id'])) {
                    $mapelDipilih = $mapelById->get((int) $data['mata_pelajaran_id']);
                } elseif (! empty($data['kode_mapel'])) {
                    $mapelDipilih = $mapelByKode->get($jenjang.'|'.strtoupper(trim((string) $data['kode_mapel'])));
                } else {
                    $mapelDipilih = $konteks['mapel'] ?? null;
                }

                if (! $mapelDipilih || Jenjang::normalisasi($mapelDipilih->jenjang) !== $jenjang) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: mata pelajaran tidak ditemukan, tidak aktif, atau bukan untuk jenjang ini.";
                    continue;
                }

                $tahun = trim((string) ($data['tahun_ajaran'] ?? $konteks['tahun'] ?? ''));
                if (! preg_match('/^\d{4}\/\d{4}$/', $tahun)) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: format tahun ajaran harus seperti 2026/2027.";
                    continue;
                }

                $semester = strtolower(trim((string) ($data['semester'] ?? $konteks['semester'] ?? '')));
                if (! array_key_exists($semester, Akademik::SEMESTER)) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: semester tidak dikenali (ganjil/genap).";
                    continue;
                }

                $kategori = strtolower(trim((string) ($data['kategori'] ?? $konteks['kategori'] ?? '')));
                if (! array_key_exists($kategori, Akademik::KATEGORI)) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: kategori nilai tidak dikenali (tugas/uts/uas/hafalan).";
                    continue;
                }

                $catatan = trim((string) ($data['catatan'] ?? '')) ?: null;
                $kunci = [
                    'santri_id'          => $santriDipilih->id,
                    'mata_pelajaran_id'  => $mapelDipilih->id,
                    'tahun_ajaran'       => $tahun,
                    'semester'           => $semester,
                    'kategori'           => $kategori,
                ];

                $dikembalikanBaris = false;

                try {
                    DB::transaction(function () use (
                        $kunci, $jenjang, $kelas, $data, $catatan, $operator, &$dikembalikanBaris
                    ) {
                        $row = NilaiSantri::firstOrNew($kunci);
                        $baru = ! $row->exists;
                        $berubah = $baru
                            || abs(((float) $row->nilai) - ((float) $data['nilai'])) > 0.001
                            || (string) $row->catatan !== (string) $catatan;

                        if ($berubah && ! $baru && $row->status === 'published') {
                            $dikembalikanBaris = true;
                        }

                        $row->fill([
                            'jenjang'         => $jenjang,
                            'kelas'           => $kelas,
                            'nilai'           => $data['nilai'],
                            'catatan'         => $catatan,
                            'diperbarui_oleh' => $operator?->id,
                        ]);

                        if ($baru) {
                            $row->created_by = $operator?->id;
                        }

                        if ($berubah) {
                            $row->status = 'draft';
                            $row->published_at = null;
                            $row->diterbitkan_oleh = null;
                        }

                        $row->save();
                    });
                } catch (\Throwable $e) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: gagal menyimpan (".$e->getMessage().')';
                    continue;
                }

                if ($dikembalikanBaris) {
                    $hasil['dikembalikan']++;
                }

                $hasil['berhasil']++;
            }
        } finally {
            fclose($handle);
        }

        return $hasil;
    }

    /** Normalisasi header: BOM dihapus, tanda baca jadi underscore. */
    private static function bersihkan(?string $nilai): string
    {
        $nilai = str_replace("\xEF\xBB\xBF", '', (string) $nilai);
        $nilai = strtolower($nilai);
        $nilai = preg_replace('/[^a-z0-9]+/', '_', $nilai);

        return trim((string) $nilai, '_');
    }
}
