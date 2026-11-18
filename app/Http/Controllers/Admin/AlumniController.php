<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Support\Akses;
use App\Support\Jenjang;
use App\Support\Kelulusan;
use App\Support\RekapJenjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tautanSiap = Kelulusan::siapTautan();
        $nilaiSiap = Alumni::siapNilai();
        $daftarJenjang = $this->jenjangUntukUser();

        $filterJenjang = trim((string) $request->input('jenjang'));
        $filterKegiatan = trim((string) $request->input('kegiatan'));

        // Filter jenjang bisa berupa slug resmi (?jenjang=mdta dari dashboard)
        // atau nilai asli di database (dari dropdown). Keduanya dinormalisasi.
        $jenjangFilterDb = null;

        if ($filterJenjang !== '') {
            $slugFilter = Jenjang::normalisasi($filterJenjang);

            if ($slugFilter) {
                $filterJenjang = $slugFilter;
                $jenjangFilterDb = RekapJenjang::nilaiDb(Alumni::class, 'jenjang', $slugFilter) ?: ['__tidak_ada__'];
            } elseif (in_array($filterJenjang, $daftarJenjang, true)) {
                $jenjangFilterDb = [$filterJenjang];
            } else {
                $filterJenjang = '';
            }
        }

        if ($filterKegiatan !== '' && ! in_array($filterKegiatan, Alumni::KEGIATAN, true)) {
            $filterKegiatan = '';
        }

        // Admin Jenjang hanya melihat alumni pada jenjangnya.
        $jenjangDb = Akses::dibatasiJenjang($user)
            ? (Akses::nilaiJenjangDb($user, Alumni::class, 'jenjang') ?: ['__tidak_ada__'])
            : null;

        // Statistik mengikuti ringkasan pada halaman publik /alumni.
        $statistik = [
            'total' => Alumni::query()->when($jenjangDb, fn ($q) => $q->whereIn('jenjang', $jenjangDb))->count(),
            'angkatan' => Alumni::query()
                ->when($jenjangDb, fn ($q) => $q->whereIn('jenjang', $jenjangDb))
                ->whereNotNull('tahun_lulus')
                ->distinct()
                ->count('tahun_lulus'),
            'kuliah' => Alumni::query()
                ->when($jenjangDb, fn ($q) => $q->whereIn('jenjang', $jenjangDb))
                ->where('kegiatan', 'Kuliah')
                ->count(),
            'bekerja' => Alumni::query()
                ->when($jenjangDb, fn ($q) => $q->whereIn('jenjang', $jenjangDb))
                ->whereIn('kegiatan', ['Bekerja', 'Wirausaha'])
                ->count(),
        ];

        // Daftar kegiatan yang benar-benar dipakai, seperti chip pada /alumni.
        $daftarKegiatan = Alumni::query()
            ->when($jenjangDb, fn ($q) => $q->whereIn('jenjang', $jenjangDb))
            ->whereNotNull('kegiatan')
            ->distinct()
            ->orderBy('kegiatan')
            ->pluck('kegiatan')
            ->all();

        $query = Alumni::query()
            ->when($tautanSiap, fn ($q) => $q->with('santri'))
            ->when($jenjangFilterDb, fn ($q) => $q->whereIn('jenjang', $jenjangFilterDb))
            ->when($filterKegiatan !== '', fn ($q) => $q->where('kegiatan', $filterKegiatan))
            ->when($request->angkatan, fn ($q, $a) => $q->where('tahun_lulus', $a));

        if ($request->filled('q')) {
            $cari = $request->q;

            $query->where(function ($w) use ($cari, $tautanSiap) {
                $w->where('nama', 'like', "%{$cari}%")
                    ->orWhere('instansi', 'like', "%{$cari}%")
                    ->orWhere('jenjang', 'like', "%{$cari}%")
                    ->orWhere('testimoni', 'like', "%{$cari}%")
                    ->orWhere('email', 'like', "%{$cari}%")
                    ->orWhere('no_hp', 'like', "%{$cari}%");

                if (Alumni::siapKolom('nisn')) {
                    $w->orWhere('nisn', 'like', "%{$cari}%");
                }

                if ($tautanSiap) {
                    $w->orWhereHas('santri', fn ($santri) => $santri
                        ->where('nomor_induk', 'like', "%{$cari}%"));
                }
            });
        }

        $query->when($jenjangDb, fn ($q) => $q->whereIn('jenjang', $jenjangDb));

        $alumnis = $query->orderByDesc('tahun_lulus')->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $daftarAngkatan = Alumni::query()
            ->select('tahun_lulus')
            ->distinct()
            ->when($jenjangDb, fn ($q) => $q->whereIn('jenjang', $jenjangDb))
            ->orderByDesc('tahun_lulus')
            ->pluck('tahun_lulus');

        // Santri yang sudah lulus tetapi belum punya baris Data Alumni.
        $santriLulus = Kelulusan::santriLulusBelumAlumni($user);

        return view('dashboard.alumni.index', [
            'alumnis' => $alumnis,
            'daftarAngkatan' => $daftarAngkatan,
            'daftarJenjang' => $daftarJenjang,
            'daftarKegiatan' => $daftarKegiatan,
            'statistik' => $statistik,
            'santriLulus' => $santriLulus,
            'tautanSiap' => $tautanSiap,
            'nilaiSiap' => $nilaiSiap,
            'totalLulus' => Kelulusan::jumlahLulus($user),
        ]);
    }

    /**
     * Tarik semua santri berstatus lulus ke Data Alumni.
     *
     * Hanya santri pada jenjang yang ditangani yang diproses. Data alumni
     * yang sudah tertaut tidak disentuh sehingga aman dijalankan berulang.
     */
    public function tarikSantriLulus(Request $request)
    {
        abort_unless($request->user()->bisa('alumni.ubah'), 403);

        if (! Kelulusan::siapTautan()) {
            return back()->with(
                'error',
                'Kolom penghubung alumni–santri belum tersedia. Jalankan php artisan migrate terlebih dahulu.'
            );
        }

        $santris = Kelulusan::santriLulusBelumAlumni($request->user());

        if ($santris->isEmpty()) {
            return back()->with(
                'success',
                'Semua santri yang sudah lulus telah terhubung ke Data Alumni.'
            );
        }

        $jumlah = 0;

        foreach ($santris as $santri) {
            Kelulusan::sinkronAlumni($santri, $santri->tahun_lulus);
            $jumlah++;
        }

        return back()->with(
            'success',
            $jumlah.' santri lulus berhasil ditarik ke Data Alumni. Lengkapi kegiatan dan testimoni mereka pada tabel di bawah.'
        );
    }

    public function show(Alumni $alumni)
    {
        $this->pastikanJenjang($alumni->jenjang);

        $tautanSiap = Kelulusan::siapTautan();

        if ($tautanSiap) {
            $alumni->loadMissing('santri');
        }

        return view('dashboard.alumni.show', [
            'alumni' => $alumni,
            'nilaiSiap' => Alumni::siapNilai(),
            'tautanSiap' => $tautanSiap,
        ]);
    }

    public function create()
    {
        return view('dashboard.alumni.create', [
            'jenjangList' => $this->jenjangUntukUser(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->bersihkanKolom($this->validasi($request));

        // Admin Jenjang tidak boleh menambah alumni di luar jenjangnya.
        $this->pastikanJenjang($data['jenjang'] ?? null);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('alumni', 'public');
        }

        $alumni = Alumni::create($data);

        return redirect()->route('admin.alumni.index')
            ->with('success', 'Data alumni '.$alumni->nama.' berhasil ditambahkan.');
    }

    public function edit(Alumni $alumni)
    {
        $this->pastikanJenjang($alumni->jenjang);

        return view('dashboard.alumni.edit', [
            'alumni' => $alumni,
            'jenjangList' => $this->jenjangUntukUser(),
        ]);
    }

    public function update(Request $request, Alumni $alumni)
    {
        $this->pastikanJenjang($alumni->jenjang);

        $data = $this->bersihkanKolom($this->validasi($request));

        // Jenjang tujuan baru juga harus tetap di dalam wewenang user.
        $this->pastikanJenjang($data['jenjang'] ?? null);

        if ($request->hasFile('foto')) {
            if ($alumni->foto) {
                Storage::disk('public')->delete($alumni->foto);
            }
            $data['foto'] = $request->file('foto')->store('alumni', 'public');
        }

        $alumni->update($data);

        return redirect()->route('admin.alumni.index')
            ->with('success', 'Data alumni '.$alumni->nama.' berhasil diperbarui.');
    }

    public function destroy(Alumni $alumni)
    {
        $this->pastikanJenjang($alumni->jenjang);

        if ($alumni->foto) {
            Storage::disk('public')->delete($alumni->foto);
        }

        if (Alumni::siapNilai() && $alumni->nilai_path) {
            Storage::disk('public')->delete($alumni->nilai_path);
        }

        $alumni->delete();

        return redirect()->route('admin.alumni.index')->with('success', 'Data alumni berhasil dihapus.');
    }

    /**
     * Unggah berkas nilai/transkrip alumni (PDF).
     *
     * Satu alumni menyimpan satu berkas; unggah ulang akan menimpa berkas lama.
     */
    public function uploadNilai(Request $request, Alumni $alumni)
    {
        abort_unless($request->user()->bisa('alumni.ubah'), 403);

        $this->pastikanJenjang($alumni->jenjang);

        if (! Alumni::siapNilai()) {
            return back()->with(
                'error',
                'Kolom nilai alumni belum tersedia. Jalankan php artisan migrate terlebih dahulu.'
            );
        }

        $request->validate([
            'file_nilai' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'file_nilai.required' => 'Pilih berkas PDF nilai/transkrip alumni.',
            'file_nilai.mimes' => 'Berkas nilai harus berformat PDF.',
            'file_nilai.max' => 'Ukuran berkas nilai maksimal 10 MB.',
        ]);

        $file = $request->file('file_nilai');

        if ($alumni->nilai_path) {
            Storage::disk('public')->delete($alumni->nilai_path);
        }

        $alumni->forceFill([
            'nilai_path' => $file->store('alumni/nilai', 'public'),
            'nilai_nama' => $file->getClientOriginalName(),
            'nilai_diunggah_pada' => now(),
        ])->save();

        return back()->with(
            'success',
            'Nilai/transkrip '.$alumni->nama.' berhasil diunggah dan dapat diunduh dari halaman ini.'
        );
    }

    /** Unduh berkas nilai/transkrip alumni. */
    public function unduhNilai(Alumni $alumni)
    {
        $this->pastikanJenjang($alumni->jenjang);

        abort_unless(
            filled($alumni->nilai_path) && Storage::disk('public')->exists($alumni->nilai_path),
            404,
            'Berkas nilai alumni tidak ditemukan.'
        );

        return Storage::disk('public')->download(
            $alumni->nilai_path,
            $alumni->nilai_nama ?: 'nilai-'.$alumni->nama.'.pdf'
        );
    }

    /** Hapus berkas nilai/transkrip alumni beserta file fisiknya. */
    public function hapusNilai(Request $request, Alumni $alumni)
    {
        abort_unless($request->user()->bisa('alumni.ubah'), 403);

        $this->pastikanJenjang($alumni->jenjang);

        if (! Alumni::siapNilai()) {
            return back()->with(
                'error',
                'Kolom nilai alumni belum tersedia. Jalankan php artisan migrate terlebih dahulu.'
            );
        }

        if ($alumni->nilai_path) {
            Storage::disk('public')->delete($alumni->nilai_path);
        }

        $alumni->forceFill([
            'nilai_path' => null,
            'nilai_nama' => null,
            'nilai_diunggah_pada' => null,
        ])->save();

        return back()->with('success', 'Berkas nilai '.$alumni->nama.' berhasil dihapus.');
    }

    /** Tolak akses bila data alumni berada di luar jenjang user. */
    private function pastikanJenjang(?string $jenjang): void
    {
        abort_unless(
            Akses::cocokJenjang(auth()->user(), $jenjang),
            403,
            'Data alumni ini berada di luar jenjang yang Anda tangani.'
        );
    }

    /**
     * Daftar jenjang yang boleh dipilih user.
     *
     * Super Admin melihat semua jenjang; Admin Jenjang hanya jenjangnya.
     *
     * @return array<int, string>
     */
    private function jenjangUntukUser(): array
    {
        $user = auth()->user();

        try {
            $semua = Alumni::query()
                ->whereNotNull('jenjang')
                ->distinct()
                ->orderBy('jenjang')
                ->pluck('jenjang')
                ->all();
        } catch (\Throwable $e) {
            $semua = [];
        }

        if ($semua === []) {
            $semua = Alumni::JENJANG;
        }

        if (! Akses::dibatasiJenjang($user)) {
            return array_values($semua);
        }

        $slug = Akses::jenjangSlug($user);

        return array_values(array_filter(
            $semua,
            fn ($jenjang) => Jenjang::normalisasi($jenjang) === $slug
        ));
    }

    /** Buang kolom opsional yang belum ada sebelum migrasi dijalankan. */
    private function bersihkanKolom(array $data): array
    {
        foreach (['nisn', 'tahun_masuk'] as $kolom) {
            if (! Alumni::siapKolom($kolom)) {
                unset($data[$kolom]);
            }
        }

        return $data;
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'nullable|string|max:30',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tahun_masuk' => 'nullable|integer|min:1998|max:'.(now()->year + 1),
            'tahun_lulus' => 'required|integer|min:1998|max:'.(now()->year + 1),
            'jenjang' => 'nullable|string|max:50',
            'kegiatan' => 'nullable|in:'.implode(',', Alumni::KEGIATAN),
            'instansi' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string|max:1000',
            'testimoni' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
    }
}
