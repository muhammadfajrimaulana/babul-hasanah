<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\RaporSantri;
use App\Support\Akademik;
use App\Support\Akses;
use App\Support\CatatanAktivitas;
use App\Support\Jenjang;
use App\Support\Rapor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Rapor rekap: rata-rata & peringkat dihitung dari nilai published,
 * catatan wali kelas diisi manual, lalu rapor diterbitkan ke portal wali.
 */
class RaporController extends Controller
{
    public function index(Request $request)
    {
        if (! Akademik::siap() || ! Rapor::siap()) {
            return view('dashboard.akademik.belum-siap');
        }

        $user = $request->user();
        $slugAdmin = Akses::jenjangSlug($user);
        $jenjang = $slugAdmin
            ?: Jenjang::normalisasi($request->query('jenjang'));
        $kelas = trim((string) $request->query('kelas')) ?: null;
        $tahun = trim((string) $request->query('tahun_ajaran')) ?: Akademik::tahunAjaranSekarang();
        $semester = strtolower((string) $request->query('semester')) ?: Akademik::semesterSekarang();

        if (! array_key_exists($semester, Akademik::SEMESTER)) {
            $semester = Akademik::semesterSekarang();
        }

        $idSantri = Akademik::santriQuery($user, $jenjang)
            ->when($kelas, fn ($q) => $q->where('kelas', $kelas))
            ->pluck('id')
            ->all();

        $rapor = RaporSantri::query()
            ->with('santri')
            ->whereIn('santri_id', $idSantri ?: [0])
            ->where('tahun_ajaran', $tahun)
            ->where('semester', $semester)
            ->get()
            ->sortBy(fn ($row) => [
                (string) $row->kelas,
                (int) ($row->peringkat ?? 9999),
                (string) $row->santri?->nama_lengkap,
            ])
            ->values();

        return view('dashboard.akademik.rapor.index', [
            'rapor' => $rapor,
            'filter' => [
                'jenjang' => $jenjang,
                'kelas' => $kelas,
                'tahun' => $tahun,
                'semester' => $semester,
            ],
            'daftarJenjang' => $this->daftarJenjang($user),
            'daftarKelas' => Akademik::daftarKelas($user, $jenjang),
            'bisaUbah' => (bool) $user->bisa('akademik.ubah'),
            'bisaTerbit' => (bool) $user->bisa('akademik.terbit'),
        ]);
    }

    /**
     * Hitung ulang rata-rata & peringkat dari nilai yang sudah published.
     * Tidak menimpa catatan wali kelas dan tidak mengubah status rapor.
     */
    public function hitungUlang(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.ubah'), 403);
        abort_unless(Rapor::siap(), 404, 'Tabel rapor belum tersedia.');

        $data = $request->validate([
            'tahun_ajaran' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', Rule::in(array_keys(Akademik::SEMESTER))],
            'jenjang' => ['nullable', 'string', 'max:100'],
            'kelas' => ['nullable', 'string', 'max:50'],
        ]);

        $jenjang = Jenjang::normalisasi($data['jenjang'] ?? null);
        abort_unless(Akses::cocokJenjang($request->user(), $jenjang), 403);

        $hasil = Rapor::hitungUlang(
            $data['tahun_ajaran'],
            $data['semester'],
            $request->user(),
            $jenjang,
            trim((string) ($data['kelas'] ?? '')) ?: null
        );

        CatatanAktivitas::catat('rapor_hitung_ulang', null, [
            'tahun_ajaran' => $data['tahun_ajaran'],
            'semester' => $data['semester'],
            'jenjang' => $jenjang,
            'kelas' => $data['kelas'] ?? null,
            'dibuat' => $hasil['dibuat'],
            'diperbarui' => $hasil['diperbarui'],
            'dilewati' => $hasil['dilewati'],
        ]);

        return redirect()
            ->route('admin.akademik.rapor.index', [
                'jenjang' => $jenjang,
                'kelas' => $data['kelas'] ?? null,
                'tahun_ajaran' => $data['tahun_ajaran'],
                'semester' => $data['semester'],
            ])
            ->with('success', "Rapor dihitung ulang: {$hasil['dibuat']} dibuat, {$hasil['diperbarui']} diperbarui, {$hasil['dilewati']} dilewati.");
    }

    public function edit(Request $request, RaporSantri $rapor)
    {
        abort_unless($request->user()->bisa('akademik.ubah'), 403);
        $this->pastikanAkses($request->user(), $rapor);

        return view('dashboard.akademik.rapor.edit', [
            'rapor' => $rapor->load('santri'),
        ]);
    }

    public function update(Request $request, RaporSantri $rapor)
    {
        abort_unless($request->user()->bisa('akademik.ubah'), 403);
        $this->pastikanAkses($request->user(), $rapor);

        $data = $request->validate([
            'catatan_wali_kelas' => ['nullable', 'string', 'max:2000'],
        ]);

        $rapor->update([
            'catatan_wali_kelas' => trim((string) ($data['catatan_wali_kelas'] ?? '')) ?: null,
            'diperbarui_oleh' => $request->user()->id,
        ]);

        CatatanAktivitas::catat('rapor_catatan_diubah', $rapor, [
            'santri_id' => $rapor->santri_id,
            'tahun_ajaran' => $rapor->tahun_ajaran,
            'semester' => $rapor->semester,
        ]);

        return redirect()
            ->route('admin.akademik.rapor.index', $this->queryIndex($rapor))
            ->with('success', 'Catatan wali kelas berhasil disimpan.');
    }

    public function terbitkan(Request $request, RaporSantri $rapor)
    {
        abort_unless($request->user()->bisa('akademik.terbit'), 403);
        $this->pastikanAkses($request->user(), $rapor);

        $rapor->update([
            'status' => 'published',
            'published_at' => now(),
            'diterbitkan_oleh' => $request->user()->id,
        ]);

        CatatanAktivitas::catat('rapor_diterbitkan', $rapor, [
            'santri_id' => $rapor->santri_id,
            'rata_rata' => $rapor->rata_rata,
            'peringkat' => $rapor->peringkat,
        ]);

        return redirect()
            ->route('admin.akademik.rapor.index', $this->queryIndex($rapor))
            ->with('success', 'Rapor berhasil diterbitkan dan tampil di portal wali.');
    }

    /** Terbitkan semua rapor draft pada konteks filter yang sedang dibuka. */
    public function terbitkanSemua(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.terbit'), 403);
        abort_unless(Rapor::siap(), 404, 'Tabel rapor belum tersedia.');

        $data = $request->validate([
            'tahun_ajaran' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', Rule::in(array_keys(Akademik::SEMESTER))],
            'jenjang' => ['nullable', 'string', 'max:100'],
            'kelas' => ['nullable', 'string', 'max:50'],
        ]);

        $jenjang = Jenjang::normalisasi($data['jenjang'] ?? null);
        abort_unless(Akses::cocokJenjang($request->user(), $jenjang), 403);

        $idSantri = Akademik::santriQuery($request->user(), $jenjang)
            ->when($data['kelas'] ?? null, fn ($q) => $q->where('kelas', $data['kelas']))
            ->pluck('id')
            ->all();

        $jumlah = RaporSantri::query()
            ->whereIn('santri_id', $idSantri ?: [0])
            ->where('tahun_ajaran', $data['tahun_ajaran'])
            ->where('semester', $data['semester'])
            ->where('status', 'draft')
            ->update([
                'status' => 'published',
                'published_at' => now(),
                'diterbitkan_oleh' => $request->user()->id,
            ]);

        CatatanAktivitas::catat('rapor_terbit_massal', null, [
            'tahun_ajaran' => $data['tahun_ajaran'],
            'semester' => $data['semester'],
            'jenjang' => $jenjang,
            'kelas' => $data['kelas'] ?? null,
            'jumlah' => $jumlah,
        ]);

        return redirect()
            ->route('admin.akademik.rapor.index', [
                'jenjang' => $jenjang,
                'kelas' => $data['kelas'] ?? null,
                'tahun_ajaran' => $data['tahun_ajaran'],
                'semester' => $data['semester'],
            ])
            ->with('success', "{$jumlah} rapor berhasil diterbitkan.");
    }

    /* ============================================================= Helper */

    private function pastikanAkses($user, RaporSantri $rapor): void
    {
        abort_unless(
            Akses::cocokJenjang($user, $rapor->jenjang),
            403,
            'Rapor ini di luar jenjang yang Anda tangani.'
        );
    }

    /** @return array<string, string|null> */
    private function queryIndex(RaporSantri $rapor): array
    {
        return [
            'jenjang' => $rapor->jenjang,
            'kelas' => $rapor->kelas,
            'tahun_ajaran' => $rapor->tahun_ajaran,
            'semester' => $rapor->semester,
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
