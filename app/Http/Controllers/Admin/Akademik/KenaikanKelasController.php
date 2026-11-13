<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Support\Jenjang;
use App\Support\KenaikanKelas;
use App\Support\NilaiAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Modul Kenaikan Kelas & Kelulusan Santri (skema `students`).
 *
 * Kenaikan kelas hanya memindahkan penempatan aktif. Baris `grades` tidak
 * pernah diubah atau dihapus sehingga nilai lama tetap utuh pada tahun ajaran
 * asalnya (`grades.academic_year_id`).
 *
 * Akses mengikuti matriks Akademik:
 * - lihat : Super Admin, Admin Jenjang (jenjangnya), Pengasuh (monitoring)
 * - ubah  : Super Admin & Admin Jenjang
 */
class KenaikanKelasController extends Controller
{
    public function index(Request $request)
    {
        if (! NilaiAkademik::siap()) {
            return view('dashboard.akademik.belum-siap');
        }

        $user = $request->user();

        abort_unless($user->bisa('akademik.lihat'), 403);

        $daftarKelas = NilaiAkademik::daftarKelas($user);
        $daftarTahun = $this->daftarTahun();

        $kelas = null;
        $classId = (int) $request->input('class_id');

        if ($classId > 0) {
            $kelas = $daftarKelas->firstWhere('id', $classId);

            abort_unless($kelas, 403, 'Kelas ini berada di luar jenjang yang Anda tangani.');
        }

        $tahun = $this->tahun($request);
        $siswa = $kelas ? KenaikanKelas::siswaKelas($kelas->id) : collect();

        $nilaiPerSiswa = $siswa->isEmpty()
            ? collect()
            : Grade::query()
                ->selectRaw('student_id, COUNT(*) as jumlah')
                ->whereIn('student_id', $siswa->pluck('id'))
                ->groupBy('student_id')
                ->pluck('jumlah', 'student_id');

        $kelasTujuan = $kelas
            ? $daftarKelas
                ->filter(fn (SchoolClass $pilihan) => $pilihan->id !== $kelas->id
                    && Jenjang::normalisasi($pilihan->jenjang) === Jenjang::normalisasi($kelas->jenjang))
                ->values()
            : collect();

        return view('dashboard.akademik.kenaikan-kelas.index', [
            'daftarKelas' => $daftarKelas,
            'daftarTahun' => $daftarTahun,
            'kelas' => $kelas,
            'kelasTujuan' => $kelasTujuan,
            'tahun' => $tahun,
            'siswa' => $siswa,
            'nilaiPerSiswa' => $nilaiPerSiswa,
            'filter' => [
                'class_id' => $kelas?->id,
                'academic_year_id' => $tahun?->id,
                'tahun' => $tahun?->nama,
            ],
            'ringkasan' => [
                'siswa' => $siswa->count(),
                'nilai' => (int) $nilaiPerSiswa->sum(),
                'kelas_tujuan' => $kelasTujuan->count(),
                'tahun_ajaran' => $daftarTahun->count(),
            ],
        ]);
    }

    /** Proses kenaikan kelas atau kelulusan santri terpilih. */
    public function proses(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.ubah'), 403);

        if (! NilaiAkademik::siap()) {
            return redirect()
                ->route('admin.akademik.nilai.index')
                ->with('error', 'Tabel akademik belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        if (! Schema::hasTable('academic_years')) {
            return redirect()
                ->route('admin.akademik.nilai.index')
                ->with('error', 'Tabel tahun ajaran belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $data = $request->validate([
            'aksi' => ['required', 'in:naik,lulus'],
            'class_id' => ['required', 'integer'],
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'class_tujuan_id' => ['nullable', 'integer', 'required_if:aksi,naik', 'different:class_id'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'distinct'],
        ], [
            'aksi.required' => 'Pilih aksi yang akan dijalankan.',
            'aksi.in' => 'Aksi tidak dikenal.',
            'class_id.required' => 'Pilih kelas asal terlebih dahulu.',
            'academic_year_id.required' => 'Pilih tahun ajaran terlebih dahulu.',
            'academic_year_id.exists' => 'Tahun ajaran tidak ditemukan.',
            'class_tujuan_id.required_if' => 'Pilih kelas tujuan untuk kenaikan kelas.',
            'class_tujuan_id.different' => 'Kelas tujuan tidak boleh sama dengan kelas asal.',
            'student_ids.required' => 'Pilih minimal satu santri.',
            'student_ids.min' => 'Pilih minimal satu santri.',
        ]);

        $user = $request->user();
        $asal = NilaiAkademik::kelasQuery($user)->whereKey((int) $data['class_id'])->first();

        abort_unless($asal, 403, 'Kelas asal berada di luar jenjang yang Anda tangani.');

        $tahun = AcademicYear::find($data['academic_year_id']);

        if (! $tahun) {
            return redirect()
                ->route('admin.akademik.kenaikan-kelas.index', ['class_id' => $asal->id])
                ->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $ids = KenaikanKelas::siswaKelas($asal->id)
            ->whereIn('id', array_map('intval', $data['student_ids']))
            ->pluck('id')
            ->all();

        if ($ids === []) {
            return redirect()
                ->route('admin.akademik.kenaikan-kelas.index', [
                    'class_id' => $asal->id,
                    'academic_year_id' => $tahun->id,
                ])
                ->with('error', 'Tidak ada santri terpilih yang masih berada di kelas asal.');
        }

        if ($data['aksi'] === 'lulus') {
            return $this->luluskan($ids, $tahun, $asal);
        }

        $tujuan = NilaiAkademik::kelasQuery($user)->whereKey((int) $data['class_tujuan_id'])->first();

        abort_unless($tujuan, 403, 'Kelas tujuan berada di luar jenjang yang Anda tangani.');

        return $this->naikkan($ids, $asal, $tujuan, $tahun);
    }

    /** Tambah tahun ajaran baru (dipakai saat pergantian tahun). */
    public function simpanTahun(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.ubah'), 403);

        if (! Schema::hasTable('academic_years')) {
            return redirect()
                ->route('admin.akademik.kenaikan-kelas.index')
                ->with('error', 'Tabel tahun ajaran belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $data = $request->validate([
            'nama' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'aktif' => ['nullable', 'boolean'],
        ], [
            'nama.required' => 'Nama tahun ajaran wajib diisi.',
            'nama.regex' => 'Format tahun ajaran harus seperti 2026/2027.',
        ]);

        $tahun = AcademicYear::untuk($data['nama']);

        if ($request->boolean('aktif')) {
            AcademicYear::query()->whereKeyNot($tahun->id)->update(['aktif' => false]);
            $tahun->forceFill(['aktif' => true])->save();
        }

        return redirect()
            ->route('admin.akademik.kenaikan-kelas.index', ['academic_year_id' => $tahun->id])
            ->with('success', 'Tahun ajaran '.$tahun->nama.' siap digunakan.');
    }

    /* ============================================================= Helper */

    /**
     * @param  array<int, int>  $ids
     */
    private function naikkan(array $ids, SchoolClass $asal, SchoolClass $tujuan, AcademicYear $tahun)
    {
        try {
            $hasil = KenaikanKelas::naikkan($ids, $asal, $tujuan, $tahun);
        } catch (RuntimeException $e) {
            return redirect()
                ->route('admin.akademik.kenaikan-kelas.index', [
                    'class_id' => $asal->id,
                    'academic_year_id' => $tahun->id,
                ])
                ->with('error', $e->getMessage());
        }

        $pesan = $hasil['berhasil'].' santri dinaikkan dari '.$asal->name.' ke '.$tujuan->name
            .' pada tahun ajaran '.$tahun->nama.'. Nilai lama tetap tersimpan pada tahun ajarannya.';

        return redirect()
            ->route('admin.akademik.kenaikan-kelas.index', [
                'class_id' => $tujuan->id,
                'academic_year_id' => $tahun->id,
            ])
            ->with('success', $pesan)
            ->with('hasil_dilewati', $hasil['dilewati']);
    }

    /**
     * @param  array<int, int>  $ids
     */
    private function luluskan(array $ids, AcademicYear $tahun, SchoolClass $asal)
    {
        try {
            $hasil = KenaikanKelas::luluskan($ids, $tahun);
        } catch (RuntimeException $e) {
            return redirect()
                ->route('admin.akademik.kenaikan-kelas.index', [
                    'class_id' => $asal->id,
                    'academic_year_id' => $tahun->id,
                ])
                ->with('error', $e->getMessage());
        }

        $pesan = $hasil['berhasil'].' santri diluluskan pada tahun ajaran '.$tahun->nama.'.';

        if ($hasil['role_diubah'] > 0) {
            $pesan .= ' '.$hasil['role_diubah'].' akun wali dialihkan menjadi alumni.';
        }

        if ($hasil['role_ditahan'] !== []) {
            $pesan .= ' '.count($hasil['role_ditahan']).' akun tidak dialihkan (lihat rincian).';
        }

        return redirect()
            ->route('admin.akademik.kenaikan-kelas.index', [
                'class_id' => $asal->id,
                'academic_year_id' => $tahun->id,
            ])
            ->with('success', $pesan)
            ->with('hasil_ditahan', $hasil['role_ditahan'])
            ->with('hasil_dilewati', $hasil['dilewati']);
    }

    /** Tahun ajaran pada filter; bawaannya tahun ajaran aktif. */
    private function tahun(Request $request): ?AcademicYear
    {
        if (! Schema::hasTable('academic_years')) {
            return null;
        }

        $id = (int) $request->input('academic_year_id');

        if ($id > 0) {
            $tahun = AcademicYear::find($id);

            if ($tahun) {
                return $tahun;
            }
        }

        return AcademicYear::berjalan();
    }

    /**
     * Daftar tahun ajaran. Bila tabel belum ada, kembalikan koleksi kosong
     * agar halaman tetap tampil tanpa error.
     *
     * @return Collection<int, AcademicYear>
     */
    private function daftarTahun()
    {
        if (! Schema::hasTable('academic_years')) {
            return collect();
        }

        return AcademicYear::query()->orderByDesc('nama')->get();
    }
}
