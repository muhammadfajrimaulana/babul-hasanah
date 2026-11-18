<?php

namespace App\Support;

use App\Models\DokumenNilai;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Aturan bersama penyimpanan rapor nilai (PDF).
 *
 * Dipakai oleh dashboard Admin Jenjang (unggah satu per satu) dan halaman
 * akademik (unggah massal). Berkas disimpan di storage/app/public/nilai/rapor
 * dan langsung berstatus terbit agar muncul di Portal Wali Santri.
 */
class RaporNilai
{
    /** Disk & folder penyimpanan berkas rapor. */
    public const DISK = 'public';

    public const FOLDER = 'nilai/rapor';

    /** Batas ukuran satu berkas PDF (KB). */
    public const MAKS_KB = 10240; // 10 MB

    /** Apakah tabel dokumen nilai sudah tersedia. */
    public static function siap(): bool
    {
        return DokumenNilai::siap();
    }

    /** Judul bawaan bila pengguna tidak mengisi judul rapor. */
    public static function judulOtomatis(?string $semester, string $tahun): string
    {
        return 'Rapor '.Akademik::labelSemester($semester).' '.$tahun;
    }

    /**
     * Simpan (atau ganti) rapor PDF untuk satu santri.
     *
     * Kombinasi santri + tahun ajaran + semester bersifat unik: unggah ulang
     * menimpa berkas lama. Berkas lama hanya dihapus setelah berkas baru
     * berhasil tersimpan, sehingga kegagalan simpan tidak menghilangkan data.
     */
    public static function simpan(
        Santri $santri,
        UploadedFile $file,
        string $tahun,
        string $semester,
        ?User $user = null,
        ?string $judul = null
    ): DokumenNilai {
        $path = $file->store(self::FOLDER, self::DISK);

        $dokumen = DokumenNilai::firstOrNew([
            'santri_id' => $santri->id,
            'tahun_ajaran' => $tahun,
            'semester' => $semester,
        ]);

        $lama = $dokumen->exists ? $dokumen->file_path : null;

        $dokumen->fill([
            'jenjang' => Jenjang::normalisasi($santri->jenjang_pendidikan)
                ?? ($santri->jenjang_pendidikan ?: '-'),
            'kelas' => $santri->kelas ?: '-',
            'judul' => filled($judul) ? trim((string) $judul) : self::judulOtomatis($semester, $tahun),
            'file_path' => $path,
            'file_name' => mb_substr($file->getClientOriginalName(), 0, 200),
            'file_size' => (int) $file->getSize(),
            'status' => 'published',
            'diunggah_oleh' => $user?->id,
            'published_at' => now(),
        ]);

        try {
            $dokumen->save();
        } catch (\Throwable $e) {
            Storage::disk(self::DISK)->delete($path);

            throw $e;
        }

        if ($lama && $lama !== $path) {
            Storage::disk(self::DISK)->delete($lama);
        }

        return $dokumen;
    }

    /**
     * Cocokkan nama berkas dengan santri.
     *
     * Urutan pencocokan:
     * 1. Nomor induk (NIS) sebagai token utuh, mis. "rapor-12345-ahmad.pdf".
     * 2. Nama lengkap yang sudah dinormalisasi (tanpa tanda baca/spasi ganda).
     *
     * Bila hasil cocok lebih dari satu santri dan tidak bisa dibedakan dengan
     * aman, berkas dianggap ambigu dan tidak disimpan agar tidak salah unggah.
     *
     * @param  Collection<int, Santri>  $santris
     * @return array{santri: Santri|null, alasan: string|null, cara: string|null}
     */
    public static function cocokkan(string $namaFile, Collection $santris): array
    {
        $dasar = pathinfo($namaFile, PATHINFO_FILENAME);
        $dasar = preg_replace('/[^a-z0-9]+/i', ' ', (string) $dasar) ?? '';
        $dasar = trim(preg_replace('/\s+/', ' ', $dasar) ?? '');
        $padBerkas = ' '.strtolower($dasar).' ';

        $token = $dasar === '' ? [] : explode(' ', strtolower($dasar));

        // Gabungan 2–3 token berurutan, supaya NIS seperti "S-001"
        // (ditulis terpisah oleh tanda baca) tetap bisa dikenali.
        $kandidat = $token;
        $jumlah = count($token);

        for ($i = 0; $i < $jumlah; $i++) {
            for ($ambil = 2; $ambil <= 3 && $i + $ambil <= $jumlah; $ambil++) {
                $kandidat[] = implode('', array_slice($token, $i, $ambil));
            }
        }

        // 1) Cocokkan nomor induk (token utuh).
        $cocokNis = $santris
            ->filter(function (Santri $santri) use ($kandidat) {
                $nis = self::normalisasiKunci($santri->nomor_induk);

                return $nis !== '' && in_array($nis, $kandidat, true);
            })
            ->values();

        if ($cocokNis->count() === 1) {
            return ['santri' => $cocokNis->first(), 'alasan' => null, 'cara' => 'nis'];
        }

        if ($cocokNis->count() > 1) {
            return [
                'santri' => null,
                'alasan' => 'NIS pada nama berkas cocok dengan lebih dari satu santri ('
                    .$cocokNis->take(3)->pluck('nama_lengkap')->implode(', ')
                    .').',
                'cara' => null,
            ];
        }

        // 2) Cocokkan nama lengkap yang sudah dinormalisasi.
        $cocokNama = $santris
            ->map(function (Santri $santri) {
                return [
                    'santri' => $santri,
                    'kunci' => self::normalisasiNama($santri->nama_lengkap),
                ];
            })
            ->filter(function (array $baris) use ($padBerkas) {
                return $baris['kunci'] !== ''
                    && str_contains($padBerkas, ' '.$baris['kunci'].' ');
            })
            ->values();

        if ($cocokNama->count() === 1) {
            return ['santri' => $cocokNama->first()['santri'], 'alasan' => null, 'cara' => 'nama'];
        }

        if ($cocokNama->count() > 1) {
            // Pilih nama paling spesifik (paling panjang) bila berbeda panjang.
            $panjang = $cocokNama->pluck('kunci')->map(fn ($k) => mb_strlen($k))->max();
            $terpanjang = $cocokNama->filter(
                fn (array $baris) => mb_strlen($baris['kunci']) === $panjang
            );

            if ($terpanjang->count() === 1) {
                return ['santri' => $terpanjang->first()['santri'], 'alasan' => null, 'cara' => 'nama'];
            }

            return [
                'santri' => null,
                'alasan' => 'Nama pada berkas cocok dengan lebih dari satu santri ('
                    .$terpanjang->take(3)->pluck('santri.nama_lengkap')->implode(', ')
                    .').',
                'cara' => null,
            ];
        }

        return [
            'santri' => null,
            'alasan' => 'Tidak ada santri pada jenjang ini yang cocok dengan nama berkas. '
                .'Sertakan NIS atau nama lengkap santri pada nama file.',
            'cara' => null,
        ];
    }

    /** Huruf/angka saja, huruf kecil — untuk pencocokan NIS. */
    private static function normalisasiKunci(?string $teks): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(trim((string) $teks))) ?? '';
    }

    /** Nama ternormalisasi: huruf kecil, tanpa tanda baca, spasi tunggal. */
    private static function normalisasiNama(?string $teks): string
    {
        $teks = preg_replace('/[^a-z0-9]+/i', ' ', strtolower(trim((string) $teks))) ?? '';

        return trim(preg_replace('/\s+/', ' ', $teks) ?? '');
    }
}
