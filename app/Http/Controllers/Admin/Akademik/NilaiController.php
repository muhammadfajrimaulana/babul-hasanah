<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\DokumenNilai;
use App\Models\MataPelajaran;
use App\Models\NilaiSantri;
use App\Support\Akademik;
use App\Support\Akses;
use App\Support\Jenjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class NilaiController extends Controller
{
    /* ============================================================ Daftar */

    public function index(Request $request)
    {
        if (! Akademik::siap()) {
            return view('dashboard.akademik.belum-siap');
        }

        $user = $request->user();
        $filter = $this->filter($request);
        $santris = collect();
        $nilai = collect();
        $ringkasan = $this->ringkasanKosong();

        if ($filter['lengkap']) {
            $santris = Akademik::santriQuery($user, $filter['jenjang'])
                ->where('kelas', $filter['kelas'])
                ->orderBy('nama_lengkap')
                ->get();

            $nilai = NilaiSantri::query()
                ->whereIn('santri_id', $santris->pluck('id'))
                ->konteks(
                    $filter['mapel']->id,
                    $filter['tahun'],
                    $filter['semester'],
                    $filter['kategori']
                )
                ->get()
                ->keyBy('santri_id');

            $ringkasan = [
                'total' => $santris->count(),
                'terisi' => $nilai->count(),
                'draft' => $nilai->where('status', 'draft')->count(),
                'terbit' => $nilai->where('status', 'published')->count(),
                'rata' => $nilai->avg('nilai'),
            ];
        }

        // Data panel input cepat & riwayat terbaru. Dahulu berada di halaman
        // dashboard-jenjang/input-nilai; kini digabung agar hanya ada satu
        // halaman pengelolaan nilai.
        $jenjangAktif = $filter['jenjang'];

        $santriList = $jenjangAktif
            ? Akademik::santriQuery($user, $jenjangAktif)->orderBy('nama_lengkap')->get()
            : collect();

        $mapelList = $jenjangAktif
            ? Akademik::mapelQuery($user, $jenjangAktif)->with('guru')->where('aktif', true)->orderBy('nama')->get()
            : collect();

        $nilaiTerbaru = NilaiSantri::query()
            ->with(['santri', 'mataPelajaran.guru'])
            ->when($jenjangAktif, fn ($q) => $q->where('jenjang', $jenjangAktif))
            ->latest()
            ->take(8)
            ->get();

        $dokumenTerbaru = DokumenNilai::siap()
            ? DokumenNilai::query()
                ->with('santri')
                ->when($jenjangAktif, fn ($q) => $q->where('jenjang', $jenjangAktif))
                ->latest()
                ->take(8)
                ->get()
            : collect();

        return view('dashboard.akademik.nilai.index', [
            'filter' => $filter,
            'santriList' => $santriList,
            'mapelList' => $mapelList,
            'nilaiTerbaru' => $nilaiTerbaru,
            'dokumenTerbaru' => $dokumenTerbaru,
            'santris' => $santris,
            'nilai' => $nilai,
            'ringkasan' => $ringkasan,
            'daftarKelas' => Akademik::daftarKelas($user, $filter['jenjang']),
            'daftarMapel' => $this->daftarMapel($user, $filter['jenjang']),
            'daftarJenjang' => $this->daftarJenjang($user),
            'tahunPilihan' => Akademik::tahunAjaranPilihan(),
            'dokumenSiap' => DokumenNilai::siap(),
        ]);
    }

    /* ======================================================= Input manual */

    public function store(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.ubah'), 403);

        $konteks = $this->konteks($request);
        $user = $request->user();

        $data = $request->validate([
            'nilai' => ['array'],
            'nilai.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'catatan' => ['array'],
            'catatan.*' => ['nullable', 'string', 'max:255'],
        ], [
            'nilai.*.numeric' => 'Nilai harus berupa angka.',
            'nilai.*.min' => 'Nilai minimal 0.',
            'nilai.*.max' => 'Nilai maksimal 100.',
        ]);

        $idSantri = Akademik::santriQuery($user, $konteks['jenjang'])
            ->where('kelas', $konteks['kelas'])
            ->pluck('id')
            ->all();

        $jumlah = 0;
        $dikembalikan = 0;

        DB::transaction(function () use ($data, $konteks, $idSantri, $user, &$jumlah, &$dikembalikan) {
            foreach ($idSantri as $santriId) {
                $nilaiBaru = $data['nilai'][$santriId] ?? null;
                $catatanBaru = trim((string) ($data['catatan'][$santriId] ?? '')) ?: null;

                $kunci = [
                    'santri_id' => $santriId,
                    'mata_pelajaran_id' => $konteks['mapel']->id,
                    'tahun_ajaran' => $konteks['tahun'],
                    'semester' => $konteks['semester'],
                    'kategori' => $konteks['kategori'],
                ];

                if ($nilaiBaru === null || $nilaiBaru === '') {
                    // Nilai draft yang dikosongkan dianggap dibatalkan.
                    NilaiSantri::where($kunci)->where('status', 'draft')->delete();

                    continue;
                }

                $row = NilaiSantri::firstOrNew($kunci);
                $baru = ! $row->exists;
                $berubah = $baru
                    || abs(((float) $row->nilai) - ((float) $nilaiBaru)) > 0.001
                    || (string) $row->catatan !== (string) $catatanBaru;

                if ($berubah && ! $baru && $row->status === 'published') {
                    $dikembalikan++;
                }

                $row->fill([
                    'jenjang' => $konteks['jenjang'],
                    'kelas' => $konteks['kelas'],
                    'nilai' => $nilaiBaru,
                    'catatan' => $catatanBaru,
                    'diperbarui_oleh' => $user->id,
                ]);

                if ($baru) {
                    $row->created_by = $user->id;
                }

                if ($berubah) {
                    $row->status = 'draft';
                    $row->published_at = null;
                    $row->diterbitkan_oleh = null;
                }

                $row->save();
                $jumlah++;
            }
        });

        $pesan = $jumlah.' nilai disimpan sebagai draft.';
        if ($dikembalikan > 0) {
            $pesan .= ' '.$dikembalikan.' nilai yang sudah terbit dikembalikan ke draft karena diubah.';
        }

        return redirect()
            ->route('admin.akademik.nilai.index', $this->queryKonteks($konteks))
            ->with('success', $pesan);
    }

    /* ========================================================= Publikasi */

    public function terbitkan(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.terbit'), 403);

        $konteks = $this->konteks($request);
        $idSantri = Akademik::santriQuery($request->user(), $konteks['jenjang'])
            ->where('kelas', $konteks['kelas'])
            ->pluck('id')
            ->all();

        $jumlah = NilaiSantri::query()
            ->whereIn('santri_id', $idSantri)
            ->where('jenjang', $konteks['jenjang'])
            ->where('kelas', $konteks['kelas'])
            ->konteks($konteks['mapel']->id, $konteks['tahun'], $konteks['semester'], $konteks['kategori'])
            ->where('status', 'draft')
            ->update([
                'status' => 'published',
                'published_at' => now(),
                'diterbitkan_oleh' => $request->user()->id,
                'updated_at' => now(),
            ]);

        $pesan = $jumlah > 0
            ? $jumlah.' nilai diterbitkan dan sekarang terlihat di portal wali santri.'
            : 'Tidak ada nilai draft pada konteks ini.';

        return redirect()
            ->route('admin.akademik.nilai.index', $this->queryKonteks($konteks))
            ->with($jumlah > 0 ? 'success' : 'info', $pesan);
    }

    /* ============================================================= Helper */

    private function filter(Request $request): array
    {
        $user = $request->user();
        $mapelId = (int) $request->input('mata_pelajaran_id');
        $mapel = $mapelId ? MataPelajaran::with('guru')->find($mapelId) : null;

        $jenjang = Akses::jenjangSlug($user)
            ?: Jenjang::normalisasi($request->input('jenjang'))
            ?: $mapel?->jenjang;

        $kelas = trim((string) $request->input('kelas')) ?: null;
        $tahun = trim((string) $request->input('tahun_ajaran')) ?: Akademik::tahunAjaranSekarang();
        $semester = strtolower((string) $request->input('semester')) ?: Akademik::semesterSekarang();
        $kategori = strtolower((string) $request->input('kategori')) ?: 'tugas';

        if (! array_key_exists($semester, Akademik::SEMESTER)) {
            $semester = Akademik::semesterSekarang();
        }

        if (! array_key_exists($kategori, Akademik::KATEGORI)) {
            $kategori = 'tugas';
        }

        // Admin tanpa jenjang: jangan tampilkan konteks apa pun.
        if (Akses::adminTanpaJenjang($user)) {
            return [
                'jenjang' => null,
                'kelas' => null,
                'mapel' => null,
                'tahun' => $tahun,
                'semester' => $semester,
                'kategori' => $kategori,
                'mismatch' => false,
                'lengkap' => false,
            ];
        }

        $mismatch = $jenjang && $mapel && $mapel->jenjang !== $jenjang;

        return [
            'jenjang' => $jenjang,
            'kelas' => $kelas,
            'mapel' => $mapel,
            'tahun' => $tahun,
            'semester' => $semester,
            'kategori' => $kategori,
            'mismatch' => $mismatch,
            'lengkap' => $jenjang && $kelas && $mapel && ! $mismatch,
        ];
    }

    /**
     * Validasi konteks nilai dan scoping jenjang.
     *
     * @return array{jenjang:string,kelas:string,tahun:string,semester:string,kategori:string,mapel:MataPelajaran}
     */
    private function konteks(Request $request): array
    {
        $data = $request->validate([
            'jenjang' => ['nullable', 'string', 'max:100'],
            'kelas' => ['required', 'string', 'max:50'],
            'mata_pelajaran_id' => ['required', 'integer'],
            'tahun_ajaran' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', Rule::in(array_keys(Akademik::SEMESTER))],
            'kategori' => ['required', Rule::in(array_keys(Akademik::KATEGORI))],
        ], [
            'kelas.required' => 'Pilih kelas terlebih dahulu.',
            'mata_pelajaran_id.required' => 'Pilih mata pelajaran.',
            'tahun_ajaran.regex' => 'Format tahun ajaran harus seperti 2026/2027.',
        ]);

        $user = $request->user();

        abort_if(
            Akses::adminTanpaJenjang($user),
            403,
            'Akun admin ini belum memiliki jenjang. Hubungi Super Admin.'
        );

        $mapel = MataPelajaran::find($data['mata_pelajaran_id']);

        if (! $mapel) {
            throw ValidationException::withMessages([
                'mata_pelajaran_id' => 'Mata pelajaran tidak ditemukan.',
            ]);
        }

        $jenjang = Akses::jenjangSlug($user)
            ?: Jenjang::normalisasi($data['jenjang'] ?? '')
            ?: $mapel->jenjang;

        abort_unless(Akses::cocokJenjang($user, $jenjang), 403, 'Jenjang ini di luar kewenangan Anda.');

        if ($mapel->jenjang !== $jenjang) {
            throw ValidationException::withMessages([
                'mata_pelajaran_id' => 'Mata pelajaran ini bukan untuk jenjang yang Anda pilih.',
            ]);
        }

        $adaSantri = Akademik::santriQuery($user, $jenjang)
            ->where('kelas', $data['kelas'])
            ->exists();

        if (! $adaSantri) {
            throw ValidationException::withMessages([
                'kelas' => 'Tidak ada santri pada kelas ini di jenjang Anda.',
            ]);
        }

        return [
            'jenjang' => $jenjang,
            'kelas' => $data['kelas'],
            'tahun' => $data['tahun_ajaran'],
            'semester' => $data['semester'],
            'kategori' => $data['kategori'],
            'mapel' => $mapel,
        ];
    }

    private function queryKonteks(array $konteks): array
    {
        return [
            'jenjang' => $konteks['jenjang'],
            'kelas' => $konteks['kelas'],
            'mata_pelajaran_id' => $konteks['mapel']->id,
            'tahun_ajaran' => $konteks['tahun'],
            'semester' => $konteks['semester'],
            'kategori' => $konteks['kategori'],
        ];
    }

    private function daftarMapel($user, ?string $jenjang)
    {
        return Akademik::mapelQuery($user)
            ->with('guru')
            ->where('aktif', true)
            ->when($jenjang, fn ($q) => $q->where('jenjang', $jenjang))
            ->orderBy('nama')
            ->get();
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

    private function ringkasanKosong(): array
    {
        return [
            'total' => 0,
            'terisi' => 0,
            'draft' => 0,
            'terbit' => 0,
            'rata' => null,
        ];
    }
}
