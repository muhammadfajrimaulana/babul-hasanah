<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PrestasiSantriMail;
use App\Models\Absensi;
use App\Models\Alumni;
use App\Models\Berita;
use App\Models\DokumenNilai;
use App\Models\Hafalan;
use App\Models\Izin;
use App\Models\Kepengurusan;
use App\Models\MataPelajaran;
use App\Models\NilaiSantri;
use App\Models\Pelanggaran;
use App\Models\PendaftaranOnline;
use App\Models\Pengumuman;
use App\Models\PrestasiSantri;
use App\Models\Santri;
use App\Support\Akademik;
use App\Support\Akses;
use App\Support\Jenjang;
use App\Support\RaporNilai;
use App\Support\RekapJenjang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Dashboard khusus per role.
 *
 * - admin   -> dashboard-jenjang (data dibatasi ke jenjang yang ditangani)
 * - pengasuh-> dashboard-pengasuh (monitoring seluruh pesantren, hanya baca)
 * - pembina -> dashboard-pembina (fokus asrama & kehadiran, hanya baca)
 *
 * Super Admin tetap memakai DashboardController@index (dashboard lengkap).
 */
class RoleDashboardController extends Controller
{
    /**
     * Dashboard jenjang (sebelumnya hanya Admin Jenjang).
     *
     * - Admin Jenjang : selalu dibatasi ke jenjangnya sendiri.
     * - Super Admin / Pengasuh / Pembina : melihat gabungan seluruh jenjang
     *   (agregat) atau memilih satu jenjang lewat `?jenjang=slug`.
     */
    public function adminJenjang(Request $request)
    {
        $user = auth()->user();
        $infoUser = Akses::jenjangInfo($user);
        $tanpaJenjang = Akses::adminTanpaJenjang($user);

        $slug = $infoUser['slug'] ?? Jenjang::normalisasi($request->input('jenjang'));
        $info = $slug ? Jenjang::cari($slug) : null;
        $slug = $info['slug'] ?? null;

        $nilaiSantri = $slug ? RekapJenjang::nilaiDb(Santri::class, 'jenjang_pendidikan', $slug) : [];
        $nilaiPendaftar = $slug ? RekapJenjang::nilaiDb(PendaftaranOnline::class, 'jenjang_tujuan', $slug) : [];
        $nilaiAlumni = $slug ? RekapJenjang::nilaiDb(Alumni::class, 'jenjang', $slug) : [];

        // Role global tanpa pilihan jenjang = agregat seluruh jenjang.
        $saring = function ($query, array $nilai, string $kolom) use ($slug, $tanpaJenjang) {
            if ($tanpaJenjang) {
                return $query->whereRaw('1 = 0');
            }

            if (! $slug) {
                return $query;
            }

            return $nilai ? $query->whereIn($kolom, $nilai) : $query->whereRaw('1 = 0');
        };

        $santri = fn ($q) => $saring($q, $nilaiSantri, 'jenjang_pendidikan');
        $pendaftar = fn ($q) => $saring($q, $nilaiPendaftar, 'jenjang_tujuan');
        $alumni = fn ($q) => $saring($q, $nilaiAlumni, 'jenjang');

        $menunggu = function ($q) {
            $q->where(function ($w) {
                $w->whereNull('status_pendaftaran')
                    ->orWhereNotIn('status_pendaftaran', ['diterima', 'ditolak', 'Diterima', 'Ditolak']);
            });
        };

        $pendaftarMenunggu = function ($q) use ($pendaftar, $menunggu) {
            $pendaftar($q);
            $menunggu($q);
        };

        $prestasiSiap = $this->dataPrestasiSiap();

        if (! $prestasiSiap || $tanpaJenjang) {
            $totalPrestasi = 0;
        } elseif ($slug) {
            $totalPrestasi = $this->hitung(PrestasiSantri::class, fn ($q) => $q->where('jenjang', $slug));
        } else {
            $totalPrestasi = $this->hitung(PrestasiSantri::class);
        }

        $totalAlumni = $this->hitung(Alumni::class, $alumni);
        $nilaiSiap = Alumni::siapNilai();
        $alumniPunyaNilai = $nilaiSiap
            ? $this->hitung(Alumni::class, function ($q) use ($alumni) {
                $alumni($q);
                $q->whereNotNull('nilai_path')->where('nilai_path', '!=', '');
            })
            : 0;

        $alumniTerbaru = collect();
        if ($this->siap(Alumni::class)) {
            try {
                $query = Alumni::query();
                $alumni($query);

                if (\App\Support\Kelulusan::siapTautan()) {
                    $query->with('santri');
                }

                $alumniTerbaru = $query->orderByDesc('tahun_lulus')->orderBy('nama')->take(6)->get();
            } catch (\Throwable $e) {
                $alumniTerbaru = collect();
            }
        }

        return view('dashboard.roles.admin-jenjang', array_merge([
            'jenjang' => $info,
            'jenjangPilihan' => $slug,
            'daftarJenjang' => Jenjang::semua(),
            'tanpaJenjang' => $tanpaJenjang,
            'modeAgregat' => $slug === null && ! $tanpaJenjang,
            'rekapJenjang' => RekapJenjang::ringkasan(),
            'totalSantri' => $this->hitung(Santri::class, $santri),
            'totalPendaftar' => $this->hitung(PendaftaranOnline::class, $pendaftar),
            'pendaftarMenunggu' => $this->hitung(PendaftaranOnline::class, $pendaftarMenunggu),
            'totalAlumni' => $totalAlumni,
            'totalPrestasi' => $totalPrestasi,
            'nilaiSiap' => $nilaiSiap,
            'alumniPunyaNilai' => $alumniPunyaNilai,
            'alumniBelumNilai' => max(0, $totalAlumni - $alumniPunyaNilai),
            'alumniTerbaru' => $alumniTerbaru,
            'bolehNilai' => $user->bisa('akademik.ubah'),
            'bolehPrestasi' => $user->bisa('prestasi.ubah'),
            'bolehAlumni' => $user->bisa('alumni.ubah'),
            'pendaftarTerbaru' => $this->terbaru(PendaftaranOnline::class, $pendaftar, 5),
            'santriTerbaru' => $this->terbaru(Santri::class, $santri, 5),
        ], $this->dataInput($user, $info, 5, $slug)));
    }

    /**
     * Data panel Dashboard Jenjang tanpa merender view.
     *
     * Dipakai dashboard Super Admin agar komponen dashboard jenjang (statistik,
     * form nilai/prestasi, alumni, santri) tampil dengan data yang sama persis.
     *
     * @return array<string, mixed>
     */
    public function dataJenjang(Request $request): array
    {
        return $this->adminJenjang($request)->getData();
    }

    /**
     * Data form nilai/prestasi untuk satu jenjang.
     *
     * Dipakai halaman /admin/jenjang/{slug} agar form input yang tampil sama
     * dengan Dashboard Jenjang.
     *
     * @return array<string, mixed>
     */
    public function dataInputJenjang(string $slug, int $limit = 5): array
    {
        $user = auth()->user();
        $info = Jenjang::cari($slug);

        return $this->dataInput($user, $info, $limit, $slug);
    }

    /** Halaman khusus input nilai santri (multi jenjang untuk role global). */
    public function formNilai(Request $request)
    {
        // Halaman input nilai sudah digabung ke menu Akademik → Nilai Santri.
        // Route lama dipertahankan sebagai pengalihan agar tautan lama tetap
        // berfungsi dan langsung membuka panel input cepat.
        $user = $request->user();
        $jenjang = Akses::jenjangSlug($user) ?: Jenjang::normalisasi($request->input('jenjang'));

        return redirect()
            ->route('admin.akademik.nilai.index', array_filter(['jenjang' => $jenjang]))
            ->withFragment('input-cepat');
    }

    /** Halaman khusus input prestasi santri (multi jenjang untuk role global). */
    public function formPrestasi(Request $request)
    {
        $user = auth()->user();
        $infoUser = Akses::jenjangInfo($user);
        $slug = $infoUser['slug'] ?? Jenjang::normalisasi($request->input('jenjang'));
        $info = $slug ? Jenjang::cari($slug) : null;

        return view('dashboard.roles.input-prestasi', array_merge([
            'jenjang' => $info,
            'jenjangPilihan' => $info['slug'] ?? null,
            'daftarJenjang' => Jenjang::semua(),
            'tanpaJenjang' => Akses::adminTanpaJenjang($user),
            'bolehNilai' => $user->bisa('akademik.ubah'),
            'bolehPrestasi' => $user->bisa('prestasi.ubah'),
        ], $this->dataInput($user, $info, 12, $info['slug'] ?? null)));
    }

    /**
     * Data bersama untuk dashboard jenjang dan halaman input khusus.
     *
     * @return array<string, mixed>
     */
    private function dataInput($user, ?array $info, int $limit = 5, ?string $slug = null): array
    {
        $slug = $slug ?? ($info['slug'] ?? null);
        $akademikSiap = Akademik::siap();
        $prestasiSiap = $this->dataPrestasiSiap();
        $dokumenSiap = DokumenNilai::siap();
        $tanpaJenjang = Akses::adminTanpaJenjang($user);

        // Tanpa pilihan jenjang: role global melihat gabungan semua jenjang,
        // sedangkan admin tanpa jenjang tidak melihat data apa pun.
        $saringJenjang = function ($query, string $kolom) use ($slug, $tanpaJenjang) {
            if ($tanpaJenjang) {
                return $query->whereRaw('1 = 0');
            }

            return $slug ? $query->where($kolom, $slug) : $query;
        };

        $santriList = $slug
            ? Akademik::santriQuery($user, $slug)->orderBy('nama_lengkap')->get()
            : collect();

        $mapelList = $slug && $akademikSiap
            ? Akademik::mapelQuery($user, $slug)->where('aktif', true)->orderBy('nama')->get()
            : collect();

        $nilaiTerbaru = $akademikSiap
            ? $saringJenjang(NilaiSantri::query()->with(['santri', 'mataPelajaran']), 'jenjang')
                ->latest()
                ->take($limit)
                ->get()
            : collect();

        $prestasiTerbaru = $prestasiSiap
            ? $saringJenjang(PrestasiSantri::query()->with('santri'), 'jenjang')
                ->latest()
                ->take($limit)
                ->get()
            : collect();

        $dokumenTerbaru = $dokumenSiap
            ? $saringJenjang(DokumenNilai::query()->with('santri'), 'jenjang')
                ->latest()
                ->take($limit)
                ->get()
            : collect();

        return [
            'santriList' => $santriList,
            'mapelList' => $mapelList,
            'nilaiTerbaru' => $nilaiTerbaru,
            'prestasiTerbaru' => $prestasiTerbaru,
            'akademikSiap' => $akademikSiap,
            'prestasiSiap' => $prestasiSiap,
            'dokumenSiap' => $dokumenSiap,
            'dokumenTerbaru' => $dokumenTerbaru,
            'tahunPilihan' => Akademik::tahunAjaranPilihan(),
            'tahunSekarang' => Akademik::tahunAjaranSekarang(),
            'semesterSekarang' => Akademik::semesterSekarang(),
            'semesterList' => Akademik::SEMESTER,
            'kategoriNilai' => Akademik::KATEGORI,
            'tingkatPrestasi' => PrestasiAdminController::TINGKAT,
            'kategoriPrestasi' => $prestasiSiap
                ? PrestasiSantri::query()->distinct()->pluck('kategori')->filter()->values()
                : collect(),
        ];
    }

    /** Apakah kolom penghubung prestasi sudah dimigrasikan. */
    private function dataPrestasiSiap(): bool
    {
        return $this->siap(PrestasiSantri::class)
            && Schema::hasColumn('prestasi_santri', 'santri_id')
            && Schema::hasColumn('prestasi_santri', 'jenjang');
    }

    /**
     * Simpan satu nilai dari dashboard Admin Jenjang.
     *
     * Nilai langsung diterbitkan (status published) agar bisa dilihat wali
     * santri pada menu Nilai di portal. Jenjang & kelas diambil dari data
     * santri yang dipilih, bukan dari input, agar tidak bisa dipalsukan.
     */
    public function simpanNilai(Request $request)
    {
        $user = $request->user();
        abort_unless($user->bisa('akademik.ubah'), 403);

        if (! Akademik::siap()) {
            return back()->with('error', 'Modul akademik belum siap. Jalankan migrasi terlebih dahulu.');
        }

        $data = $request->validate([
            'jenjang' => ['nullable', 'string', 'max:100'],
            'santri_id' => ['required', 'integer'],
            'mata_pelajaran_id' => ['required', 'integer'],
            'tahun_ajaran' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', Rule::in(array_keys(Akademik::SEMESTER))],
            'kategori' => ['required', Rule::in(array_keys(Akademik::KATEGORI))],
            'nilai' => ['required', 'numeric', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ], [
            'santri_id.required' => 'Pilih santri terlebih dahulu.',
            'mata_pelajaran_id.required' => 'Pilih mata pelajaran.',
            'tahun_ajaran.regex' => 'Format tahun ajaran harus seperti 2026/2027.',
            'nilai.required' => 'Nilai wajib diisi.',
            'nilai.numeric' => 'Nilai harus berupa angka.',
            'nilai.min' => 'Nilai minimal 0.',
            'nilai.max' => 'Nilai maksimal 100.',
        ]);

        // Jenjang harus jelas: dari akun Admin Jenjang, atau dari pilihan
        // jenjang pada form untuk Super Admin. Permintaan yang menyebut
        // jenjang lain ditolak agar nilai tidak bisa menyeberang jenjang.
        $jenjangAdmin = Akses::jenjangSlug($user);
        $jenjangInput = Jenjang::normalisasi($data['jenjang'] ?? '');

        if ($jenjangAdmin && $jenjangInput && $jenjangInput !== $jenjangAdmin) {
            abort(403, 'Jenjang ini di luar kewenangan Anda.');
        }

        $jenjang = $jenjangAdmin ?: $jenjangInput;

        if (! $jenjang) {
            throw ValidationException::withMessages([
                'jenjang' => 'Pilih jenjang terlebih dahulu sebelum menyimpan nilai.',
            ]);
        }

        abort_unless(Akses::cocokJenjang($user, $jenjang), 403, 'Jenjang ini di luar kewenangan Anda.');

        // Santri harus benar-benar berada di jenjang yang dipilih, bukan
        // sekadar jenjang mana pun yang boleh diakses akun ini.
        $santri = Akademik::santriQuery($user, $jenjang)->whereKey($data['santri_id'])->first();

        if (! $santri) {
            throw ValidationException::withMessages([
                'santri_id' => 'Santri tidak ditemukan pada jenjang yang dipilih.',
            ]);
        }

        $jenjangSantri = Jenjang::normalisasi($santri->jenjang_pendidikan);

        if ($jenjangSantri !== $jenjang) {
            throw ValidationException::withMessages([
                'santri_id' => 'Santri ini bukan bagian dari jenjang yang dipilih.',
            ]);
        }

        $mapel = MataPelajaran::query()->where('aktif', true)->find($data['mata_pelajaran_id']);

        if (! $mapel || Jenjang::normalisasi($mapel->jenjang) !== $jenjang) {
            throw ValidationException::withMessages([
                'mata_pelajaran_id' => 'Mata pelajaran ini bukan untuk jenjang yang dipilih.',
            ]);
        }

        $row = NilaiSantri::firstOrNew([
            'santri_id' => $santri->id,
            'mata_pelajaran_id' => $mapel->id,
            'tahun_ajaran' => $data['tahun_ajaran'],
            'semester' => $data['semester'],
            'kategori' => $data['kategori'],
        ]);

        $baru = ! $row->exists;

        $row->fill([
            'jenjang' => $jenjangSantri,
            'kelas' => $santri->kelas ?: '-',
            'nilai' => $data['nilai'],
            'catatan' => filled($data['catatan'] ?? null) ? trim($data['catatan']) : null,
            'status' => 'published',
            'published_at' => now(),
            'diperbarui_oleh' => $user->id,
            'diterbitkan_oleh' => $user->id,
        ]);

        if ($baru) {
            $row->created_by = $user->id;
        }

        $row->save();

        return back()->with(
            'success',
            'Nilai '.$mapel->nama.' untuk '.$santri->nama_lengkap.' berhasil '
            .($baru ? 'disimpan' : 'diperbarui')
            .' dan langsung terbit di portal wali santri.'
        );
    }

    /**
     * Simpan prestasi santri dari dashboard Admin Jenjang.
     *
     * Prestasi otomatis tampil di halaman publik /prestasi karena memakai
     * tabel yang sama, lalu notifikasi email dikirim ke akun wali yang
     * tertaut dengan data santri (bila tersedia).
     */
    public function simpanPrestasi(Request $request)
    {
        $user = $request->user();
        abort_unless($user->bisa('prestasi.ubah'), 403);

        if (! $this->siap(PrestasiSantri::class)
            || ! Schema::hasColumn('prestasi_santri', 'santri_id')
            || ! Schema::hasColumn('prestasi_santri', 'jenjang')) {
            return back()->with(
                'error',
                'Kolom prestasi belum lengkap. Jalankan php artisan migrate terlebih dahulu.'
            );
        }

        $data = $request->validate([
            'santri_id' => ['required', 'integer'],
            'judul' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'string', 'max:60'],
            'tingkat' => ['required', Rule::in(PrestasiAdminController::TINGKAT)],
            'wilayah' => ['required', 'string', 'max:100'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:'.(date('Y') + 1)],
            'peringkat' => ['required', 'integer', 'min:0', 'max:99'],
            'label' => ['nullable', 'string', 'max:40'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ], [
            'santri_id.required' => 'Pilih santri terlebih dahulu.',
            'judul.required' => 'Nama lomba / capaian wajib diisi.',
            'kategori.required' => 'Bidang lomba wajib diisi.',
            'tingkat.required' => 'Tingkat prestasi wajib dipilih.',
            'wilayah.required' => 'Lokasi lomba wajib diisi.',
            'tahun.required' => 'Tahun prestasi wajib diisi.',
            'peringkat.required' => 'Peringkat wajib diisi.',
        ]);

        // Pastikan santri berada di jenjang admin ini.
        $santri = Akademik::santriQuery($user)->whereKey($data['santri_id'])->first();

        if (! $santri) {
            throw ValidationException::withMessages([
                'santri_id' => 'Santri tidak ditemukan pada jenjang yang Anda tangani.',
            ]);
        }

        $label = filled($data['label'] ?? null)
            ? trim($data['label'])
            : ((int) $data['peringkat'] > 0 ? 'Juara '.$data['peringkat'] : 'Penghargaan');

        $prestasi = PrestasiSantri::create([
            'santri_id' => $santri->id,
            'jenjang' => Jenjang::normalisasi($santri->jenjang_pendidikan),
            'judul' => $data['judul'],
            'nama' => $santri->nama_lengkap,
            'kelas' => $santri->kelas ?: '-',
            'kategori' => $data['kategori'],
            'tingkat' => $data['tingkat'],
            'wilayah' => $data['wilayah'],
            'tahun' => $data['tahun'],
            'peringkat' => $data['peringkat'],
            'label' => $label,
            'deskripsi' => filled($data['deskripsi'] ?? null) ? trim($data['deskripsi']) : null,
            'dicatat_oleh' => $user->id,
        ]);

        // Kirim notifikasi ke akun wali yang tertaut dengan santri.
        $wali = $santri->user;
        $infoEmail = 'Akun wali santri belum tertaut, notifikasi email belum bisa dikirim.';

        if ($wali && filled($wali->email)) {
            try {
                Mail::to($wali->email, $wali->name)->send(new PrestasiSantriMail($prestasi, $santri));
                $infoEmail = 'Notifikasi email juga dikirim ke '.$wali->email.'.';
            } catch (\Throwable $e) {
                report($e);
                $infoEmail = 'Prestasi tersimpan, tetapi email notifikasi ke wali gagal dikirim.';
            }
        }

        return back()->with(
            'success',
            'Prestasi "'.$prestasi->judul.'" untuk '.$santri->nama_lengkap
            .' berhasil disimpan dan tampil di halaman /prestasi. '.$infoEmail
        );
    }

    /**
     * Unggah rapor nilai (PDF) untuk satu santri.
     *
     * Satu santri hanya menyimpan satu PDF per tahun ajaran + semester.
     * Unggah ulang akan menimpa berkas lama dan langsung terbit ke portal wali.
     */
    public function simpanDokumenNilai(Request $request)
    {
        $user = $request->user();
        abort_unless($user->bisa('akademik.ubah'), 403);

        if (! DokumenNilai::siap()) {
            return back()->with(
                'error',
                'Tabel dokumen nilai belum tersedia. Jalankan php artisan migrate terlebih dahulu.'
            );
        }

        $data = $request->validate([
            'santri_id' => ['required', 'integer'],
            'tahun_ajaran' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', Rule::in(array_keys(Akademik::SEMESTER))],
            'judul' => ['nullable', 'string', 'max:150'],
            'file_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'santri_id.required' => 'Pilih santri terlebih dahulu.',
            'tahun_ajaran.regex' => 'Format tahun ajaran harus seperti 2026/2027.',
            'file_pdf.required' => 'Pilih file PDF rapor yang akan diunggah.',
            'file_pdf.mimes' => 'File rapor harus berformat PDF.',
            'file_pdf.max' => 'Ukuran file PDF maksimal 10 MB.',
        ]);

        // Pastikan santri benar-benar berada di jenjang admin ini.
        $santri = Akademik::santriQuery($user)->whereKey($data['santri_id'])->first();

        if (! $santri) {
            throw ValidationException::withMessages([
                'santri_id' => 'Santri tidak ditemukan pada jenjang yang Anda tangani.',
            ]);
        }

        $file = $request->file('file_pdf');

        $dokumen = RaporNilai::simpan(
            $santri,
            $file,
            $data['tahun_ajaran'],
            $data['semester'],
            $user,
            $data['judul'] ?? null
        );

        return back()->with(
            'success',
            'Rapor PDF '.$santri->nama_lengkap.' ('.$dokumen->judul.') berhasil '
            .($dokumen->wasRecentlyCreated ? 'diunggah' : 'diperbarui')
            .' dan langsung terlihat di Portal Wali Santri.'
        );
    }

    /**
     * Unggah banyak rapor PDF sekaligus dari halaman input nilai jenjang.
     *
     * Berkas dicocokkan ke santri berdasarkan NIS (token utuh) lalu nama
     * lengkap pada nama file. Berkas ambigu/tidak cocok dilewati dan
     * dilaporkan agar rapor tidak salah menempel ke santri lain.
     */
    public function pdfMassal(Request $request)
    {
        $user = $request->user();
        abort_unless($user->bisa('akademik.ubah'), 403);

        if (! RaporNilai::siap()) {
            return back()->with(
                'error',
                'Tabel dokumen nilai belum tersedia. Jalankan php artisan migrate terlebih dahulu.'
            );
        }

        $data = $request->validate([
            'jenjang' => ['nullable', 'string', 'max:100'],
            'tahun_ajaran' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', Rule::in(array_keys(Akademik::SEMESTER))],
            'files' => ['required', 'array', 'min:1', 'max:50'],
            'files.*' => ['required', 'file', 'mimes:pdf', 'max:'.RaporNilai::MAKS_KB],
        ], [
            'tahun_ajaran.regex' => 'Format tahun ajaran harus seperti 2026/2027.',
            'files.required' => 'Pilih minimal satu file PDF rapor.',
            'files.min' => 'Pilih minimal satu file PDF rapor.',
            'files.max' => 'Maksimal 50 berkas per unggahan. Lakukan dalam beberapa kali.',
            'files.*.mimes' => 'Semua berkas harus berformat PDF.',
            'files.*.max' => 'Ukuran setiap file PDF maksimal 10 MB.',
        ]);

        $jenjang = Akses::jenjangSlug($user) ?: Jenjang::normalisasi($data['jenjang'] ?? '');

        if ($jenjang) {
            abort_unless(Akses::cocokJenjang($user, $jenjang), 403, 'Jenjang ini di luar kewenangan Anda.');
        }

        $santris = Akademik::santriQuery($user, $jenjang)
            ->orderBy('nama_lengkap')
            ->get();

        if ($santris->isEmpty()) {
            return back()->with(
                'error',
                'Tidak ada santri pada jenjang ini, sehingga rapor PDF tidak dapat dicocokkan.'
            );
        }

        $berhasil = [];
        $gagal = [];
        $sudah = [];

        foreach ($request->file('files', []) as $file) {
            if (! $file) {
                continue;
            }

            $nama = $file->getClientOriginalName();
            $hasil = RaporNilai::cocokkan($nama, $santris);
            $santri = $hasil['santri'];

            if (! $santri) {
                $gagal[] = ['file' => $nama, 'alasan' => $hasil['alasan']];

                continue;
            }

            if (isset($sudah[$santri->id])) {
                $gagal[] = [
                    'file' => $nama,
                    'alasan' => 'Ada lebih dari satu berkas untuk '.$santri->nama_lengkap
                        .' pada unggahan ini. Berkas ini dilewati.',
                ];

                continue;
            }

            try {
                RaporNilai::simpan($santri, $file, $data['tahun_ajaran'], $data['semester'], $user);
            } catch (\Throwable $e) {
                report($e);
                $gagal[] = [
                    'file' => $nama,
                    'alasan' => 'Gagal menyimpan berkas. Silakan coba unggah ulang.',
                ];

                continue;
            }

            $sudah[$santri->id] = true;
            $berhasil[] = [
                'file' => $nama,
                'nama' => $santri->nama_lengkap,
                'kelas' => $santri->kelas ?: '-',
                'cara' => $hasil['cara'] === 'nis' ? 'NIS' : 'nama',
            ];
        }

        if ($berhasil === []) {
            $pesan = 'Tidak ada rapor yang tersimpan. '.count($gagal).' berkas dilewati.';
        } else {
            $pesan = count($berhasil).' rapor PDF berhasil diunggah dan diterbitkan ke Portal Wali Santri.';

            if ($gagal !== []) {
                $pesan .= ' '.count($gagal).' berkas dilewati.';
            }
        }

        return back()
            ->with($berhasil === [] ? 'error' : 'success', $pesan)
            ->with('pdf_hasil', ['berhasil' => $berhasil, 'gagal' => $gagal]);
    }

    /** Unduh rapor PDF dari dashboard (dibatasi ke jenjang admin). */
    public function unduhDokumenNilai(Request $request, DokumenNilai $dokumen)
    {
        $user = $request->user();
        abort_unless($user->bisa('akademik.lihat'), 403);

        $milikJenjang = Akademik::santriQuery($user)
            ->whereKey($dokumen->santri_id)
            ->exists();

        abort_unless($milikJenjang, 403, 'Dokumen ini bukan milik jenjang yang Anda tangani.');
        abort_unless(
            filled($dokumen->file_path) && Storage::disk('public')->exists($dokumen->file_path),
            404,
            'Berkas PDF tidak ditemukan.'
        );

        return Storage::disk('public')->download(
            $dokumen->file_path,
            $dokumen->file_name ?: 'rapor-'.$dokumen->tahun_ajaran.'.pdf'
        );
    }

    /** Hapus rapor PDF beserta berkas fisiknya. */
    public function hapusDokumenNilai(Request $request, DokumenNilai $dokumen)
    {
        $user = $request->user();
        abort_unless($user->bisa('akademik.ubah'), 403);

        $milikJenjang = Akademik::santriQuery($user)
            ->whereKey($dokumen->santri_id)
            ->exists();

        abort_unless($milikJenjang, 403, 'Dokumen ini bukan milik jenjang yang Anda tangani.');

        $nama = $dokumen->file_name;
        $dokumen->delete();

        return back()->with('success', 'Rapor PDF "'.$nama.'" berhasil dihapus.');
    }

    /** Dashboard Pengurus: ringkasan seluruh pesantren (hanya baca). */
    public function pengurus()
    {
        $menunggu = function ($q) {
            $q->where(function ($w) {
                $w->whereNull('status_pendaftaran')
                    ->orWhereNotIn('status_pendaftaran', ['diterima', 'ditolak', 'Diterima', 'Ditolak']);
            });
        };

        Izin::tandaiTerlambat();

        return view('dashboard.roles.pengurus', [
            'totalSantri' => $this->hitung(Santri::class),
            'totalPendaftar' => $this->hitung(PendaftaranOnline::class),
            'pendaftarMenunggu' => $this->hitung(PendaftaranOnline::class, $menunggu),
            'totalAlumni' => $this->hitung(Alumni::class),
            'totalPrestasi' => $this->hitung(PrestasiSantri::class),
            'totalAsatidz' => $this->hitung(Kepengurusan::class),
            'totalBerita' => $this->hitung(Berita::class, fn ($q) => $q->published()),
            'jumlahPengumuman' => $this->hitung(Pengumuman::class),
            'jenjangStats' => RekapJenjang::ringkasan(),
            'pendaftarTerbaru' => $this->terbaru(PendaftaranOnline::class, null, 6),
        ]);
    }

    /** Alias lama untuk kompatibilitas. */
    public function pengasuh()
    {
        return $this->pengurus();
    }

    /** Dashboard Pembina: fokus pembinaan harian. */
    public function pembina()
    {
        Izin::tandaiTerlambat();

        return view('dashboard.roles.pembina', [
            'totalSantri' => $this->hitung(Santri::class),
            'totalPrestasi' => $this->hitung(PrestasiSantri::class),
            'totalAlumni' => $this->hitung(Alumni::class),
            'absensiHariIni' => $this->hitung(Absensi::class, fn ($q) => $q->whereDate('tanggal', now()->toDateString())),
            'hafalanHariIni' => $this->hitung(Hafalan::class, fn ($q) => $q->whereDate('tanggal', now()->toDateString())),
            'pelanggaranBulanIni' => $this->hitung(Pelanggaran::class, fn ($q) => $q->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)),
            'izinMenunggu' => $this->hitung(Izin::class, fn ($q) => $q->where('status', Izin::MENUNGGU)),
            'izinTerlambat' => $this->hitung(Izin::class, fn ($q) => $q->where('status', Izin::TERLAMBAT)),
            'jenjangStats' => RekapJenjang::ringkasan(),
            'santriTerbaru' => $this->terbaru(Santri::class, null, 6),
            'izinTerbaru' => $this->terbaru(Izin::class, fn ($q) => $q->with('santri'), 6),
        ]);
    }

    /** Hitung baris dengan aman (tabel/kolom belum ada tidak membuat error). */
    private function hitung(string $model, ?callable $filter = null): int
    {
        if (! $this->siap($model)) {
            return 0;
        }

        try {
            $query = $model::query();
            if ($filter) {
                $filter($query);
            }

            return (int) $query->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /** Ambil beberapa baris terbaru dengan aman. */
    private function terbaru(string $model, ?callable $filter = null, int $limit = 5)
    {
        if (! $this->siap($model)) {
            return collect();
        }

        try {
            $query = $model::query();
            if ($filter) {
                $filter($query);
            }

            return $query->latest()->take($limit)->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function siap(string $model): bool
    {
        try {
            if (! class_exists($model)) {
                return false;
            }

            /** @var Model $instance */
            $instance = new $model;

            return Schema::hasTable($instance->getTable());
        } catch (\Throwable $e) {
            return false;
        }
    }
}
