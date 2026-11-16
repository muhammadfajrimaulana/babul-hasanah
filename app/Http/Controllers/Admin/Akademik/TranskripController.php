<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\NilaiSantri;
use App\Models\Santri;
use App\Support\Akademik;
use App\Support\Akses;
use App\Support\Jenjang;
use Illuminate\Http\Request;

class TranskripController extends Controller
{
    public function index(Request $request)
    {
        if (! Akademik::siap()) {
            return view('dashboard.akademik.belum-siap');
        }

        $user = $request->user();
        $filter = $this->filter($request);

        $santris = Akademik::santriQuery($user, $filter['jenjang'])
            ->when($filter['kelas'], fn ($q) => $q->where('kelas', $filter['kelas']))
            ->orderBy('nama_lengkap')
            ->get();

        $agregat = NilaiSantri::query()
            ->selectRaw('santri_id, AVG(nilai) as rata, COUNT(DISTINCT mata_pelajaran_id) as jumlah')
            ->whereIn('santri_id', $santris->pluck('id'))
            ->where('status', 'published')
            ->where('tahun_ajaran', $filter['tahun'])
            ->where('semester', $filter['semester'])
            ->groupBy('santri_id')
            ->get()
            ->keyBy('santri_id');

        return view('dashboard.akademik.transkrip.index', [
            'filter' => $filter,
            'santris' => $santris,
            'agregat' => $agregat,
            'daftarKelas' => Akademik::daftarKelas($user, $filter['jenjang']),
            'daftarJenjang' => $this->daftarJenjang($user),
            'tahunPilihan' => Akademik::tahunAjaranPilihan(),
        ]);
    }

    public function show(Request $request, Santri $santri)
    {
        if (! Akademik::siap()) {
            return view('dashboard.akademik.belum-siap');
        }

        $this->pastikanAkses($santri);

        $filter = $this->filter($request, $santri);

        return view('dashboard.akademik.transkrip.show', [
            'santri' => $santri,
            'filter' => $filter,
            'baris' => $this->barisNilai($santri, $filter, false),
            'ringkasan' => $this->ringkasan($santri, $filter, false),
            'tahunPilihan' => Akademik::tahunAjaranPilihan(),
        ]);
    }

    /** Versi cetak: hanya nilai yang sudah terbit (dokumen resmi). */
    public function cetak(Request $request, Santri $santri)
    {
        if (! Akademik::siap()) {
            return view('dashboard.akademik.belum-siap');
        }

        $this->pastikanAkses($santri);

        $filter = $this->filter($request, $santri);

        return view('dashboard.akademik.transkrip.cetak', [
            'santri' => $santri,
            'filter' => $filter,
            'baris' => $this->barisNilai($santri, $filter, true),
            'ringkasan' => $this->ringkasan($santri, $filter, true),
        ]);
    }

    /* ============================================================= Helper */

    private function filter(Request $request, ?Santri $santri = null): array
    {
        $user = $request->user();
        $jenjang = Akses::jenjangSlug($user)
            ?: Jenjang::normalisasi($request->input('jenjang'))
            ?: Jenjang::normalisasi($santri?->jenjang_pendidikan);

        $tahun = trim((string) $request->input('tahun_ajaran'));
        $semester = strtolower((string) $request->input('semester'));

        if ($santri && ($tahun === '' || ! array_key_exists($semester, Akademik::SEMESTER))) {
            $terakhir = NilaiSantri::query()
                ->where('santri_id', $santri->id)
                ->orderByDesc('tahun_ajaran')
                ->orderByDesc('semester')
                ->first();

            $tahun = $tahun ?: ($terakhir?->tahun_ajaran ?? Akademik::tahunAjaranSekarang());
            $semester = array_key_exists($semester, Akademik::SEMESTER)
                ? $semester
                : ($terakhir?->semester ?? Akademik::semesterSekarang());
        }

        $tahun = $tahun ?: Akademik::tahunAjaranSekarang();
        $semester = array_key_exists($semester, Akademik::SEMESTER)
            ? $semester
            : Akademik::semesterSekarang();

        return [
            'jenjang' => $jenjang,
            'kelas' => trim((string) $request->input('kelas')) ?: null,
            'tahun' => $tahun,
            'semester' => $semester,
        ];
    }

    private function pastikanAkses(Santri $santri): void
    {
        abort_if(
            Akses::adminTanpaJenjang(auth()->user()),
            403,
            'Akun admin ini belum memiliki jenjang. Hubungi Super Admin.'
        );

        abort_unless(
            Akses::cocokJenjang(auth()->user(), $santri->jenjang_pendidikan),
            403,
            'Data santri ini berada di luar jenjang yang Anda tangani.'
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function barisNilai(Santri $santri, array $filter, bool $hanyaTerbit): array
    {
        $query = NilaiSantri::query()
            ->with('mataPelajaran')
            ->where('santri_id', $santri->id)
            ->where('tahun_ajaran', $filter['tahun'])
            ->where('semester', $filter['semester']);

        if ($hanyaTerbit) {
            $query->where('status', 'published');
        }

        $semua = $query->get()->groupBy('mata_pelajaran_id');

        return $semua->map(function ($grup) {
            $ambil = fn (string $kategori) => $grup->firstWhere('kategori', $kategori)?->nilai;
            $rata = round((float) $grup->avg('nilai'), 2);
            $catatan = $grup->first(fn ($n) => filled($n->catatan))?->catatan;

            return [
                'mapel' => $grup->first()->mataPelajaran,
                'tugas' => $ambil('tugas'),
                'uts' => $ambil('uts'),
                'uas' => $ambil('uas'),
                'hafalan' => $ambil('hafalan'),
                'akhir' => $rata,
                'predikat' => Akademik::predikat($rata),
                'catatan' => $catatan,
                'ada_draft' => $grup->contains(fn ($n) => $n->status !== 'published'),
            ];
        })->sortBy(fn ($r) => $r['mapel']?->nama)->values()->all();
    }

    private function ringkasan(Santri $santri, array $filter, bool $hanyaTerbit): array
    {
        $query = NilaiSantri::query()
            ->where('santri_id', $santri->id)
            ->where('tahun_ajaran', $filter['tahun'])
            ->where('semester', $filter['semester']);

        if ($hanyaTerbit) {
            $query->where('status', 'published');
        }

        $nilai = $query->get();

        return [
            'rata' => $nilai->avg('nilai'),
            'predikat' => Akademik::predikat($nilai->avg('nilai')),
            'mapel' => $nilai->pluck('mata_pelajaran_id')->unique()->count(),
            'kkm' => $nilai->filter(fn ($n) => (float) $n->nilai >= (float) ($n->mataPelajaran?->kkm ?? 70))->count(),
        ];
    }

    private function daftarJenjang($user): array
    {
        if (Akses::adminTanpaJenjang($user)) {
            return [];
        }

        $slug = Akses::jenjangSlug($user);

        if (! $slug) {
            return Jenjang::semua();
        }

        $jenjang = Jenjang::cari($slug);

        return $jenjang ? [$jenjang] : [];
    }
}
