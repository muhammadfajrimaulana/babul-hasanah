<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Exports\GradesTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\GradesImport;
use App\Models\Student;
use App\Support\Akademik;
use App\Support\NilaiAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

/**
 * Manajemen Nilai Akademik (skema `grades`).
 *
 * Halaman ini memakai `classes`, `subjects`, `students`, dan `grades`.
 * Bila tabel skema baru belum dimigrasikan, controller mengalihkan ke
 * NilaiController lama agar modul akademik lama tetap dapat dipakai.
 */
class GradeController extends Controller
{
    public function index(Request $request)
    {
        if (! NilaiAkademik::siap()) {
            return $this->fallback($request, 'index');
        }

        $user = $request->user();
        $filter = NilaiAkademik::filter($request, $user);
        $siswa = collect();
        $matriks = collect();
        $ringkasan = $this->ringkasanKosong();

        if ($filter['lengkap']) {
            $siswa = NilaiAkademik::siswaKelas($filter['class_id'])->get();
            $matriks = NilaiAkademik::matriks(
                $siswa,
                $filter['subject_id'],
                $filter['tahun'],
                $filter['semester']
            );
            $ringkasan = NilaiAkademik::ringkasan($siswa, $matriks);
        }

        return view('dashboard.akademik.nilai-akademik.index', [
            'filter' => $filter,
            'siswa' => $siswa,
            'matriks' => $matriks,
            'ringkasan' => $ringkasan,
            'daftarKelas' => NilaiAkademik::daftarKelas($user),
            'daftarMapel' => NilaiAkademik::daftarMapel($user),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.ubah'), 403);

        if (! NilaiAkademik::siap()) {
            return $this->fallback($request, 'store');
        }

        $filter = NilaiAkademik::filter($request, $request->user());

        if (! $filter['lengkap']) {
            return redirect()
                ->route('admin.akademik.nilai.index', $this->queryKonteks($filter))
                ->with('error', 'Lengkapi kelas, mata pelajaran, tahun ajaran, dan semester terlebih dahulu.');
        }

        $data = $request->validate([
            'nilai' => ['array'],
            'nilai.*' => ['array'],
            'nilai.*.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ], [
            'nilai.*.*.numeric' => 'Nilai harus berupa angka.',
            'nilai.*.*.min' => 'Nilai minimal 0.',
            'nilai.*.*.max' => 'Nilai maksimal 100.',
        ]);

        $siswa = NilaiAkademik::siswaKelas($filter['class_id'])->get();
        $hasil = NilaiAkademik::simpan(
            $siswa,
            $filter['class_id'],
            $filter['subject_id'],
            $filter['tahun'],
            $filter['semester'],
            $data['nilai'] ?? []
        );

        $pesan = $hasil['jumlah'].' nilai disimpan sebagai draft.';
        if ($hasil['dikembalikan'] > 0) {
            $pesan .= ' '.$hasil['dikembalikan'].' nilai terbit dikembalikan ke draft karena diubah.';
        }

        return redirect()
            ->route('admin.akademik.nilai.index', $this->queryKonteks($filter))
            ->with('success', $pesan);
    }

    public function upload(Request $request)
    {
        if (! NilaiAkademik::siap()) {
            return $this->fallback($request, 'upload');
        }

        $user = $request->user();

        return view('dashboard.akademik.nilai-akademik.upload', [
            'filter' => NilaiAkademik::filter($request, $user),
            'daftarKelas' => NilaiAkademik::daftarKelas($user),
            'daftarMapel' => NilaiAkademik::daftarMapel($user),
        ]);
    }

    public function template(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.ubah'), 403);

        if (! NilaiAkademik::siap()) {
            return $this->fallback($request, 'template');
        }

        $filter = NilaiAkademik::filter($request, $request->user());

        if (! $filter['lengkap']) {
            return redirect()
                ->route('admin.akademik.nilai.upload', $this->queryKonteks($filter))
                ->with('error', 'Pilih kelas dan mata pelajaran sebelum mengunduh template.');
        }

        $siswa = NilaiAkademik::siswaKelas($filter['class_id'])->get();
        $matriks = NilaiAkademik::matriks(
            $siswa,
            $filter['subject_id'],
            $filter['tahun'],
            $filter['semester']
        );

        $nama = 'template-nilai-'.$filter['kelas']->name.'-'.$filter['mapel']->kode
            .'-'.$filter['tahun'].'-'.$filter['semester'].'.xlsx';

        return Excel::download(
            new GradesTemplateExport(NilaiAkademik::barisTemplate($siswa, $matriks)),
            Str::slug(pathinfo($nama, PATHINFO_FILENAME)).'.xlsx'
        );
    }

    /**
     * Kompatibilitas route lama: form unggah lama memakai pratinjau.
     * Pada skema baru, unggahan langsung divalidasi dan disimpan.
     */
    public function preview(Request $request)
    {
        if (! NilaiAkademik::siap()) {
            return $this->fallback($request, 'preview');
        }

        return redirect()
            ->route('admin.akademik.nilai.upload', $this->queryKonteks(NilaiAkademik::filter($request, $request->user())))
            ->with('info', 'Unggah file Excel pada formulir di bawah; sistem akan memvalidasi lalu menyimpan nilai.');
    }

    public function import(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.ubah'), 403);

        if (! NilaiAkademik::siap()) {
            return $this->fallback($request, 'import');
        }

        $request->validate([
            'file_nilai' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:10240'],
        ], [
            'file_nilai.required' => 'Pilih file Excel yang akan diunggah.',
            'file_nilai.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
            'file_nilai.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $filter = NilaiAkademik::filter($request, $request->user());

        if (! $filter['lengkap']) {
            return redirect()
                ->route('admin.akademik.nilai.upload', $this->queryKonteks($filter))
                ->with('error', 'Lengkapi kelas dan mata pelajaran sebelum mengunggah nilai.');
        }

        try {
            $sheets = Excel::toArray(new GradesImport, $request->file('file_nilai'));
        } catch (Throwable $e) {
            return redirect()
                ->route('admin.akademik.nilai.upload', $this->queryKonteks($filter))
                ->with('error', 'File tidak dapat dibaca: '.$e->getMessage());
        }

        $baris = $sheets[0] ?? [];

        if ($baris === []) {
            return redirect()
                ->route('admin.akademik.nilai.upload', $this->queryKonteks($filter))
                ->with('error', 'File kosong atau tidak memiliki lembar kerja.');
        }

        $header = array_map(
            fn ($judul) => Str::slug(trim((string) $judul), '_'),
            array_shift($baris)
        );
        $kolom = array_flip($header);

        if (! isset($kolom['nisn']) && ! isset($kolom['nis']) && ! isset($kolom['nomor_induk'])) {
            return redirect()
                ->route('admin.akademik.nilai.upload', $this->queryKonteks($filter))
                ->with('error', 'Kolom NISN tidak ditemukan. Gunakan template yang disediakan.');
        }

        $siswa = NilaiAkademik::siswaKelas($filter['class_id'])
            ->get()
            ->keyBy(fn (Student $student) => strtoupper(trim((string) $student->nisn)));

        $input = [];
        $errors = [];
        $terlihat = [];
        $diproses = 0;
        $nomorBaris = 1;

        foreach ($baris as $row) {
            $nomorBaris++;

            if (collect($row)->filter(fn ($sel) => filled($sel))->isEmpty()) {
                continue;
            }

            $nisn = strtoupper($this->teks($this->sel($row, $kolom, ['nisn', 'nis', 'nomor_induk'])));

            if ($nisn === '') {
                $errors[] = 'Baris '.$nomorBaris.': NISN kosong.';

                continue;
            }

            if (isset($terlihat[$nisn])) {
                $errors[] = 'Baris '.$nomorBaris.': NISN '.$nisn.' muncul lebih dari sekali.';

                continue;
            }
            $terlihat[$nisn] = true;

            /** @var Student|null $student */
            $student = $siswa->get($nisn);

            if (! $student) {
                $errors[] = 'Baris '.$nomorBaris.': NISN '.$nisn.' tidak ditemukan di kelas '
                    .$filter['kelas']->name.'.';

                continue;
            }

            $nilaiBaris = [];
            $valid = true;

            foreach (array_keys(Akademik::KATEGORI) as $kategori) {
                $mentah = trim($this->teks($this->sel($row, $kolom, [$kategori])));

                if ($mentah === '') {
                    $nilaiBaris[$kategori] = null;

                    continue;
                }

                $angka = str_replace(',', '.', $mentah);

                if (! is_numeric($angka)) {
                    $errors[] = 'Baris '.$nomorBaris.': nilai '.$kategori.' "'.$mentah.'" bukan angka.';
                    $valid = false;

                    continue;
                }

                $angka = round((float) $angka, 2);

                if ($angka < 0 || $angka > 100) {
                    $errors[] = 'Baris '.$nomorBaris.': nilai '.$kategori.' harus antara 0 sampai 100.';
                    $valid = false;

                    continue;
                }

                $nilaiBaris[$kategori] = $angka;
            }

            if (! $valid) {
                continue;
            }

            if (collect($nilaiBaris)->filter(fn ($n) => $n !== null)->isEmpty()) {
                continue;
            }

            $input[$student->id] = $nilaiBaris;
            $diproses++;
        }

        if ($input === []) {
            return redirect()
                ->route('admin.akademik.nilai.upload', $this->queryKonteks($filter))
                ->with('error', 'Tidak ada nilai yang dapat disimpan.')
                ->with('import_errors', $errors);
        }

        $hasil = NilaiAkademik::simpan(
            $siswa->values(),
            $filter['class_id'],
            $filter['subject_id'],
            $filter['tahun'],
            $filter['semester'],
            $input
        );

        $pesan = $diproses.' baris nilai berhasil diproses dan disimpan sebagai draft.';
        if ($hasil['dikembalikan'] > 0) {
            $pesan .= ' '.$hasil['dikembalikan'].' nilai terbit dikembalikan ke draft karena diubah.';
        }

        return redirect()
            ->route('admin.akademik.nilai.index', $this->queryKonteks($filter))
            ->with('success', $pesan)
            ->with('import_errors', $errors);
    }

    public function terbitkan(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.terbit'), 403);

        if (! NilaiAkademik::siap()) {
            return $this->fallback($request, 'terbitkan');
        }

        $filter = NilaiAkademik::filter($request, $request->user());

        if (! $filter['lengkap']) {
            return redirect()
                ->route('admin.akademik.nilai.index', $this->queryKonteks($filter))
                ->with('error', 'Lengkapi konteks nilai sebelum menerbitkan.');
        }

        $siswa = NilaiAkademik::siswaKelas($filter['class_id'])->get();
        $jumlah = NilaiAkademik::terbitkan(
            $siswa,
            $filter['class_id'],
            $filter['subject_id'],
            $filter['tahun'],
            $filter['semester']
        );

        if ($jumlah === 0) {
            return redirect()
                ->route('admin.akademik.nilai.index', $this->queryKonteks($filter))
                ->with('info', 'Tidak ada nilai draft yang perlu diterbitkan.');
        }

        return redirect()
            ->route('admin.akademik.nilai.index', $this->queryKonteks($filter))
            ->with('success', $jumlah.' nilai diterbitkan dan kini tampil di Portal Wali Santri.');
    }

    /* ============================================================= Helper */

    private function fallback(Request $request, string $method)
    {
        if (Akademik::siap()) {
            return app(NilaiController::class)->{$method}($request);
        }

        return view('dashboard.akademik.belum-siap');
    }

    /** @return array<string, int|string> */
    private function queryKonteks(array $filter): array
    {
        return array_filter([
            'class_id' => $filter['class_id'],
            'subject_id' => $filter['subject_id'],
            'tahun_ajaran' => $filter['tahun'],
            'semester' => $filter['semester'],
        ], fn ($nilai) => $nilai !== null && $nilai !== '');
    }

    /** @return array{total:int,terisi:int,draft:int,terbit:int,rata:?float} */
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

    /**
     * @param  array<int, mixed>  $row
     * @param  array<string, int>  $kolom
     * @param  array<int, string>  $kandidat
     */
    private function sel(array $row, array $kolom, array $kandidat): mixed
    {
        foreach ($kandidat as $nama) {
            if (isset($kolom[$nama])) {
                return $row[$kolom[$nama]] ?? null;
            }
        }

        return null;
    }

    private function teks(mixed $nilai): string
    {
        if (is_float($nilai) && floor($nilai) === $nilai) {
            return number_format($nilai, 0, '', '');
        }

        return trim((string) $nilai);
    }
}
