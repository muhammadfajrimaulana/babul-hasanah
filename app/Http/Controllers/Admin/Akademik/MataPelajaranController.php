<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Models\Subject;
use App\Support\Akademik;
use App\Support\Akses;
use App\Support\Jenjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        if (! Akademik::siap()) {
            return view('dashboard.akademik.belum-siap');
        }

        $user = $request->user();
        $jenjang = Akses::jenjangSlug($user)
            ?: Jenjang::normalisasi($request->query('jenjang'));

        $mapel = Akademik::mapelQuery($user)
            ->when($jenjang, fn ($q) => $q->where('jenjang', $jenjang))
            ->orderBy('jenjang')
            ->orderBy('nama')
            ->get();

        return view('dashboard.akademik.mapel.index', [
            'mapel' => $mapel,
            'jenjang' => $jenjang,
            'daftarJenjang' => $this->daftarJenjang($user),
            'bisaUbah' => $user->bisa('akademik.mapel') && ! Akses::adminTanpaJenjang($user),
        ]);
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.mapel'), 403);
        abort_if(
            Akses::adminTanpaJenjang($request->user()),
            403,
            'Akun admin ini belum memiliki jenjang. Hubungi Super Admin.'
        );

        return view('dashboard.akademik.mapel.form', [
            'item' => null,
            'jenjang' => Akses::jenjangSlug($request->user())
                ?: Jenjang::normalisasi($request->query('jenjang')),
            'daftarJenjang' => $this->daftarJenjang($request->user()),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.mapel'), 403);

        $data = $this->validasi($request);
        $mapel = MataPelajaran::create($data);
        $this->sinkronSubject($mapel);

        return redirect()
            ->route('admin.akademik.mapel.index', ['jenjang' => $data['jenjang']])
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(Request $request, MataPelajaran $mapel)
    {
        abort_unless($request->user()->bisa('akademik.mapel'), 403);
        $this->pastikanAkses($mapel);

        return view('dashboard.akademik.mapel.form', [
            'item' => $mapel,
            'jenjang' => $mapel->jenjang,
            'daftarJenjang' => $this->daftarJenjang($request->user()),
        ]);
    }

    public function update(Request $request, MataPelajaran $mapel)
    {
        abort_unless($request->user()->bisa('akademik.mapel'), 403);
        $this->pastikanAkses($mapel);

        $jenjangLama = $mapel->jenjang;
        $kodeLama = $mapel->kode;
        $mapel->update($this->validasi($request, $mapel));
        $this->sinkronSubject($mapel, $jenjangLama, $kodeLama);

        return redirect()
            ->route('admin.akademik.mapel.index', ['jenjang' => $mapel->jenjang])
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Request $request, MataPelajaran $mapel)
    {
        abort_unless($request->user()->bisa('akademik.mapel'), 403);
        $this->pastikanAkses($mapel);

        if ($mapel->nilai()->exists()) {
            return redirect()
                ->route('admin.akademik.mapel.index', ['jenjang' => $mapel->jenjang])
                ->with('error', 'Mata pelajaran sudah memiliki nilai. Nonaktifkan saja agar riwayat tetap utuh.');
        }

        $jenjang = $mapel->jenjang;
        $kode = $mapel->kode;
        $mapel->delete();

        if (Schema::hasTable('subjects')) {
            $subject = Subject::query()
                ->where('jenjang', $jenjang)
                ->where('kode', $kode)
                ->first();

            if ($subject && ! $subject->grades()->exists()) {
                $subject->delete();
            }
        }

        return redirect()
            ->route('admin.akademik.mapel.index', ['jenjang' => $jenjang])
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    /* ============================================================= Helper */

    private function validasi(Request $request, ?MataPelajaran $mapel = null): array
    {
        $user = $request->user();
        $slugAdmin = Akses::jenjangSlug($user);

        abort_if(
            Akses::adminTanpaJenjang($user),
            403,
            'Akun admin ini belum memiliki jenjang. Hubungi Super Admin.'
        );

        // Jenjang ditetapkan lebih dulu agar aturan unik kode selalu memakai
        // jenjang yang benar, termasuk saat update tanpa field jenjang.
        $jenjang = $slugAdmin
            ?: Jenjang::normalisasi($request->input('jenjang'))
            ?: $mapel?->jenjang;

        $data = $request->validate([
            'jenjang' => ['nullable', 'string', 'max:100'],
            'kode' => [
                'required', 'string', 'max:30',
                Rule::unique('mata_pelajaran', 'kode')
                    ->where(fn ($q) => $q->where('jenjang', $jenjang))
                    ->ignore($mapel?->id),
            ],
            'nama' => ['required', 'string', 'max:150'],
            'kelompok' => ['nullable', 'string', 'max:50'],
            'kkm' => ['required', 'integer', 'min:0', 'max:100'],
            'aktif' => ['nullable', 'boolean'],
        ], [
            'kode.unique' => 'Kode mata pelajaran sudah dipakai pada jenjang ini.',
            'kkm.min' => 'KKM minimal 0.',
            'kkm.max' => 'KKM maksimal 100.',
        ]);

        if (! $jenjang || ! Jenjang::cari($jenjang)) {
            throw ValidationException::withMessages(['jenjang' => 'Pilih jenjang yang valid.']);
        }

        abort_unless(Akses::cocokJenjang($user, $jenjang), 403);

        return [
            'jenjang' => $jenjang,
            'kode' => strtoupper(trim($data['kode'])),
            'nama' => trim($data['nama']),
            'kelompok' => trim((string) ($data['kelompok'] ?? '')) ?: null,
            'kkm' => (int) $data['kkm'],
            'aktif' => $request->boolean('aktif'),
        ];
    }

    private function pastikanAkses(MataPelajaran $mapel): void
    {
        abort_if(
            Akses::adminTanpaJenjang(auth()->user()),
            403,
            'Akun admin ini belum memiliki jenjang. Hubungi Super Admin.'
        );

        abort_unless(
            Akses::cocokJenjang(auth()->user(), $mapel->jenjang),
            403,
            'Mata pelajaran ini di luar jenjang yang Anda tangani.'
        );
    }

    /**
     * Cerminkan mata pelajaran lama ke tabel `subjects` agar Manajemen Nilai
     * Akademik (skema `grades`) memakai daftar mapel yang sama.
     */
    private function sinkronSubject(MataPelajaran $mapel, ?string $jenjangLama = null, ?string $kodeLama = null): void
    {
        if (! Schema::hasTable('subjects')) {
            return;
        }

        if ($jenjangLama && $kodeLama
            && ($jenjangLama !== $mapel->jenjang || $kodeLama !== $mapel->kode)) {
            Subject::query()
                ->where('jenjang', $jenjangLama)
                ->where('kode', $kodeLama)
                ->update([
                    'jenjang' => $mapel->jenjang,
                    'kode' => $mapel->kode,
                ]);
        }

        $subject = Subject::withTrashed()
            ->where('jenjang', $mapel->jenjang)
            ->where('kode', $mapel->kode)
            ->first() ?? new Subject;

        $subject->fill([
            'jenjang' => $mapel->jenjang,
            'kode' => $mapel->kode,
            'nama' => $mapel->nama,
            'kelompok' => $mapel->kelompok,
            'kkm' => $mapel->kkm,
        ]);

        if ($subject->trashed()) {
            $subject->restore();
        }

        $subject->deleted_at = null;
        $subject->save();
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
}
