<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Models\User;
use App\Support\AkunPengguna;
use App\Support\Akses;
use App\Support\Jenjang;
use App\Support\Ppdb;
use App\Support\RoleSistem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

/**
 * Kelola akun dashboard & portal.
 *
 * Sumber peran: Spatie Permission (6 peran resmi, lihat App\Support\RoleSistem).
 * Kolom lama `users.role` + `users.jenjang` selalu disinkronkan agar modul yang
 * belum dimigrasi tetap berjalan.
 *
 * Pembagian hak:
 * - Super Admin      : semua akun, semua peran.
 * - Pengasuh/Pembina : akun Admin Jenjang + Wali Santri, serta mengaktifkan
 *                      akun wali yang masih pending.
 * - Admin Jenjang    : hanya akun Wali Santri pada jenjangnya.
 *
 * Pengaman:
 * - Super Admin terakhir tidak boleh diturunkan perannya atau dihapus.
 * - Akun sendiri tidak boleh dihapus.
 * - Admin Jenjang wajib memiliki satu jenjang (disimpan di users.jenjang).
 */
class PenggunaController extends Controller
{
    /** Label peran resmi (name => label). */
    public const ROLES = RoleSistem::ROLES;

    /**
     * Kategori tab daftar pengguna.
     * `roles` = nilai kolom lama (users.role) agar query tetap sederhana.
     */
    public const KATEGORI = [
        'super_admin' => [
            'label' => 'Super Admin',
            'roles' => ['super_admin'],
            'ikon' => 'fa-user-shield',
            'warna' => 'red',
            'deskripsi' => 'Pemilik sistem dengan akses penuh ke seluruh panel.',
        ],
        'admin' => [
            'label' => 'Admin Jenjang',
            'roles' => ['admin'],
            'ikon' => 'fa-layer-group',
            'warna' => 'emerald',
            'deskripsi' => 'Admin MDTA, PPS Wustha, MTs, MA, PDF Wustha/Ulya, dan Ma\'had \'Aly.',
        ],
        'pengurus' => [
            'label' => 'Pengasuh & Pembina',
            'roles' => ['pengasuh', 'pembina'],
            'ikon' => 'fa-mosque',
            'warna' => 'amber',
            'deskripsi' => 'Monitoring pesantren dan pendaftaran akun Admin Jenjang/Wali Santri.',
        ],
        'wali_santri' => [
            'label' => 'Wali Santri',
            'roles' => ['wali_santri'],
            'ikon' => 'fa-people-roof',
            'warna' => 'sky',
            'deskripsi' => 'Hanya memantau data anak yang dikirim oleh Admin Jenjang.',
        ],
        'alumni' => [
            'label' => 'Alumni',
            'roles' => ['alumni'],
            'ikon' => 'fa-graduation-cap',
            'warna' => 'violet',
            'deskripsi' => 'Akun alumni untuk mengakses halaman alumni.',
        ],
    ];

    /** Pilihan jenjang: slug => nama. */
    public static function jenjangOptions(): array
    {
        return collect(Jenjang::semua())
            ->mapWithKeys(fn ($j) => [$j['slug'] => $j['kode'].' — '.$j['nama']])
            ->all();
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $bolehSemua = Akses::superAdmin($user);
        $punyaStatus = Schema::hasColumn('users', 'status');

        $tabs = $this->tabs($user);

        $kategoriAktif = (string) $request->kategori;
        if (! array_key_exists($kategoriAktif, $tabs)) {
            $kategoriAktif = array_key_first($tabs);
        }

        $dasar = $this->queryTerlihat($user)
            ->when($request->filled('q'), function ($q) use ($request) {
                $cari = trim((string) $request->input('q'));

                $q->where(function ($w) use ($cari) {
                    $w->where('name', 'like', "%{$cari}%")
                        ->orWhere('email', 'like', "%{$cari}%");
                });
            })
            ->when($punyaStatus && $request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('jenjang'), fn ($q) => $q->where('jenjang', $request->input('jenjang')));

        $hitung = [];
        foreach ($tabs as $key => $tab) {
            $hitung[$key] = $key === 'semua'
                ? (clone $dasar)->count()
                : (clone $dasar)->whereIn('role', $tab['roles'])->count();
        }

        $penggunas = (clone $dasar)
            ->when($kategoriAktif !== 'semua', fn ($q) => $q->whereIn('role', $tabs[$kategoriAktif]['roles']))
            ->withCount('santri')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statistik = [
            'total' => (clone $this->queryTerlihat($user))->count(),
            'aktif' => $punyaStatus
                ? (clone $this->queryTerlihat($user))->where('status', 'active')->count()
                : 0,
            'pending' => $punyaStatus
                ? (clone $this->queryTerlihat($user))->where('status', 'pending')->count()
                : 0,
        ];

        return view('dashboard.pengguna.index', [
            'penggunas' => $penggunas,
            'roles' => RoleSistem::daftar(),
            'rolesBolehDibuat' => AkunPengguna::roleBolehDibuat($user),
            'jenjangList' => self::jenjangOptions(),
            'santriList' => $this->santriOptions(),
            'tabs' => $tabs,
            'hitung' => $hitung,
            'kategoriAktif' => $kategoriAktif,
            'bolehSemua' => $bolehSemua,
            'bisaReset' => $bolehSemua,
            'statistik' => $statistik,
            'punyaStatus' => $punyaStatus,
            'filter' => $request->only(['q', 'status', 'jenjang']),
        ]);
    }

    public function create()
    {
        return view('dashboard.pengguna.create', $this->dataForm(auth()->user()));
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        $role = $data['role'];
        $jenjang = $data['jenjang'] ?? null;
        $santriId = $data['santri_id'] ?? null;
        $password = $data['password'] ?? null;

        unset($data['santri_id'], $data['role'], $data['jenjang']);

        // Akun yang dibuat pengelola langsung aktif. Bila kata sandi kosong,
        // gunakan kata sandi default sistem (babul2026).
        $data['password'] = $password ?: AkunPengguna::passwordDefault();
        $data['status'] = 'active';

        $pengguna = User::create($data);
        $pengguna->forceFill(['email_verified_at' => now()])->save();
        $pengguna->setPeran($role, $jenjang);

        if ($santriId) {
            $this->tautkanSantri($pengguna, (int) $santriId);
        }

        return redirect()->route('super.pengguna.index', $this->kategoriRedirect($role))
            ->with('success', 'Pengguna berhasil ditambahkan dengan peran '.RoleSistem::label($role).'.'
                .' Kata sandi: '.($password ?: AkunPengguna::passwordDefault()).'.');
    }

    public function edit(User $pengguna)
    {
        $this->pastikanBolehKelola($pengguna);

        return view('dashboard.pengguna.edit', array_merge(
            $this->dataForm(auth()->user(), $pengguna),
            ['pengguna' => $pengguna]
        ));
    }

    public function update(Request $request, User $pengguna)
    {
        $this->pastikanBolehKelola($pengguna);

        $user = auth()->user();
        $data = $this->validasi($request, $pengguna);

        $role = $data['role'];
        $jenjang = $data['jenjang'] ?? null;
        $santriId = $data['santri_id'] ?? null;

        unset($data['santri_id'], $data['role'], $data['jenjang']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Super Admin terakhir tidak boleh diturunkan perannya.
        if (AkunPengguna::superAdminTerakhir($pengguna) && $role !== 'super-admin') {
            return back()->withErrors([
                'role' => 'Super Admin terakhir tidak boleh diturunkan perannya. Tambahkan Super Admin lain terlebih dahulu.',
            ])->withInput();
        }

        // Jika peran berubah dari wali santri, lepaskan tautan santri/siswa.
        if ($pengguna->peranSistem() === 'walisantri' && $role !== 'walisantri') {
            $pengguna->santri()->update(['user_id' => null]);
            $pengguna->students()->update(['user_id' => null]);
        }

        $pengguna->fill($data)->save();
        $pengguna->setPeran($role, $jenjang);

        if ($santriId) {
            // Satu akun wali dipantau untuk satu santri: lepaskan tautan lama
            // yang berbeda sebelum menautkan pilihan baru.
            $pengguna->santri()->where('id', '!=', (int) $santriId)->update(['user_id' => null]);

            $this->tautkanSantri($pengguna, (int) $santriId);
        }

        return redirect()->route('super.pengguna.index', $this->kategoriRedirect($role))
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /** Reset kata sandi ke default `babul2026` (khusus Super Admin). */
    public function resetPassword(User $pengguna)
    {
        $this->pastikanBolehKelola($pengguna);

        abort_unless(Akses::superAdmin(auth()->user()), 403, 'Hanya Super Admin yang boleh mereset kata sandi.');

        $password = AkunPengguna::passwordDefault();

        $data = ['password' => $password];

        if (Schema::hasColumn('users', 'password_generated_at')) {
            $data['password_generated_at'] = now();
        }

        $pengguna->forceFill($data)->save();

        $terkirim = null;

        if ($pengguna->peranSistem() === 'walisantri') {
            $terkirim = AkunPengguna::kirimPassword($pengguna, $password, false);
        }

        $pesan = 'Kata sandi berhasil direset ke '.$password.'.';

        if ($terkirim === true) {
            $pesan .= ' Kredensial baru telah dikirim ke '.$pengguna->email.'.';
        } elseif ($terkirim === false) {
            $pesan .= ' Namun email gagal dikirim; sampaikan kata sandi secara langsung kepada pengguna.';
        }

        return back()->with('success', $pesan);
    }

    /**
     * Aktifkan akun yang masih pending.
     *
     * Sistem membuat kata sandi acak, menandai akun aktif, lalu mengirimkan
     * kredensial ke email wali (bila perannya wali santri).
     */
    public function aktifkan(User $pengguna)
    {
        $this->pastikanBolehKelola($pengguna);

        if ($pengguna->sudahAktif()) {
            return back()->with('error', 'Akun '.$pengguna->name.' sudah berstatus Aktif.');
        }

        $kirimEmail = $pengguna->peranSistem() === 'walisantri';

        $hasil = AkunPengguna::aktifkan($pengguna, null, $kirimEmail, false);

        $pesan = 'Akun '.$pengguna->name.' berhasil diaktifkan.'
            .' Kata sandi sementara: '.$hasil['password'].'.';

        if ($kirimEmail && $hasil['email_terkirim']) {
            $pesan .= ' Kredensial telah dikirim ke '.$pengguna->email.'.';
        } elseif ($kirimEmail) {
            $pesan .= ' Email gagal dikirim; sampaikan kata sandi secara langsung kepada pengguna.';
        }

        return back()->with('success', $pesan);
    }

    public function destroy(User $pengguna)
    {
        $this->pastikanBolehKelola($pengguna);

        if ($pengguna->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        if (AkunPengguna::superAdminTerakhir($pengguna)) {
            return back()->with('error', 'Super Admin terakhir tidak bisa dihapus. Tambahkan Super Admin lain terlebih dahulu.');
        }

        // Lepas tautan santri/siswa agar tidak menunjuk akun yang sudah dihapus.
        $pengguna->santri()->update(['user_id' => null]);
        $pengguna->students()->update(['user_id' => null]);

        $kategori = $this->kategoriRedirect($pengguna->peranSistem());
        $pengguna->delete();

        return redirect()->route('super.pengguna.index', $kategori)
            ->with('success', 'Pengguna '.$pengguna->name.' berhasil dihapus.');
    }

    /**
     * Tab kategori yang boleh dilihat pengguna aktif.
     *
     * @return array<string, array{label:string, roles:array<int,string>, ikon:string, warna:string, deskripsi:string}>
     */
    private function tabs(User $user): array
    {
        if (Akses::superAdmin($user)) {
            return [
                'semua' => [
                    'label' => 'Semua Pengguna',
                    'roles' => array_values(array_unique(array_merge(...array_column(self::KATEGORI, 'roles')))),
                    'ikon' => 'fa-users',
                    'warna' => 'slate',
                    'deskripsi' => 'Seluruh akun yang dapat masuk ke sistem.',
                ],
            ] + self::KATEGORI;
        }

        $legacy = RoleSistem::legacyDari(array_keys(AkunPengguna::roleBolehDibuat($user)));

        $tabs = [];

        foreach (self::KATEGORI as $key => $tab) {
            if (array_intersect($tab['roles'], $legacy)) {
                $tabs[$key] = $tab;
            }
        }

        return $tabs;
    }

    /**
     * Parameter tab kategori yang sesuai dengan peran akun yang baru diubah.
     * Pengelola non-Super Admin selalu kembali ke tab yang relevan.
     */
    private function kategoriRedirect(?string $role): array
    {
        $lama = RoleSistem::legacy($role);

        foreach (self::KATEGORI as $key => $tab) {
            if (in_array($lama, $tab['roles'], true)) {
                return ['kategori' => $key];
            }
        }

        return [];
    }

    /**
     * Data untuk form create/edit.
     * Pilihan peran mengikuti hak pengguna aktif (lihat AkunPengguna).
     */
    private function dataForm(User $user, ?User $pengguna = null): array
    {
        $bolehSemua = Akses::superAdmin($user);
        $sudahTertaut = $pengguna ? $pengguna->santri()->exists() : false;

        return [
            'roles' => AkunPengguna::roleBolehDibuat($user),
            'jenjangList' => self::jenjangOptions(),
            'jenjangTertaut' => Jenjang::normalisasi($pengguna?->jenjang),
            'santriList' => $this->santriOptions($pengguna),
            'santriTertaut' => $pengguna ? $pengguna->santri()->pluck('id')->all() : [],
            'wajibSantri' => ! $bolehSemua && ! $sudahTertaut,
        ];
    }

    /** Daftar santri yang boleh ditautkan ke akun wali. */
    private function santriOptions(?User $pengguna = null): array
    {
        $user = auth()->user();

        $query = Santri::query()->orderBy('nama_lengkap');

        if (Akses::dibatasiJenjang($user)) {
            $nilai = Akses::nilaiJenjangDb($user, Santri::class, 'jenjang_pendidikan') ?: ['__tidak_ada__'];
            $query->whereIn('jenjang_pendidikan', $nilai);
        }

        // Hanya santri tanpa wali, atau yang sudah tertaut ke akun ini.
        $query->where(function ($q) use ($pengguna) {
            $q->whereNull('user_id');
            if ($pengguna) {
                $q->orWhere('user_id', $pengguna->id);
            }
        });

        return $query->get(['id', 'nama_lengkap', 'jenjang_pendidikan', 'kelas'])
            ->mapWithKeys(function ($s) {
                $jenjang = Jenjang::label($s->jenjang_pendidikan) ?: 'Tanpa jenjang';
                $kelas = $s->kelas ?: 'tanpa kelas';

                return [$s->id => "{$s->nama_lengkap} — {$jenjang} — {$kelas}"];
            })
            ->all();
    }

    /**
     * Batasi query daftar sesuai peran yang sedang login.
     *
     * Admin Jenjang juga melihat akun wali pending yang emailnya terdaftar
     * pada PPDB jenjangnya (belum tertaut ke santri sampai diverifikasi).
     */
    private function queryTerlihat(User $user)
    {
        $query = User::query();

        if (Akses::superAdmin($user)) {
            return $query;
        }

        $query->whereIn('role', RoleSistem::legacyDari(array_keys(AkunPengguna::roleBolehDibuat($user))));

        if (Akses::dibatasiJenjang($user)) {
            $nilai = Akses::nilaiJenjangDb($user, Santri::class, 'jenjang_pendidikan') ?: [];
            $variasi = Ppdb::variasiJenjang($user->jenjangSlug());

            $query->where(function ($q) use ($nilai, $variasi) {
                if ($nilai !== []) {
                    $q->whereHas('santri', fn ($s) => $s->whereIn('jenjang_pendidikan', $nilai));
                }

                if ($variasi !== []) {
                    $q->orWhereHas('students', fn ($s) => $s->whereIn('jenjang', $variasi));
                }

                if ($variasi !== []) {
                    $q->orWhereIn('email', function ($sub) use ($variasi) {
                        $sub->select('parent_email')
                            ->from('pendaftaran_online')
                            ->whereNotNull('parent_email')
                            ->whereIn('jenjang_tujuan', $variasi);
                    });
                }
            });
        }

        return $query;
    }

    /** Tolak bila akun target tidak boleh dikelola oleh pengguna aktif. */
    private function pastikanBolehKelola(User $target): void
    {
        $user = auth()->user();

        if (Akses::superAdmin($user)) {
            return;
        }

        $peranTarget = $target->peranSistem();

        abort_unless(
            in_array($peranTarget, array_keys(AkunPengguna::roleBolehDibuat($user)), true),
            403,
            'Anda tidak berhak mengelola akun dengan peran ini.'
        );

        if (Akses::dibatasiJenjang($user)) {
            $nilai = Akses::nilaiJenjangDb($user, Santri::class, 'jenjang_pendidikan') ?: [];
            $variasi = Ppdb::variasiJenjang($user->jenjangSlug());

            $cocok = ($nilai !== [] && $target->santri()->whereIn('jenjang_pendidikan', $nilai)->exists())
                || ($variasi !== [] && $target->students()->whereIn('jenjang', $variasi)->exists())
                || ($variasi !== [] && PendaftaranOnline::whereIn('jenjang_tujuan', $variasi)
                    ->where('parent_email', $target->email)
                    ->exists());

            abort_unless(
                $cocok,
                403,
                'Akun ini bukan bagian dari jenjang yang Anda tangani.'
            );
        }
    }

    /** Tautkan satu santri ke akun wali. */
    private function tautkanSantri(User $pengguna, int $santriId): void
    {
        $santri = Santri::findOrFail($santriId);

        $santri->user_id = $pengguna->id;
        $santri->save();
    }

    /** Validasi bersama untuk store & update. */
    private function validasi(Request $request, ?User $pengguna = null): array
    {
        $user = auth()->user();
        $bolehSemua = Akses::superAdmin($user);
        $rolesBoleh = array_keys(AkunPengguna::roleBolehDibuat($user));

        abort_if($rolesBoleh === [], 403, 'Anda tidak berhak menambah atau mengubah pengguna.');

        // Normalisasi penulisan peran lama (mis. `admin`, `wali-santri`).
        if ($request->filled('role')) {
            $request->merge(['role' => RoleSistem::normalisasi($request->input('role'))]);
        }

        $aturanEmail = ['required', 'email', 'max:255'];

        $aturanEmail[] = $pengguna
            ? Rule::unique('users', 'email')->ignore($pengguna->id)
            : Rule::unique('users', 'email');

        $aturan = [
            'name' => ['required', 'string', 'max:255'],
            'email' => $aturanEmail,
            'role' => ['required', Rule::in($rolesBoleh)],
            'jenjang' => [
                'nullable',
                'string',
                Rule::requiredIf(fn () => $request->input('role') === 'admin-jenjang'),
                Rule::in(array_keys(self::jenjangOptions())),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'santri_id' => [
                'nullable',
                'integer',
                Rule::exists('santri', 'id'),
                function ($attribute, $value, $fail) use ($pengguna) {
                    $santri = Santri::find($value);

                    if (! $santri) {
                        return;
                    }

                    if ($santri->user_id && $santri->user_id !== $pengguna?->id) {
                        $fail('Santri ini sudah tertaut ke akun wali lain.');
                    }

                    $user = auth()->user();
                    if (Akses::dibatasiJenjang($user)
                        && ! Akses::cocokJenjang($user, $santri->jenjang_pendidikan)) {
                        $fail('Santri ini berada di luar jenjang yang Anda tangani.');
                    }
                },
            ],
        ];

        // Pengelola selain Super Admin wajib menautkan santri saat MEMBUAT
        // akun wali agar akun langsung terlihat pada daftar jenjangnya.
        if (! $bolehSemua
            && ! $pengguna
            && $request->input('role') === 'walisantri') {
            $aturan['santri_id'][] = 'required';
        }

        return $request->validate($aturan, [
            'role.in' => 'Peran yang dipilih tidak dikenali atau di luar hak akses Anda.',
            'jenjang.required' => 'Pilih jenjang yang ditangani akun Admin Jenjang.',
            'jenjang.in' => 'Jenjang yang dipilih tidak dikenali.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'santri_id.required' => 'Pilih santri yang akan ditautkan ke akun wali.',
        ]);
    }
}
