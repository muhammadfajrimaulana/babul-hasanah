<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Support\Akademik;
use App\Support\Akses;
use App\Support\CatatanAktivitas;
use App\Support\Jenjang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * CRUD jadwal pelajaran.
 *
 * Jadwal mengikuti batas jenjang yang sama dengan nilai: Admin Jenjang hanya
 * mengelola jenjangnya, Superadmin/Pengurus/Pembina bisa memilih jenjang.
 */
class JadwalController extends Controller
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
        $kelas = trim((string) $request->query('kelas')) ?: null;
        $tahun = trim((string) $request->query('tahun_ajaran')) ?: Akademik::tahunAjaranSekarang();
        $semester = strtolower((string) $request->query('semester')) ?: Akademik::semesterSekarang();

        if (! array_key_exists($semester, Akademik::SEMESTER)) {
            $semester = Akademik::semesterSekarang();
        }

        $query = JadwalPelajaran::query()->with(['mataPelajaran', 'guru']);

        if (Akses::adminTanpaJenjang($user)) {
            $query->whereRaw('1 = 0');
        } elseif ($slugAdmin) {
            $query->where('jenjang', $slugAdmin);
        } elseif ($jenjang) {
            $query->where('jenjang', $jenjang);
        }

        if ($kelas) {
            $query->where('kelas', $kelas);
        }

        $jadwal = $query
            ->where('tahun_ajaran', $tahun)
            ->where('semester', $semester)
            ->get()
            ->sortBy(fn ($row) => [
                array_search($row->hari, JadwalPelajaran::HARI, true) ?: 99,
                (string) $row->jam_mulai,
            ])
            ->values();

        return view('dashboard.akademik.jadwal.index', [
            'jadwal' => $jadwal,
            'filter' => [
                'jenjang' => $jenjang,
                'kelas' => $kelas,
                'tahun' => $tahun,
                'semester' => $semester,
            ],
            'daftarJenjang' => $this->daftarJenjang($user),
            'daftarKelas' => Akademik::daftarKelas($user, $jenjang),
            'bisaUbah' => $this->bisaUbah($user),
        ]);
    }

    public function create(Request $request)
    {
        abort_unless($this->bisaUbah($request->user()), 403);
        $this->pastikanAdminBergenjang($request->user());

        $jenjang = Akses::jenjangSlug($request->user())
            ?: Jenjang::normalisasi($request->query('jenjang'));

        return view('dashboard.akademik.jadwal.form', [
            'item' => null,
            'jenjang' => $jenjang,
            'daftarJenjang' => $this->daftarJenjang($request->user()),
            'daftarKelas' => Akademik::daftarKelas($request->user(), $jenjang),
            'daftarMapel' => $this->daftarMapel($request->user(), $jenjang),
            'daftarGuru' => $this->daftarGuru(),
            'hari' => JadwalPelajaran::HARI,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($this->bisaUbah($request->user()), 403);

        $data = $this->validasi($request);

        if ($bentrok = $this->bentrok($data)) {
            throw ValidationException::withMessages(['jam_mulai' => $bentrok]);
        }

        $data['created_by'] = $request->user()->id;
        $jadwal = JadwalPelajaran::create($data);

        CatatanAktivitas::catat('jadwal_dibuat', $jadwal, $this->ringkasan($jadwal));

        return redirect()
            ->route('admin.akademik.jadwal.index', $this->queryIndex($jadwal))
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function edit(Request $request, JadwalPelajaran $jadwal)
    {
        abort_unless($this->bisaUbah($request->user()), 403);
        $this->pastikanAkses($request->user(), $jadwal);

        return view('dashboard.akademik.jadwal.form', [
            'item' => $jadwal,
            'jenjang' => $jadwal->jenjang,
            'daftarJenjang' => $this->daftarJenjang($request->user()),
            'daftarKelas' => Akademik::daftarKelas($request->user(), $jadwal->jenjang),
            'daftarMapel' => $this->daftarMapel($request->user(), $jadwal->jenjang),
            'daftarGuru' => $this->daftarGuru(),
            'hari' => JadwalPelajaran::HARI,
        ]);
    }

    public function update(Request $request, JadwalPelajaran $jadwal)
    {
        abort_unless($this->bisaUbah($request->user()), 403);
        $this->pastikanAkses($request->user(), $jadwal);

        $data = $this->validasi($request, $jadwal);

        if ($bentrok = $this->bentrok($data, $jadwal->id)) {
            throw ValidationException::withMessages(['jam_mulai' => $bentrok]);
        }

        $jadwal->update($data);

        CatatanAktivitas::catat('jadwal_diubah', $jadwal, $this->ringkasan($jadwal));

        return redirect()
            ->route('admin.akademik.jadwal.index', $this->queryIndex($jadwal))
            ->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy(Request $request, JadwalPelajaran $jadwal)
    {
        abort_unless($this->bisaUbah($request->user()), 403);
        $this->pastikanAkses($request->user(), $jadwal);

        $ringkasan = $this->ringkasan($jadwal);
        $query = $this->queryIndex($jadwal);
        $jadwal->delete();

        CatatanAktivitas::catat('jadwal_dihapus', null, $ringkasan);

        return redirect()
            ->route('admin.akademik.jadwal.index', $query)
            ->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }

    /* ============================================================= Helper */

    private function siap(): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasTable('jadwal_pelajaran');
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

    private function pastikanAkses($user, JadwalPelajaran $jadwal): void
    {
        $this->pastikanAdminBergenjang($user);

        abort_unless(
            Akses::cocokJenjang($user, $jadwal->jenjang),
            403,
            'Jadwal ini di luar jenjang yang Anda tangani.'
        );
    }

    private function validasi(Request $request, ?JadwalPelajaran $jadwal = null): array
    {
        $user = $request->user();
        $this->pastikanAdminBergenjang($user);

        $slugAdmin = Akses::jenjangSlug($user);
        $jenjang = $slugAdmin
            ?: Jenjang::normalisasi($request->input('jenjang'))
            ?: $jadwal?->jenjang;

        if (! $jenjang || ! Jenjang::cari($jenjang)) {
            throw ValidationException::withMessages(['jenjang' => 'Pilih jenjang yang valid.']);
        }

        abort_unless(Akses::cocokJenjang($user, $jenjang), 403);

        $data = $request->validate([
            'kelas' => ['required', 'string', 'max:50'],
            'tahun_ajaran' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', Rule::in(array_keys(Akademik::SEMESTER))],
            'hari' => ['required', Rule::in(JadwalPelajaran::HARI)],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i'],
            'mata_pelajaran_id' => ['required', 'integer', Rule::exists('mata_pelajaran', 'id')],
            'guru_id' => ['nullable', 'integer', Rule::exists('guru', 'id')],
            'ruang' => ['nullable', 'string', 'max:50'],
            'urutan' => ['nullable', 'integer', 'min:0', 'max:99'],
        ], [
            'jam_mulai.date_format' => 'Format jam mulai harus HH:MM.',
            'jam_selesai.date_format' => 'Format jam selesai harus HH:MM.',
            'mata_pelajaran_id.exists' => 'Mata pelajaran yang dipilih tidak terdaftar.',
            'guru_id.exists' => 'Guru yang dipilih tidak terdaftar.',
        ]);

        if (strcmp((string) $data['jam_selesai'], (string) $data['jam_mulai']) <= 0) {
            throw ValidationException::withMessages([
                'jam_selesai' => 'Jam selesai harus lebih lambat dari jam mulai.',
            ]);
        }

        $mapelOk = Akademik::mapelQuery($user, $jenjang)
            ->where('id', $data['mata_pelajaran_id'])
            ->exists();

        abort_unless($mapelOk, 403, 'Mata pelajaran ini di luar jenjang yang Anda tangani.');

        return [
            'jenjang' => $jenjang,
            'kelas' => trim($data['kelas']),
            'tahun_ajaran' => $data['tahun_ajaran'],
            'semester' => $data['semester'],
            'hari' => $data['hari'],
            // Kolom bertipe TIME menyimpan HH:MM:SS; Form hanya mengirim HH:MM.
            'jam_mulai' => $data['jam_mulai'].':00',
            'jam_selesai' => $data['jam_selesai'].':00',
            'mata_pelajaran_id' => (int) $data['mata_pelajaran_id'],
            'guru_id' => ! empty($data['guru_id']) ? (int) $data['guru_id'] : null,
            'ruang' => trim((string) ($data['ruang'] ?? '')) ?: null,
            'urutan' => (int) ($data['urutan'] ?? 0),
        ];
    }

    /** Cek bentrok jam kelas dan bentrok jam guru pengampu. */
    private function bentrok(array $data, ?int $ignoreId = null): ?string
    {
        $dasar = JadwalPelajaran::query()
            ->where('jenjang', $data['jenjang'])
            ->where('kelas', $data['kelas'])
            ->where('tahun_ajaran', $data['tahun_ajaran'])
            ->where('semester', $data['semester'])
            ->where('hari', $data['hari'])
            ->where('jam_mulai', '<', $data['jam_selesai'])
            ->where('jam_selesai', '>', $data['jam_mulai'])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId));

        if ($dasar->exists()) {
            return 'Kelas ini sudah memiliki jadwal pada jam tersebut. Pilih jam lain atau ubah jadwal yang sudah ada.';
        }

        if (! empty($data['guru_id'])) {
            $guruBentrok = JadwalPelajaran::query()
                ->where('guru_id', $data['guru_id'])
                ->where('tahun_ajaran', $data['tahun_ajaran'])
                ->where('semester', $data['semester'])
                ->where('hari', $data['hari'])
                ->where('jam_mulai', '<', $data['jam_selesai'])
                ->where('jam_selesai', '>', $data['jam_mulai'])
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists();

            if ($guruBentrok) {
                return 'Guru pengampu sudah mengajar pada jam tersebut. Pilih guru lain atau ubah jam.';
            }
        }

        return null;
    }

    /** @return array<string, mixed> */
    private function ringkasan(JadwalPelajaran $jadwal): array
    {
        return [
            'jenjang' => $jadwal->jenjang,
            'kelas' => $jadwal->kelas,
            'hari' => $jadwal->hari,
            'jam' => $jadwal->labelWaktu(),
            'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
        ];
    }

    /** @return array<string, string|null> */
    private function queryIndex(JadwalPelajaran $jadwal): array
    {
        return [
            'jenjang' => $jadwal->jenjang,
            'kelas' => $jadwal->kelas,
            'tahun_ajaran' => $jadwal->tahun_ajaran,
            'semester' => $jadwal->semester,
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

    private function daftarMapel($user, ?string $jenjang)
    {
        return Akademik::mapelQuery($user, $jenjang)
            ->where('aktif', true)
            ->orderBy('nama')
            ->get(['id', 'jenjang', 'nama']);
    }

    private function daftarGuru()
    {
        return Guru::query()->where('aktif', true)->orderBy('nama')->get(['id', 'nama']);
    }
}
