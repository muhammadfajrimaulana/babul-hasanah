<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Kurikulum;
use App\Models\KurikulumItem;
use App\Support\Akademik;
use App\Support\Akses;
use App\Support\CatatanAktivitas;
use App\Support\Jenjang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * CRUD kurikulum dan item mata pelajarannya.
 *
 * Status `draft` tidak ditampilkan di portal wali; Admin Jenjang/Superadmin
 * dapat menerbitkan kurikulum setelah isi kurikulum diperiksa.
 */
class KurikulumController extends Controller
{
    public function index(Request $request)
    {
        if (! Akademik::siap() || ! $this->siap()) {
            return view('dashboard.akademik.belum-siap');
        }

        $user = $request->user();
        $slugAdmin = Akses::jenjangSlug($user);
        $jenjang = $slugAdmin
            ?: Jenjang::normalisasi($request->query('jenjang'));
        $tahun = trim((string) $request->query('tahun_ajaran')) ?: Akademik::tahunAjaranSekarang();
        $semester = strtolower((string) $request->query('semester')) ?: Akademik::semesterSekarang();

        if (! array_key_exists($semester, Akademik::SEMESTER)) {
            $semester = Akademik::semesterSekarang();
        }

        $query = Kurikulum::query()->withCount('item');

        if (Akses::adminTanpaJenjang($user)) {
            $query->whereRaw('1 = 0');
        } elseif ($slugAdmin) {
            $query->where('jenjang', $slugAdmin);
        } elseif ($jenjang) {
            $query->where('jenjang', $jenjang);
        }

        return view('dashboard.akademik.kurikulum.index', [
            'kurikulum' => $query
                ->where('tahun_ajaran', $tahun)
                ->where('semester', $semester)
                ->orderBy('nama')
                ->get(),
            'filter' => [
                'jenjang' => $jenjang,
                'tahun' => $tahun,
                'semester' => $semester,
            ],
            'daftarJenjang' => $this->daftarJenjang($user),
            'bisaUbah' => $this->bisaUbah($user),
        ]);
    }

    public function create(Request $request)
    {
        abort_unless($this->bisaUbah($request->user()), 403);
        $this->pastikanAdminBergenjang($request->user());

        return view('dashboard.akademik.kurikulum.form', [
            'item' => null,
            'jenjang' => Akses::jenjangSlug($request->user())
                ?: Jenjang::normalisasi($request->query('jenjang')),
            'daftarJenjang' => $this->daftarJenjang($request->user()),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($this->bisaUbah($request->user()), 403);

        $data = $this->validasi($request);
        $data['created_by'] = $request->user()->id;
        $data['status'] = 'draft';

        $kurikulum = Kurikulum::create($data);

        CatatanAktivitas::catat('kurikulum_dibuat', $kurikulum, $this->ringkasan($kurikulum));

        return redirect()
            ->route('admin.akademik.kurikulum.show', $kurikulum)
            ->with('success', 'Kurikulum berhasil dibuat. Tambahkan item mata pelajaran lalu terbitkan.');
    }

    public function show(Request $request, Kurikulum $kurikulum)
    {
        if (! Akademik::siap() || ! $this->siap()) {
            return view('dashboard.akademik.belum-siap');
        }

        abort_unless(
            $request->user()->bisa('akademik.lihat'),
            403
        );
        abort_unless(
            Akses::cocokJenjang($request->user(), $kurikulum->jenjang),
            403,
            'Kurikulum ini di luar jenjang yang Anda tangani.'
        );

        return view('dashboard.akademik.kurikulum.show', [
            'kurikulum' => $kurikulum->load('item.mataPelajaran', 'pembuat'),
            'daftarMapel' => $this->daftarMapel($request->user(), $kurikulum->jenjang, $kurikulum->item->pluck('mata_pelajaran_id')->all()),
            'bisaUbah' => $this->bisaUbah($request->user())
                && Akses::cocokJenjang($request->user(), $kurikulum->jenjang),
        ]);
    }

    public function edit(Request $request, Kurikulum $kurikulum)
    {
        abort_unless($this->bisaUbah($request->user()), 403);
        $this->pastikanAkses($request->user(), $kurikulum);

        return view('dashboard.akademik.kurikulum.form', [
            'item' => $kurikulum,
            'jenjang' => $kurikulum->jenjang,
            'daftarJenjang' => $this->daftarJenjang($request->user()),
        ]);
    }

    public function update(Request $request, Kurikulum $kurikulum)
    {
        abort_unless($this->bisaUbah($request->user()), 403);
        $this->pastikanAkses($request->user(), $kurikulum);

        $kurikulum->update($this->validasi($request, $kurikulum));

        CatatanAktivitas::catat('kurikulum_diubah', $kurikulum, $this->ringkasan($kurikulum));

        return redirect()
            ->route('admin.akademik.kurikulum.show', $kurikulum)
            ->with('success', 'Kurikulum berhasil diperbarui.');
    }

    public function destroy(Request $request, Kurikulum $kurikulum)
    {
        abort_unless($this->bisaUbah($request->user()), 403);
        $this->pastikanAkses($request->user(), $kurikulum);

        $ringkasan = $this->ringkasan($kurikulum);
        $kurikulum->delete();

        CatatanAktivitas::catat('kurikulum_dihapus', null, $ringkasan);

        return redirect()
            ->route('admin.akademik.kurikulum.index')
            ->with('success', 'Kurikulum dan seluruh itemnya berhasil dihapus.');
    }

    public function terbitkan(Request $request, Kurikulum $kurikulum)
    {
        abort_unless($request->user()->bisa('akademik.terbit'), 403);
        $this->pastikanAkses($request->user(), $kurikulum);

        if ($kurikulum->item()->count() === 0) {
            return redirect()
                ->route('admin.akademik.kurikulum.show', $kurikulum)
                ->with('error', 'Tambahkan minimal satu item mata pelajaran sebelum menerbitkan kurikulum.');
        }

        $kurikulum->update(['status' => 'published']);

        CatatanAktivitas::catat(
            'kurikulum_diterbitkan',
            $kurikulum,
            $this->ringkasan($kurikulum),
            null
        );

        return redirect()
            ->route('admin.akademik.kurikulum.show', $kurikulum)
            ->with('success', 'Kurikulum berhasil diterbitkan dan tampil di portal wali.');
    }

    /* ==================================================== Item kurikulum */

    public function itemStore(Request $request, Kurikulum $kurikulum)
    {
        abort_unless($this->bisaUbah($request->user()), 403);
        $this->pastikanAkses($request->user(), $kurikulum);

        $data = $this->validasiItem($request, $kurikulum);

        if ($kurikulum->item()->where('mata_pelajaran_id', $data['mata_pelajaran_id'])->exists()) {
            throw ValidationException::withMessages([
                'mata_pelajaran_id' => 'Mata pelajaran ini sudah ada di kurikulum.',
            ]);
        }

        $item = $kurikulum->item()->create($data);

        CatatanAktivitas::catat('kurikulum_item_ditambah', $item, [
            'kurikulum_id' => $kurikulum->id,
            'mata_pelajaran_id' => $item->mata_pelajaran_id,
        ]);

        return redirect()
            ->route('admin.akademik.kurikulum.show', $kurikulum)
            ->with('success', 'Item kurikulum berhasil ditambahkan.');
    }

    public function itemUpdate(Request $request, Kurikulum $kurikulum, KurikulumItem $item)
    {
        abort_unless($this->bisaUbah($request->user()), 403);
        $this->pastikanAkses($request->user(), $kurikulum);

        abort_unless($item->kurikulum_id === $kurikulum->id, 404);

        $item->update($this->validasiItem($request, $kurikulum, $item));

        CatatanAktivitas::catat('kurikulum_item_diubah', $item, [
            'kurikulum_id' => $kurikulum->id,
            'mata_pelajaran_id' => $item->mata_pelajaran_id,
        ]);

        return redirect()
            ->route('admin.akademik.kurikulum.show', $kurikulum)
            ->with('success', 'Item kurikulum berhasil diperbarui.');
    }

    public function itemDestroy(Request $request, Kurikulum $kurikulum, KurikulumItem $item)
    {
        abort_unless($this->bisaUbah($request->user()), 403);
        $this->pastikanAkses($request->user(), $kurikulum);

        abort_unless($item->kurikulum_id === $kurikulum->id, 404);

        $data = [
            'kurikulum_id' => $kurikulum->id,
            'mata_pelajaran_id' => $item->mata_pelajaran_id,
        ];
        $item->delete();

        CatatanAktivitas::catat('kurikulum_item_dihapus', null, $data);

        return redirect()
            ->route('admin.akademik.kurikulum.show', $kurikulum)
            ->with('success', 'Item kurikulum berhasil dihapus.');
    }

    /* ============================================================= Helper */

    private function siap(): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasTable('kurikulum')
                && \Illuminate\Support\Facades\Schema::hasTable('kurikulum_item');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function bisaUbah($user): bool
    {
        return (bool) $user?->bisa('akademik.ubah');
    }

    private function pastikanAdminBergenjang($user): void
    {
        abort_if(
            Akses::adminTanpaJenjang($user),
            403,
            'Akun admin ini belum memiliki jenjang. Hubungi Super Admin.'
        );
    }

    private function pastikanAkses($user, Kurikulum $kurikulum): void
    {
        $this->pastikanAdminBergenjang($user);

        abort_unless(
            Akses::cocokJenjang($user, $kurikulum->jenjang),
            403,
            'Kurikulum ini di luar jenjang yang Anda tangani.'
        );
    }

    private function validasi(Request $request, ?Kurikulum $kurikulum = null): array
    {
        $user = $request->user();
        $this->pastikanAdminBergenjang($user);

        $slugAdmin = Akses::jenjangSlug($user);
        $jenjang = $slugAdmin
            ?: Jenjang::normalisasi($request->input('jenjang'))
            ?: $kurikulum?->jenjang;

        if (! $jenjang || ! Jenjang::cari($jenjang)) {
            throw ValidationException::withMessages(['jenjang' => 'Pilih jenjang yang valid.']);
        }

        abort_unless(Akses::cocokJenjang($user, $jenjang), 403);

        $data = $request->validate([
            'tahun_ajaran' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', Rule::in(array_keys(Akademik::SEMESTER))],
            'nama' => [
                'required', 'string', 'max:150',
                Rule::unique('kurikulum', 'nama')
                    ->where(fn ($q) => $q
                        ->where('jenjang', $jenjang)
                        ->where('tahun_ajaran', $request->input('tahun_ajaran'))
                        ->where('semester', $request->input('semester')))
                    ->ignore($kurikulum?->id),
            ],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
        ], [
            'nama.unique' => 'Nama kurikulum sudah dipakai pada jenjang, tahun, dan semester ini.',
        ]);

        return [
            'jenjang' => $jenjang,
            'tahun_ajaran' => $data['tahun_ajaran'],
            'semester' => $data['semester'],
            'nama' => trim($data['nama']),
            'deskripsi' => trim((string) ($data['deskripsi'] ?? '')) ?: null,
        ];
    }

    private function validasiItem(Request $request, Kurikulum $kurikulum, ?KurikulumItem $item = null): array
    {
        $data = $request->validate([
            'mata_pelajaran_id' => ['required', 'integer', Rule::exists('mata_pelajaran', 'id')],
            'kkm' => ['required', 'integer', 'min:0', 'max:100'],
            'target_kd' => ['nullable', 'string', 'max:1000'],
            'urutan' => ['nullable', 'integer', 'min:0', 'max:999'],
        ], [
            'mata_pelajaran_id.exists' => 'Mata pelajaran yang dipilih tidak terdaftar.',
        ]);

        $mapelOk = Akademik::mapelQuery($request->user(), $kurikulum->jenjang)
            ->where('id', $data['mata_pelajaran_id'])
            ->exists();

        abort_unless($mapelOk, 403, 'Mata pelajaran ini di luar jenjang yang Anda tangani.');

        return [
            'mata_pelajaran_id' => (int) $data['mata_pelajaran_id'],
            'kkm' => (int) $data['kkm'],
            'target_kd' => trim((string) ($data['target_kd'] ?? '')) ?: null,
            'urutan' => (int) ($data['urutan'] ?? 0),
        ];
    }

    /** @return array<string, mixed> */
    private function ringkasan(Kurikulum $kurikulum): array
    {
        return [
            'jenjang' => $kurikulum->jenjang,
            'tahun_ajaran' => $kurikulum->tahun_ajaran,
            'semester' => $kurikulum->semester,
            'nama' => $kurikulum->nama,
            'status' => $kurikulum->status,
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

    private function daftarMapel($user, ?string $jenjang, array $kecualikan = [])
    {
        return Akademik::mapelQuery($user, $jenjang)
            ->where('aktif', true)
            ->when($kecualikan, fn ($q) => $q->whereNotIn('id', $kecualikan))
            ->orderBy('nama')
            ->get(['id', 'nama', 'kkm']);
    }
}
