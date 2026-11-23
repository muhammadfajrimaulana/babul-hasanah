<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Hafalan;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\NilaiSantri;
use App\Models\Pelanggaran;
use App\Support\Akademik;
use App\Support\Akses;
use App\Support\CatatanAktivitas;
use App\Support\Jenjang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Master kelas & guru.
 *
 * Guru adalah data nama (tanpa akun login). Wali Kelas adalah jabatan pada
 * satu kelas: tabel kelas hanya punya satu kolom wali_kelas_id.
 */
class KelasController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $jenjangAdmin = Akses::jenjangSlug($user);

        $query = Kelas::query()->with(['waliKelas'])->withCount('santri');

        if (Akses::dibatasiJenjang($user)) {
            $query->where('jenjang', $jenjangAdmin ?: '__tidak_ada__');
        } elseif ($request->filled('jenjang')) {
            $slug = Jenjang::normalisasi($request->input('jenjang'));
            $query->where('jenjang', $slug ?: '__tidak_ada__');
        }

        return view('dashboard.kelas.index', [
            'kelas' => $query->orderBy('jenjang')->orderBy('nama')->get(),
            'gurus' => Guru::query()->orderBy('nama')->get(),
            'daftarJenjang' => Jenjang::semua(),
            'jenjangAdmin' => $jenjangAdmin,
            'filterJenjang' => $request->input('jenjang'),
            'bolehUbah' => $user->bisa('kelas.ubah'),
            'bolehGuru' => $user->bisa('guru.ubah'),
            'tahunAjaran' => now()->month >= 7
                ? now()->year.'/'.(now()->year + 1)
                : (now()->year - 1).'/'.now()->year,
        ]);
    }

    /**
     * Rekap kelas untuk Wali Kelas.
     *
     * Wali Kelas tidak memiliki akun login. Admin Jenjang mencetak rekap ini,
     * memberikannya ke Wali Kelas untuk diperiksa/direkap, lalu Admin Jenjang
     * yang menginput perubahan ke sistem (tercatat di Log Aktivitas).
     */
    public function rekap(Request $request, Kelas $kelas)
    {
        $this->pastikanJenjang($kelas);

        $tahun = trim((string) $request->input('tahun_ajaran')) ?: Akademik::tahunAjaranSekarang();
        $semester = strtolower((string) $request->input('semester')) ?: Akademik::semesterSekarang();

        if (! array_key_exists($semester, Akademik::SEMESTER)) {
            $semester = Akademik::semesterSekarang();
        }

        $santris = $kelas->santri()->orderBy('nama_lengkap')->get();
        $idSantri = $santris->pluck('id');

        $mapel = MataPelajaran::query()
            ->with('guru')
            ->where('jenjang', $kelas->jenjang)
            ->where('aktif', true)
            ->orderBy('nama')
            ->get();

        $nilai = NilaiSantri::query()
            ->whereIn('santri_id', $idSantri)
            ->where('tahun_ajaran', $tahun)
            ->where('semester', $semester)
            ->where('status', 'published')
            ->get()
            ->groupBy(fn (NilaiSantri $n) => $n->santri_id.'|'.$n->mata_pelajaran_id)
            ->map(fn ($grup) => round((float) $grup->avg('nilai'), 2));

        $hafalan = Hafalan::query()
            ->whereIn('santri_id', $idSantri)
            ->selectRaw('santri_id, COUNT(*) as jumlah')
            ->groupBy('santri_id')
            ->pluck('jumlah', 'santri_id');

        $pelanggaran = Pelanggaran::query()
            ->whereIn('santri_id', $idSantri)
            ->selectRaw('santri_id, COUNT(*) as jumlah, COALESCE(SUM(poin), 0) as poin')
            ->groupBy('santri_id')
            ->get()
            ->keyBy('santri_id');

        $presensi = Absensi::query()
            ->whereIn('santri_id', $idSantri)
            ->where('status_kehadiran', 'hadir')
            ->selectRaw('santri_id, COUNT(*) as jumlah')
            ->groupBy('santri_id')
            ->pluck('jumlah', 'santri_id');

        return view('dashboard.kelas.rekap', [
            'kelas' => $kelas,
            'santris' => $santris,
            'mapel' => $mapel,
            'nilai' => $nilai,
            'hafalan' => $hafalan,
            'pelanggaran' => $pelanggaran,
            'presensi' => $presensi,
            'tahun' => $tahun,
            'semester' => $semester,
            'tahunPilihan' => Akademik::tahunAjaranPilihan(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->bisa('kelas.ubah'), 403);

        $data = $this->validasi($request);

        $kelas = Kelas::create([
            'jenjang' => $data['jenjang'],
            'nama' => $data['nama'],
            'tingkat' => $data['tingkat'] ?? null,
            'tahun_ajaran' => $data['tahun_ajaran'],
            'wali_kelas_id' => $data['wali_kelas_id'] ?? null,
            'aktif' => true,
        ]);

        CatatanAktivitas::catat('buat_kelas', $kelas, [
            'jenjang' => $kelas->jenjang,
            'nama' => $kelas->nama,
            'wali_kelas_id' => $kelas->wali_kelas_id,
        ]);

        return back()->with('success', 'Kelas '.$kelas->nama.' berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas)
    {
        abort_unless(auth()->user()->bisa('kelas.ubah'), 403);
        $this->pastikanJenjang($kelas);

        $data = $this->validasi($request);

        $waliLama = $kelas->wali_kelas_id;
        $kelas->update([
            'jenjang' => $data['jenjang'],
            'nama' => $data['nama'],
            'tingkat' => $data['tingkat'] ?? null,
            'tahun_ajaran' => $data['tahun_ajaran'],
            'wali_kelas_id' => $data['wali_kelas_id'] ?? null,
        ]);

        // Perubahan wali kelas wajib tercatat dan langsung tampil di portal wali.
        if ((int) $waliLama !== (int) $kelas->wali_kelas_id) {
            CatatanAktivitas::catat('ganti_wali_kelas', $kelas, [
                'jenjang' => $kelas->jenjang,
                'kelas' => $kelas->nama,
                'wali_kelas_lama' => $waliLama,
                'wali_kelas_baru' => $kelas->wali_kelas_id,
            ]);
        }

        return back()->with('success', 'Kelas '.$kelas->nama.' berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        abort_unless(auth()->user()->bisa('kelas.ubah'), 403);
        $this->pastikanJenjang($kelas);

        if ($kelas->santri()->exists()) {
            return back()->with('error', 'Kelas masih memiliki santri. Pindahkan santri terlebih dahulu.');
        }

        $nama = $kelas->nama;
        $kelas->delete();

        CatatanAktivitas::catat('hapus_kelas', null, ['nama' => $nama]);

        return back()->with('success', 'Kelas '.$nama.' berhasil dihapus.');
    }

    public function storeGuru(Request $request)
    {
        abort_unless(auth()->user()->bisa('guru.ubah'), 403);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nip' => ['nullable', 'string', 'max:50'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'aktif' => ['nullable', 'boolean'],
        ]);

        $guru = Guru::create([
            'nama' => $data['nama'],
            'nip' => $data['nip'] ?? null,
            'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
            'aktif' => $request->boolean('aktif', true),
        ]);

        CatatanAktivitas::catat('tambah_guru', $guru, ['nama' => $guru->nama]);

        return back()->with('success', 'Data guru '.$guru->nama.' berhasil ditambahkan. Guru tidak memiliki akun login.');
    }

    public function updateGuru(Request $request, Guru $guru)
    {
        abort_unless(auth()->user()->bisa('guru.ubah'), 403);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nip' => ['nullable', 'string', 'max:50'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'aktif' => ['nullable', 'boolean'],
        ]);

        $guru->update([
            'nama' => $data['nama'],
            'nip' => $data['nip'] ?? null,
            'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
            'aktif' => $request->boolean('aktif', true),
        ]);

        CatatanAktivitas::catat('ubah_guru', $guru, ['nama' => $guru->nama]);

        return back()->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroyGuru(Guru $guru)
    {
        abort_unless(auth()->user()->bisa('guru.ubah'), 403);

        if ($guru->kelas()->exists()) {
            return back()->with('error', 'Guru ini masih menjadi wali kelas. Ganti wali kelas terlebih dahulu.');
        }

        $nama = $guru->nama;
        $guru->delete();

        CatatanAktivitas::catat('hapus_guru', null, ['nama' => $nama]);

        return back()->with('success', 'Data guru '.$nama.' berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validasi(Request $request): array
    {
        $user = auth()->user();

        $data = $request->validate([
            'jenjang' => ['nullable', 'string', 'max:100'],
            'nama' => ['required', 'string', 'max:50'],
            'tingkat' => ['nullable', 'integer', 'min:1', 'max:12'],
            'tahun_ajaran' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'wali_kelas_id' => ['nullable', 'integer', Rule::exists('guru', 'id')],
        ], [
            'nama.required' => 'Nama kelas wajib diisi (contoh: 8A).',
            'tahun_ajaran.regex' => 'Format tahun ajaran harus 2026/2027.',
            'wali_kelas_id.exists' => 'Wali kelas yang dipilih tidak terdaftar.',
        ]);

        if (Akses::dibatasiJenjang($user)) {
            $slug = Akses::jenjangSlug($user);

            if (! $slug) {
                abort(403, 'Akun Admin Jenjang belum memiliki jenjang.');
            }

            // Admin Jenjang tidak bisa membuat kelas jenjang lain.
            $data['jenjang'] = $slug;
        } else {
            $slug = Jenjang::normalisasi($data['jenjang'] ?? '');

            if (! $slug) {
                abort(422, 'Pilih jenjang untuk kelas ini.');
            }

            $data['jenjang'] = $slug;
        }

        return $data;
    }

    private function pastikanJenjang(Kelas $kelas): void
    {
        if (Akses::dibatasiJenjang(auth()->user())) {
            abort_unless(
                Akses::cocokJenjang(auth()->user(), $kelas->jenjang),
                403,
                'Kelas ini berada di luar jenjang yang Anda tangani.'
            );
        }
    }
}
