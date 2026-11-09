<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\User;
use App\Support\Akses;
use App\Support\Jenjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Kelola akun dashboard.
 *
 * Pembagian hak:
 * - Super Admin  : semua akun (Super Admin, Admin Jenjang, Pengasuh, Pembina, Wali Santri).
 * - Admin Jenjang: hanya akun Wali Santri yang terhubung ke santri di jenjangnya.
 * - Pengasuh & Pembina: hanya akun Wali Santri (seluruh pesantren).
 *
 * Pengaman tambahan:
 * - Super Admin terakhir tidak boleh diturunkan/dihapus.
 * - Akun sendiri tidak boleh dihapus atau diturunkan perannya.
 * - Jenjang wajib diisi untuk role "admin" agar dashboard-nya terarah.
 * - Akun wali bisa ditautkan ke satu santri sekaligus (santri.user_id).
 */
class PenggunaController extends Controller
{
    /** Label role (sumber tunggal: App\Support\Akses). */
    public const ROLES = Akses::ROLE_LABELS;

    /**
     * Kategori tab pada halaman daftar pengguna.
     * Urutan array = urutan tab yang tampil.
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
            'deskripsi' => 'Mengelola data operasional pada jenjang masing-masing.',
        ],
        'pengasuh' => [
            'label' => 'Pengurus & Pembina',
            'roles' => ['pengasuh', 'pembina'],
            'ikon' => 'fa-mosque',
            'warna' => 'amber',
            'deskripsi' => 'Monitoring pesantren; hanya boleh mengelola akun wali santri.',
        ],
        'wali_santri' => [
            'label' => 'Wali Santri',
            'roles' => ['wali_santri'],
            'ikon' => 'fa-people-roof',
            'warna' => 'sky',
            'deskripsi' => 'Hanya memantau data anak yang dikirim oleh Admin Jenjang.',
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

        // Tab yang boleh dilihat sesuai peran.
        $tabs = [];
        if ($bolehSemua) {
            $tabs['semua'] = [
                'label' => 'Semua Pengguna',
                'roles' => array_keys(self::ROLES),
                'ikon' => 'fa-users',
                'warna' => 'slate',
                'deskripsi' => 'Seluruh akun yang dapat masuk ke sistem.',
            ];
        }
        $tabs += $bolehSemua ? self::KATEGORI : ['wali_santri' => self::KATEGORI['wali_santri']];

        $kategoriAktif = (string) $request->kategori;
        if (! array_key_exists($kategoriAktif, $tabs)) {
            $kategoriAktif = $bolehSemua ? 'semua' : 'wali_santri';
        }

        $dasar = $this->queryTerlihat($user)
            ->when($request->q, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            }))
            ->when($request->jenjang, fn ($q, $j) => $q->where('jenjang', $j));

        // Jumlah per tab (mengikuti pencarian/filter jenjang yang aktif).
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

        return view('dashboard.pengguna.index', [
            'penggunas' => $penggunas,
            'roles' => self::ROLES,
            'jenjangList' => self::jenjangOptions(),
            'tabs' => $tabs,
            'hitung' => $hitung,
            'kategoriAktif' => $kategoriAktif,
            'bolehSemua' => $bolehSemua,
        ]);
    }

    public function create()
    {
        $user = auth()->user();

        return view('dashboard.pengguna.create', $this->dataForm($user));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $this->validasi($request);

        // Peran non-Super Admin dipaksa menjadi wali santri.
        if (! Akses::superAdmin($user)) {
            $data['role'] = 'wali_santri';
        }

        $santriId = $data['santri_id'] ?? null;
        unset($data['santri_id']);

        $data['password'] = Hash::make($data['password']);
        $data['jenjang'] = $data['role'] === 'admin' ? $data['jenjang'] : null;

        $pengguna = User::create($data);

        if ($santriId) {
            $this->tautkanSantri($pengguna, (int) $santriId);
        }

        return redirect()->route('super.pengguna.index', $this->kategoriRedirect($pengguna->role))
            ->with('success', 'Pengguna berhasil ditambahkan.');
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

        // Non-Super Admin tidak boleh mengubah peran wali menjadi peran lain.
        if (! Akses::superAdmin($user)) {
            $data['role'] = 'wali_santri';
        }

        // Akun sendiri tidak boleh diturunkan perannya (mencegah lockout).
        if ($pengguna->id === $user->id && $data['role'] !== 'super_admin') {
            return back()->withErrors(['role' => 'Anda tidak bisa mengubah peran akun sendiri.'])->withInput();
        }

        // Jangan turunkan peran Super Admin terakhir.
        if ($pengguna->role === 'super_admin' && $data['role'] !== 'super_admin'
            && User::where('role', 'super_admin')->count() <= 1) {
            return back()->withErrors(['role' => 'Tidak bisa mengubah peran Super Admin terakhir.'])->withInput();
        }

        $santriId = $data['santri_id'] ?? null;
        unset($data['santri_id']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['jenjang'] = $data['role'] === 'admin' ? ($data['jenjang'] ?? null) : null;

        // Jika peran berubah dari wali santri, lepaskan tautan santri.
        if ($pengguna->role === 'wali_santri' && $data['role'] !== 'wali_santri') {
            $pengguna->santri()->update(['user_id' => null]);
        }

        $pengguna->update($data);

        if ($santriId) {
            // Satu akun wali dipantau untuk satu santri: lepaskan tautan lama
            // yang berbeda sebelum menautkan pilihan baru.
            $pengguna->santri()->where('id', '!=', (int) $santriId)->update(['user_id' => null]);

            $this->tautkanSantri($pengguna, (int) $santriId);
        }

        return redirect()->route('super.pengguna.index', $this->kategoriRedirect($pengguna->role))
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $pengguna)
    {
        $this->pastikanBolehKelola($pengguna);

        if ($pengguna->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        if ($pengguna->role === 'super_admin' && User::where('role', 'super_admin')->count() <= 1) {
            return back()->with('error', 'Super Admin terakhir tidak bisa dihapus.');
        }

        // Lepaskan tautan santri agar tidak menunjuk akun yang sudah dihapus.
        $pengguna->santri()->update(['user_id' => null]);

        $role = $pengguna->role;
        $pengguna->delete();

        return redirect()->route('super.pengguna.index', $this->kategoriRedirect($role))
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Parameter tab kategori yang sesuai dengan peran akun yang baru diubah.
     * Pengelola non-Super Admin selalu kembali ke tab wali santri.
     */
    private function kategoriRedirect(?string $role): array
    {
        if (! Akses::superAdmin(auth()->user())) {
            return ['kategori' => 'wali_santri'];
        }

        foreach (self::KATEGORI as $key => $tab) {
            if (in_array($role, $tab['roles'], true)) {
                return ['kategori' => $key];
            }
        }

        return [];
    }

    /**
     * Data untuk form create/edit.
     * Non-Super Admin hanya mendapat pilihan peran Wali Santri.
     */
    private function dataForm(User $user, ?User $pengguna = null): array
    {
        $bolehSemua = Akses::superAdmin($user);
        $sudahTertaut = $pengguna ? $pengguna->santri()->exists() : false;

        $roles = $bolehSemua
            ? self::ROLES
            : ['wali_santri' => self::ROLES['wali_santri']];

        return [
            'roles' => $roles,
            'jenjangList' => self::jenjangOptions(),
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

        if (Akses::normalisasi($user?->role) === 'admin') {
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

    /** Batasi query daftar sesuai peran yang sedang login. */
    private function queryTerlihat(User $user)
    {
        $query = User::query();

        if (Akses::superAdmin($user)) {
            return $query;
        }

        $query->where('role', 'wali_santri');

        if (Akses::normalisasi($user->role) === 'admin') {
            $nilai = Akses::nilaiJenjangDb($user, Santri::class, 'jenjang_pendidikan') ?: ['__tidak_ada__'];

            $query->whereHas('santri', fn ($q) => $q->whereIn('jenjang_pendidikan', $nilai));
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

        abort_unless(
            $target->role === 'wali_santri',
            403,
            'Anda hanya boleh mengelola akun wali santri.'
        );

        if (Akses::normalisasi($user->role) === 'admin') {
            $nilai = Akses::nilaiJenjangDb($user, Santri::class, 'jenjang_pendidikan') ?: ['__tidak_ada__'];

            $cocok = $target->santri()->whereIn('jenjang_pendidikan', $nilai)->exists();

            abort_unless(
                $cocok,
                403,
                'Akun wali ini bukan bagian dari jenjang yang Anda tangani.'
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
        $aturanEmail = ['required', 'email', 'max:255'];

        $aturanEmail[] = $pengguna
            ? Rule::unique('users', 'email')->ignore($pengguna->id)
            : Rule::unique('users', 'email');

        $bolehSemua = Akses::superAdmin(auth()->user());
        $rolesDiizinkan = $bolehSemua ? array_keys(self::ROLES) : ['wali_santri'];

        $aturan = [
            'name' => ['required', 'string', 'max:255'],
            'email' => $aturanEmail,
            'role' => ['required', Rule::in($rolesDiizinkan)],
            'jenjang' => [
                'nullable',
                'required_if:role,admin',
                Rule::in(array_keys(self::jenjangOptions())),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => [$pengguna ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
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
                    if (Akses::normalisasi($user?->role) === 'admin'
                        && ! Akses::cocokJenjang($user, $santri->jenjang_pendidikan)) {
                        $fail('Santri ini berada di luar jenjang yang Anda tangani.');
                    }
                },
            ],
        ];

        // Pengelola selain Super Admin wajib menautkan santri agar akun wali
        // langsung terlihat pada daftar jenjangnya. Saat edit, akun yang sudah
        // punya santri tidak perlu memilih ulang.
        if (! $bolehSemua) {
            $sudahTertaut = $pengguna && $pengguna->santri()->exists();

            if (! $sudahTertaut) {
                $aturan['santri_id'][] = 'required';
            }
        }

        return $request->validate($aturan, [
            'jenjang.required_if' => 'Pilih jenjang untuk akun Admin Jenjang.',
            'jenjang.in' => 'Jenjang yang dipilih tidak dikenali.',
            'santri_id.required' => 'Pilih santri yang akan ditautkan ke akun wali.',
        ]);
    }
}
