<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\DokumenNilai;
use App\Models\NilaiSantri;
use App\Models\Santri;
use App\Support\Akademik;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class PortalAkademikController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $anakList = $user->anakQuery()->orderBy('nama_lengkap')->get();
        $anak = $this->pilihAnak($request, $anakList);

        if (! Akademik::siap()) {
            return view('portal.akademik.index', [
                'anakList' => $anakList,
                'anak' => $anak,
                'filter' => null,
                'opsi' => collect(),
                'data' => null,
                'dokumen' => collect(),
                'siap' => false,
            ]);
        }

        $filter = $this->filter($request, $anak);
        $data = $anak ? $this->data($anak, $filter) : null;

        return view('portal.akademik.index', [
            'anakList' => $anakList,
            'anak' => $anak,
            'filter' => $filter,
            'opsi' => $anak ? $this->opsi($anak) : collect(),
            'data' => $data,
            'dokumen' => $anak ? $this->dokumen($anak, $filter) : collect(),
            'siap' => true,
        ]);
    }

    public function cetak(Request $request)
    {
        $user = $request->user();
        $anakList = $user->anakQuery()->orderBy('nama_lengkap')->get();
        $anak = $this->pilihAnak($request, $anakList);

        abort_unless($anak, 404, 'Pilih anak terlebih dahulu.');
        abort_unless(Akademik::siap(), 404, 'Modul nilai belum tersedia.');

        $filter = $this->filter($request, $anak);

        return view('portal.akademik.cetak', [
            'anak' => $anak,
            'filter' => $filter,
            'data' => $this->data($anak, $filter),
        ]);
    }

    /* ============================================================= Helper */

    private function pilihAnak(Request $request, Collection $anakList): ?Santri
    {
        if ($anakList->isEmpty()) {
            return null;
        }

        $id = (int) $request->input('anak');

        if ($id === 0) {
            return $anakList->first();
        }

        $anak = $anakList->firstWhere('id', $id);

        // Jangan pernah mengandalkan dropdown saja: kepemilikan anak dicek
        // di server pada setiap request.
        abort_unless($anak, 403, 'Data anak ini bukan milik akun Anda.');

        return $anak;
    }

    /**
     * @return array{tahun:string,semester:string}
     */
    private function filter(Request $request, ?Santri $anak): array
    {
        $tahun = trim((string) $request->input('tahun_ajaran'));
        $semester = strtolower((string) $request->input('semester'));

        if ($anak && $tahun === '') {
            $terakhir = $this->periodeTerakhir($anak->id);

            $tahun = $terakhir?->tahun_ajaran ?? Akademik::tahunAjaranSekarang();
        }

        if ($anak && ! array_key_exists($semester, Akademik::SEMESTER)) {
            $terakhir = $this->periodeTerakhir($anak->id, $tahun);

            $semester = $terakhir?->semester ?? Akademik::semesterSekarang();
        }

        return [
            'tahun' => $tahun ?: Akademik::tahunAjaranSekarang(),
            'semester' => array_key_exists($semester, Akademik::SEMESTER)
                ? $semester
                : Akademik::semesterSekarang(),
        ];
    }

    /** Pilihan tahun/semester yang benar-benar punya nilai atau rapor terbit. */
    private function opsi(Santri $anak): Collection
    {
        $nilai = NilaiSantri::query()
            ->where('santri_id', $anak->id)
            ->where('status', 'published')
            ->select('tahun_ajaran', 'semester')
            ->distinct()
            ->get();

        $dokumen = $this->dokumenSiap()
            ? DokumenNilai::query()
                ->where('santri_id', $anak->id)
                ->where('status', 'published')
                ->select('tahun_ajaran', 'semester')
                ->distinct()
                ->get()
            : collect();

        return $nilai
            ->concat($dokumen)
            ->unique(fn ($baris) => $baris->tahun_ajaran.'|'.$baris->semester)
            ->sortByDesc(fn ($baris) => $baris->tahun_ajaran.'|'.$baris->semester)
            ->values();
    }

    /**
     * Periode terbaru yang punya nilai atau rapor PDF terbit.
     * Dipakai agar halaman tetap membuka periode yang benar meskipun
     * santri hanya punya rapor PDF tanpa nilai angka.
     */
    private function periodeTerakhir(int $santriId, ?string $tahun = null): ?object
    {
        $nilai = NilaiSantri::query()
            ->where('santri_id', $santriId)
            ->where('status', 'published')
            ->when($tahun, fn ($q) => $q->where('tahun_ajaran', $tahun))
            ->orderByDesc('tahun_ajaran')
            ->orderByDesc('semester')
            ->first();

        $dokumen = $this->dokumenSiap()
            ? DokumenNilai::query()
                ->where('santri_id', $santriId)
                ->where('status', 'published')
                ->when($tahun, fn ($q) => $q->where('tahun_ajaran', $tahun))
                ->orderByDesc('tahun_ajaran')
                ->orderByDesc('semester')
                ->first()
            : null;

        if ($nilai && $dokumen) {
            $kunciNilai = $nilai->tahun_ajaran.'|'.$nilai->semester;
            $kunciDokumen = $dokumen->tahun_ajaran.'|'.$dokumen->semester;

            return $kunciDokumen > $kunciNilai ? $dokumen : $nilai;
        }

        return $dokumen ?? $nilai;
    }

    /** Rapor PDF terbit untuk satu anak pada periode terpilih. */
    private function dokumen(Santri $anak, array $filter): Collection
    {
        if (! $this->dokumenSiap()) {
            return collect();
        }

        return DokumenNilai::query()
            ->with('pengunggah')
            ->where('santri_id', $anak->id)
            ->where('status', 'published')
            ->where('tahun_ajaran', $filter['tahun'])
            ->where('semester', $filter['semester'])
            ->orderByDesc('created_at')
            ->get();
    }

    private function dokumenSiap(): bool
    {
        return Akademik::siap() && DokumenNilai::siap();
    }

    /** Pastikan dokumen benar-benar milik anak dari akun wali ini. */
    private function pastikanMilik(Request $request, DokumenNilai $dokumen): void
    {
        $milik = $request->user()
            ->anakQuery()
            ->whereKey($dokumen->santri_id)
            ->exists();

        abort_unless($milik, 403, 'Dokumen ini bukan milik akun Anda.');
        abort_unless(
            filled($dokumen->file_path) && Storage::disk('public')->exists($dokumen->file_path),
            404,
            'Berkas PDF tidak ditemukan.'
        );
    }

    /** Tampilkan PDF langsung di browser (untuk pratinjau/iframe). */
    public function dokumenLihat(Request $request, DokumenNilai $dokumen)
    {
        $this->pastikanMilik($request, $dokumen);

        return response()->file(
            Storage::disk('public')->path($dokumen->file_path),
            ['Content-Type' => 'application/pdf']
        );
    }

    /** Unduh PDF rapor ke perangkat wali. */
    public function dokumenUnduh(Request $request, DokumenNilai $dokumen)
    {
        $this->pastikanMilik($request, $dokumen);

        return Storage::disk('public')->download(
            $dokumen->file_path,
            $dokumen->file_name ?: 'rapor-'.$dokumen->tahun_ajaran.'.pdf'
        );
    }

    /**
     * Susun nilai per mata pelajaran untuk satu anak.
     *
     * @return array<string, mixed>
     */
    private function data(Santri $anak, array $filter): array
    {
        $rows = NilaiSantri::query()
            ->with('mataPelajaran')
            ->where('santri_id', $anak->id)
            ->where('status', 'published')
            ->where('tahun_ajaran', $filter['tahun'])
            ->where('semester', $filter['semester'])
            ->get();

        $mapel = $rows->groupBy('mata_pelajaran_id')->map(function ($grup) {
            $ambil = fn (string $kategori) => $grup->firstWhere('kategori', $kategori)?->nilai;
            $angka = $grup->pluck('nilai')->map(fn ($n) => (float) $n);
            $akhir = round((float) $angka->avg(), 2);
            $kkm = (float) ($grup->first()->mataPelajaran?->kkm ?? 70);

            return [
                'mapel' => $grup->first()->mataPelajaran,
                'tugas' => $ambil('tugas'),
                'uts' => $ambil('uts'),
                'uas' => $ambil('uas'),
                'hafalan' => $ambil('hafalan'),
                'akhir' => $akhir,
                'predikat' => Akademik::predikat($akhir),
                'tuntas' => $akhir >= $kkm,
                'catatan' => $grup->first(fn ($n) => filled($n->catatan))?->catatan,
            ];
        })->sortBy(fn ($r) => $r['mapel']?->nama)->values();

        $rata = $mapel->avg('akhir');
        $terakhir = $rows->max('published_at');
        $catatan = $mapel->first(fn ($r) => filled($r['catatan']));

        return [
            'mapel' => $mapel,
            'rata' => $rata,
            'predikat' => Akademik::predikat($rata),
            'jumlah' => $mapel->count(),
            'tuntas' => $mapel->where('tuntas', true)->count(),
            'catatan' => $catatan['catatan'] ?? null,
            'terakhir' => $terakhir,
            'baru' => $terakhir && $terakhir->gte(now()->subDays(14)),
        ];
    }
}
