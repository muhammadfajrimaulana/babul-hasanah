<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\KredensialMail;
use App\Models\Alumni;
use App\Models\Santri;
use App\Models\User;
use App\Support\Akses;
use App\Support\AlumniAkun;
use App\Support\CatatanAktivitas;
use App\Support\Jenjang;
use App\Support\Kelulusan;
use App\Support\RekapJenjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            ->when(Alumni::siapKolom('user_id'), fn ($q) => $q->with('user'))
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

        // Alumni yang belum memiliki akun login (dibatasi jenjang user).
        $tanpaAkun = Alumni::siapKolom('user_id')
            ? Alumni::query()
                ->when($tautanSiap, fn ($q) => $q->with('santri'))
                ->when($jenjangDb, fn ($q) => $q->whereIn('jenjang', $jenjangDb))
                ->whereNull('user_id')
                ->orderByDesc('tahun_lulus')
                ->orderBy('nama')
                ->get()
            : collect();

        return view('dashboard.alumni.index', [
            'alumnis' => $alumnis,
            'daftarAngkatan' => $daftarAngkatan,
            'daftarJenjang' => $daftarJenjang,
            'daftarKegiatan' => $daftarKegiatan,
            'statistik' => $statistik,
            'santriLulus' => $santriLulus,
            'tanpaAkun' => $tanpaAkun,
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

    /**
     * Buat akun login untuk banyak alumni sekaligus.
     *
     * Hanya alumni yang belum punya akun dan masih berada di jenjang pengguna
     * yang diproses. Kredensial dikirim ke email alumni, atau email santri bila
     * kolomnya sudah tersedia. Kegagalan satu alumni tidak menghentikan yang lain.
     */
    public function buatAkunMassal(Request $request)
    {
        abort_unless($request->user()->bisa('alumni.ubah'), 403);

        if (! Alumni::siapKolom('user_id')) {
            return back()->with(
                'error',
                'Kolom akun alumni belum tersedia. Jalankan php artisan migrate terlebih dahulu.'
            );
        }

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ], [
            'ids.required' => 'Pilih minimal satu alumni.',
            'ids.min' => 'Pilih minimal satu alumni.',
        ]);

        $tautanSiap = Kelulusan::siapTautan();

        $alumnis = Alumni::query()
            ->when($tautanSiap, fn ($q) => $q->with('santri'))
            ->whereIn('id', array_values(array_unique($data['ids'])))
            ->get();

        $berhasil = 0;
        $dilewati = 0;
        $gagal = [];

        foreach ($alumnis as $alumni) {
            if (! Akses::cocokJenjang($request->user(), $alumni->jenjang)) {
                $dilewati++;
                continue;
            }

            if ($alumni->user_id) {
                $dilewati++;
                continue;
            }

            try {
                $this->pakaiEmailSantri($alumni, $tautanSiap);

                $hasil = AlumniAkun::pastikan($alumni->fresh());
                $emailTerkirim = false;

                if ($hasil['password']) {
                    $emailTerkirim = $this->kirimKredensial(
                        $hasil['user'],
                        $hasil['password'],
                        'Akun Portal Alumni & Unduh Ijazah',
                        'Kredensial di email langsung dipakai; ijazah dan dokumen kelulusan dapat diunduh setelah login.'
                    );
                }

                CatatanAktivitas::catat('buat_akun_alumni', $alumni, [
                    'akun_alumni' => $hasil['user']->email,
                    'email_terkirim' => $emailTerkirim,
                ]);

                $berhasil++;
            } catch (\Throwable $e) {
                report($e);
                $gagal[] = $alumni->nama;
            }
        }

        $pesan = $berhasil.' akun alumni berhasil dibuat dan kredensialnya dikirim.';

        if ($dilewati > 0) {
            $pesan .= ' '.$dilewati.' alumni dilewati (sudah punya akun atau di luar jenjang Anda).';
        }

        if ($gagal !== []) {
            $pesan .= ' Gagal: '.implode(', ', array_slice($gagal, 0, 5))
                .(count($gagal) > 5 ? ' dan '.(count($gagal) - 5).' lainnya' : '').'.';
        }

        if ($alumnis->isEmpty()) {
            $pesan = 'Tidak ada alumni yang dipilih.';
        }

        CatatanAktivitas::catat('buat_akun_alumni_massal', null, [
            'diminta' => count(array_unique($data['ids'])),
            'berhasil' => $berhasil,
            'dilewati' => $dilewati,
            'gagal' => count($gagal),
            'nama_gagal' => array_slice($gagal, 0, 5),
        ]);

        return back()->with($berhasil > 0 ? 'success' : 'error', $pesan);
    }

    /**
     * Reset password alumni menjadi password baru yang langsung bisa dipakai.
     *
     * Bila alumni belum punya akun, akun dibuat lebih dulu agar tombol ini
     * tetap berguna untuk data lama yang belum tersinkron.
     */
    public function resetPassword(Alumni $alumni)
    {
        abort_unless(auth()->user()->bisa('alumni.ubah'), 403);
        $this->pastikanJenjang($alumni->jenjang);

        if (! Alumni::siapKolom('user_id')) {
            return back()->with(
                'error',
                'Kolom akun alumni belum tersedia. Jalankan php artisan migrate terlebih dahulu.'
            );
        }

        $tautanSiap = Kelulusan::siapTautan();
        $akun = $alumni->user;
        $akunBaru = false;

        if (! $akun) {
            $this->pakaiEmailSantri($alumni, $tautanSiap);

            $hasil = AlumniAkun::pastikan($alumni->fresh());
            $akun = $hasil['user'];
            $password = $hasil['password'];
            $akunBaru = true;
        } else {
            if (! $akun->isAlumni()) {
                return back()->with('error', 'Akun yang tertaut bukan akun alumni. Periksa menu Pengguna.');
            }

            $password = Str::password(12, symbols: false);

            $akun->forceFill([
                'password' => Hash::make($password),
                'must_change_password' => false,
                'temp_password_expires_at' => null,
                'is_active' => true,
            ])->save();
        }

        $judul = $akunBaru ? 'Akun Portal Alumni & Unduh Ijazah' : 'Reset Password Portal Alumni';
        $catatan = $akunBaru
            ? 'Akun alumni dibuat oleh admin. Ijazah dan dokumen kelulusan dapat diunduh setelah login.'
            : 'Password baru untuk akun alumni '.$alumni->nama.'. Langsung dipakai untuk login.';

        $terkirim = $this->kirimKredensial($akun, $password, $judul, $catatan);

        CatatanAktivitas::catat('reset_password_alumni', $alumni, [
            'akun_alumni' => $akun->email,
            'akun_baru' => $akunBaru,
            'email_terkirim' => $terkirim,
        ]);

        $kata = $akunBaru ? 'Akun alumni dibuat' : 'Password alumni direset';

        if ($terkirim) {
            return back()->with(
                'success',
                $kata.'. Password baru dikirim ke '.$akun->email.' dan langsung bisa dipakai.'
            );
        }

        return back()->with(
            'error',
            $kata.', tetapi email gagal dikirim ke '.$akun->email
                .'. Periksa konfigurasi email atau ubah password lewat menu Pengguna.'
        );
    }

    public function show(Alumni $alumni)
    {
        $this->pastikanJenjang($alumni->jenjang);

        $tautanSiap = Kelulusan::siapTautan();
        $akunSiap = Alumni::siapKolom('user_id');

        if ($tautanSiap) {
            $alumni->loadMissing('santri');
        }

        if ($akunSiap) {
            $alumni->loadMissing('user');
        }

        return view('dashboard.alumni.show', [
            'alumni' => $alumni,
            'nilaiSiap' => Alumni::siapNilai(),
            'tautanSiap' => $tautanSiap,
            'akunSiap' => $akunSiap,
            'akunAlumni' => $akunSiap ? $alumni->user : null,
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

    /**
     * Samakan email alumni dengan email santri bila belum dipakai akun lain.
     *
     * Dipakai saat membuat akun, termasuk oleh aksi reset password bila akun
     * lama belum ada. Email yang sengaja diisi admin tidak ditimpa.
     */
    private function pakaiEmailSantri(Alumni $alumni, bool $tautanSiap): void
    {
        if (! $tautanSiap || ! Santri::siapEmailSantri() || blank($alumni->santri?->email_santri)) {
            return;
        }

        $emailSantri = $alumni->santri->email_santri;

        if (User::where('email', $emailSantri)->exists()) {
            return;
        }

        if (strcasecmp((string) $alumni->email, $emailSantri) !== 0) {
            $alumni->forceFill(['email' => $emailSantri])->save();
        }
    }

    /** Kirim kredensial alumni dan laporkan hasil pengirimannya. */
    private function kirimKredensial(User $user, string $password, string $judul, string $catatan): bool
    {
        try {
            Mail::to($user->email, $user->name)->send(new KredensialMail(
                nama: $user->name,
                email: $user->email,
                passwordSementara: $password,
                peran: 'Alumni',
                judul: $judul,
                catatan: $catatan,
                wajibGanti: false,
            ));

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
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
        foreach (['nisn', 'tahun_masuk', 'pekerjaan'] as $kolom) {
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
            'pekerjaan' => 'nullable|string|max:150',
            'no_hp' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string|max:1000',
            'testimoni' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
    }
}
