<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Models\NilaiSantri;
use App\Support\Akademik;
use App\Support\Akses;
use App\Support\Jenjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class NilaiController extends Controller
{
    /* ============================================================ Daftar */

    public function index(Request $request)
    {
        if (! Akademik::siap()) {
            return view('dashboard.akademik.belum-siap');
        }

        $user = $request->user();
        $filter = $this->filter($request);
        $santris = collect();
        $nilai = collect();
        $ringkasan = $this->ringkasanKosong();

        if ($filter['lengkap']) {
            $santris = Akademik::santriQuery($user, $filter['jenjang'])
                ->where('kelas', $filter['kelas'])
                ->orderBy('nama_lengkap')
                ->get();

            $nilai = NilaiSantri::query()
                ->whereIn('santri_id', $santris->pluck('id'))
                ->konteks(
                    $filter['mapel']->id,
                    $filter['tahun'],
                    $filter['semester'],
                    $filter['kategori']
                )
                ->get()
                ->keyBy('santri_id');

            $ringkasan = [
                'total' => $santris->count(),
                'terisi' => $nilai->count(),
                'draft' => $nilai->where('status', 'draft')->count(),
                'terbit' => $nilai->where('status', 'published')->count(),
                'rata' => $nilai->avg('nilai'),
            ];
        }

        return view('dashboard.akademik.nilai.index', [
            'filter' => $filter,
            'santris' => $santris,
            'nilai' => $nilai,
            'ringkasan' => $ringkasan,
            'daftarKelas' => Akademik::daftarKelas($user, $filter['jenjang']),
            'daftarMapel' => $this->daftarMapel($user, $filter['jenjang']),
            'daftarJenjang' => $this->daftarJenjang($user),
            'tahunPilihan' => Akademik::tahunAjaranPilihan(),
        ]);
    }

    /* ======================================================= Input manual */

    public function store(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.ubah'), 403);

        $konteks = $this->konteks($request);
        $user = $request->user();

        $data = $request->validate([
            'nilai' => ['array'],
            'nilai.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'catatan' => ['array'],
            'catatan.*' => ['nullable', 'string', 'max:255'],
        ], [
            'nilai.*.numeric' => 'Nilai harus berupa angka.',
            'nilai.*.min' => 'Nilai minimal 0.',
            'nilai.*.max' => 'Nilai maksimal 100.',
        ]);

        $idSantri = Akademik::santriQuery($user, $konteks['jenjang'])
            ->where('kelas', $konteks['kelas'])
            ->pluck('id')
            ->all();

        $jumlah = 0;
        $dikembalikan = 0;

        DB::transaction(function () use ($data, $konteks, $idSantri, $user, &$jumlah, &$dikembalikan) {
            foreach ($idSantri as $santriId) {
                $nilaiBaru = $data['nilai'][$santriId] ?? null;
                $catatanBaru = trim((string) ($data['catatan'][$santriId] ?? '')) ?: null;

                $kunci = [
                    'santri_id' => $santriId,
                    'mata_pelajaran_id' => $konteks['mapel']->id,
                    'tahun_ajaran' => $konteks['tahun'],
                    'semester' => $konteks['semester'],
                    'kategori' => $konteks['kategori'],
                ];

                if ($nilaiBaru === null || $nilaiBaru === '') {
                    // Nilai draft yang dikosongkan dianggap dibatalkan.
                    NilaiSantri::where($kunci)->where('status', 'draft')->delete();

                    continue;
                }

                $row = NilaiSantri::firstOrNew($kunci);
                $baru = ! $row->exists;
                $berubah = $baru
                    || abs(((float) $row->nilai) - ((float) $nilaiBaru)) > 0.001
                    || (string) $row->catatan !== (string) $catatanBaru;

                if ($berubah && ! $baru && $row->status === 'published') {
                    $dikembalikan++;
                }

                $row->fill([
                    'jenjang' => $konteks['jenjang'],
                    'kelas' => $konteks['kelas'],
                    'nilai' => $nilaiBaru,
                    'catatan' => $catatanBaru,
                    'diperbarui_oleh' => $user->id,
                ]);

                if ($baru) {
                    $row->created_by = $user->id;
                }

                if ($berubah) {
                    $row->status = 'draft';
                    $row->published_at = null;
                    $row->diterbitkan_oleh = null;
                }

                $row->save();
                $jumlah++;
            }
        });

        $pesan = $jumlah.' nilai disimpan sebagai draft.';
        if ($dikembalikan > 0) {
            $pesan .= ' '.$dikembalikan.' nilai yang sudah terbit dikembalikan ke draft karena diubah.';
        }

        return redirect()
            ->route('admin.akademik.nilai.index', $this->queryKonteks($konteks))
            ->with('success', $pesan);
    }

    /* ==================================================== Unggah file CSV */

    public function upload(Request $request)
    {
        if (! Akademik::siap()) {
            return view('dashboard.akademik.belum-siap');
        }

        $user = $request->user();
        $filter = $this->filter($request);

        return view('dashboard.akademik.nilai.upload', [
            'filter' => $filter,
            'daftarKelas' => Akademik::daftarKelas($user, $filter['jenjang']),
            'daftarMapel' => $this->daftarMapel($user, $filter['jenjang']),
            'daftarJenjang' => $this->daftarJenjang($user),
            'tahunPilihan' => Akademik::tahunAjaranPilihan(),
            'preview' => null,
            'errorBaris' => [],
        ]);
    }

    public function preview(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.ubah'), 403);

        $konteks = $this->konteks($request);

        $request->validate([
            'file_nilai' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ], [
            'file_nilai.required' => 'Pilih file CSV yang akan diunggah.',
            'file_nilai.mimes' => 'Format file harus CSV. Simpan Excel sebagai CSV UTF-8 terlebih dahulu.',
            'file_nilai.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        $user = $request->user();
        $santris = Akademik::santriQuery($user, $konteks['jenjang'])
            ->where('kelas', $konteks['kelas'])
            ->get()
            ->keyBy(fn ($s) => strtoupper(trim((string) $s->nomor_induk)));

        $existing = NilaiSantri::query()
            ->whereIn('santri_id', $santris->pluck('id'))
            ->konteks($konteks['mapel']->id, $konteks['tahun'], $konteks['semester'], $konteks['kategori'])
            ->get()
            ->keyBy('santri_id');

        $baris = [];
        $errors = [];
        $terlihat = [];

        foreach ($this->bacaCsv($request->file('file_nilai')) as $row) {
            $nomorBaris = $row['_line'] ?? null;
            $nis = strtoupper(trim((string) ($row['nis'] ?? $row['nomor_induk'] ?? '')));
            $nilaiMentah = trim((string) ($row['nilai'] ?? ''));
            $catatan = trim((string) ($row['catatan'] ?? '')) ?: null;

            if ($nis === '' && $nilaiMentah === '') {
                continue;
            }

            if ($nis === '') {
                $errors[] = 'Baris '.$nomorBaris.': NIS/Nomor Induk kosong.';

                continue;
            }

            if (isset($terlihat[$nis])) {
                $errors[] = 'Baris '.$nomorBaris.': NIS '.$nis.' muncul lebih dari sekali.';

                continue;
            }
            $terlihat[$nis] = true;

            $santri = $santris->get($nis);

            if (! $santri) {
                $errors[] = 'Baris '.$nomorBaris.': NIS '.$nis.' tidak ditemukan di kelas '.$konteks['kelas'].'.';

                continue;
            }

            $nilai = str_replace(',', '.', $nilaiMentah);
            if ($nilai === '' || ! is_numeric($nilai)) {
                $errors[] = 'Baris '.$nomorBaris.': nilai "'.$nilaiMentah.'" bukan angka.';

                continue;
            }

            $nilai = round((float) $nilai, 2);
            if ($nilai < 0 || $nilai > 100) {
                $errors[] = 'Baris '.$nomorBaris.': nilai harus antara 0 sampai 100.';

                continue;
            }

            $lama = $existing->get($santri->id);

            $baris[] = [
                'santri_id' => $santri->id,
                'nis' => $santri->nomor_induk,
                'nama' => $santri->nama_lengkap,
                'nilai' => $nilai,
                'catatan' => $catatan,
                'aksi' => $lama ? 'Perbarui' : 'Baru',
                'status' => $lama?->status,
            ];
        }

        if ($baris === [] && $errors === []) {
            $errors[] = 'File tidak berisi baris nilai yang bisa diproses.';
        }

        $preview = [
            'konteks' => $konteks,
            'baris' => $baris,
            'file' => $request->file('file_nilai')->getClientOriginalName(),
        ];

        if ($errors === [] && $baris !== []) {
            $request->session()->put($this->sessionKey($user->id), $preview);
        }

        return view('dashboard.akademik.nilai.upload', [
            'filter' => $this->filterFromKonteks($konteks),
            'daftarKelas' => Akademik::daftarKelas($user, $konteks['jenjang']),
            'daftarMapel' => $this->daftarMapel($user, $konteks['jenjang']),
            'daftarJenjang' => $this->daftarJenjang($user),
            'tahunPilihan' => Akademik::tahunAjaranPilihan(),
            'preview' => $preview,
            'errorBaris' => $errors,
        ]);
    }

    public function import(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.ubah'), 403);

        $user = $request->user();

        abort_if(
            Akses::adminTanpaJenjang($user),
            403,
            'Akun admin ini belum memiliki jenjang. Hubungi Super Admin.'
        );

        $terbitkan = $request->input('aksi') === 'terbit';
        abort_unless(! $terbitkan || $user->bisa('akademik.terbit'), 403);

        $preview = $request->session()->pull($this->sessionKey($user->id));

        if (! $preview || empty($preview['baris'])) {
            return redirect()
                ->route('admin.akademik.nilai.index')
                ->with('error', 'Data pratinjau sudah kedaluwarsa. Silakan unggah ulang file.');
        }

        $konteks = $preview['konteks'];

        // Pastikan konteks lama masih valid dan masih dalam jenjang user.
        abort_unless(Akses::cocokJenjang($user, $konteks['jenjang']), 403);

        $mapel = MataPelajaran::find($konteks['mapel']->id);
        abort_unless($mapel && $mapel->jenjang === $konteks['jenjang'], 403);

        $idSantri = Akademik::santriQuery($user, $konteks['jenjang'])
            ->where('kelas', $konteks['kelas'])
            ->pluck('id')
            ->all();

        $disimpan = 0;

        DB::transaction(function () use ($preview, $konteks, $idSantri, $user, &$disimpan) {
            foreach ($preview['baris'] as $baris) {
                if (! in_array($baris['santri_id'], $idSantri, true)) {
                    continue;
                }

                $row = NilaiSantri::firstOrNew([
                    'santri_id' => $baris['santri_id'],
                    'mata_pelajaran_id' => $konteks['mapel']->id,
                    'tahun_ajaran' => $konteks['tahun'],
                    'semester' => $konteks['semester'],
                    'kategori' => $konteks['kategori'],
                ]);

                $baru = ! $row->exists;

                $row->fill([
                    'jenjang' => $konteks['jenjang'],
                    'kelas' => $konteks['kelas'],
                    'nilai' => $baris['nilai'],
                    'catatan' => $baris['catatan'],
                    'status' => 'draft',
                    'published_at' => null,
                    'diperbarui_oleh' => $user->id,
                ]);

                if ($baru) {
                    $row->created_by = $user->id;
                }

                $row->save();
                $disimpan++;
            }
        });

        if ($terbitkan) {
            $diterbitkan = NilaiSantri::query()
                ->whereIn('santri_id', $idSantri)
                ->where('jenjang', $konteks['jenjang'])
                ->where('kelas', $konteks['kelas'])
                ->konteks($konteks['mapel']->id, $konteks['tahun'], $konteks['semester'], $konteks['kategori'])
                ->where('status', 'draft')
                ->update([
                    'status' => 'published',
                    'published_at' => now(),
                    'diterbitkan_oleh' => $user->id,
                    'updated_at' => now(),
                ]);

            return redirect()
                ->route('admin.akademik.nilai.index', $this->queryKonteks($konteks))
                ->with('success', $disimpan.' nilai diunggah dan '.$diterbitkan.' nilai diterbitkan ke portal wali santri.');
        }

        return redirect()
            ->route('admin.akademik.nilai.index', $this->queryKonteks($konteks))
            ->with('success', $disimpan.' nilai berhasil diunggah sebagai draft. Periksa lalu terbitkan.');
    }

    /* ========================================================= Publikasi */

    public function terbitkan(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.terbit'), 403);

        $konteks = $this->konteks($request);
        $idSantri = Akademik::santriQuery($request->user(), $konteks['jenjang'])
            ->where('kelas', $konteks['kelas'])
            ->pluck('id')
            ->all();

        $jumlah = NilaiSantri::query()
            ->whereIn('santri_id', $idSantri)
            ->where('jenjang', $konteks['jenjang'])
            ->where('kelas', $konteks['kelas'])
            ->konteks($konteks['mapel']->id, $konteks['tahun'], $konteks['semester'], $konteks['kategori'])
            ->where('status', 'draft')
            ->update([
                'status' => 'published',
                'published_at' => now(),
                'diterbitkan_oleh' => $request->user()->id,
                'updated_at' => now(),
            ]);

        $pesan = $jumlah > 0
            ? $jumlah.' nilai diterbitkan dan sekarang terlihat di portal wali santri.'
            : 'Tidak ada nilai draft pada konteks ini.';

        return redirect()
            ->route('admin.akademik.nilai.index', $this->queryKonteks($konteks))
            ->with($jumlah > 0 ? 'success' : 'info', $pesan);
    }

    /* ======================================================== Template CSV */

    public function template(Request $request)
    {
        abort_unless($request->user()->bisa('akademik.ubah'), 403);

        $konteks = $this->konteks($request);
        $santris = Akademik::santriQuery($request->user(), $konteks['jenjang'])
            ->where('kelas', $konteks['kelas'])
            ->orderBy('nama_lengkap')
            ->get();

        $namaFile = 'template-nilai-'
            .strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', $konteks['mapel']->kode ?: $konteks['mapel']->nama))
            .'-kelas-'.preg_replace('/[^A-Za-z0-9]+/', '-', $konteks['kelas'])
            .'-'.str_replace('/', '-', $konteks['tahun'])
            .'-'.$konteks['semester'].'.csv';

        return response()->streamDownload(function () use ($santris, $konteks) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8 dengan benar.
            fputcsv($out, ['nis', 'nama', 'kelas', 'kategori', 'nilai', 'catatan']);
            foreach ($santris as $santri) {
                fputcsv($out, [
                    $santri->nomor_induk,
                    $santri->nama_lengkap,
                    $santri->kelas,
                    $konteks['kategori'],
                    '',
                    '',
                ]);
            }
            fclose($out);
        }, $namaFile, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /* ============================================================= Helper */

    private function filter(Request $request): array
    {
        $user = $request->user();
        $mapelId = (int) $request->input('mata_pelajaran_id');
        $mapel = $mapelId ? MataPelajaran::find($mapelId) : null;

        $jenjang = Akses::jenjangSlug($user)
            ?: Jenjang::normalisasi($request->input('jenjang'))
            ?: $mapel?->jenjang;

        $kelas = trim((string) $request->input('kelas')) ?: null;
        $tahun = trim((string) $request->input('tahun_ajaran')) ?: Akademik::tahunAjaranSekarang();
        $semester = strtolower((string) $request->input('semester')) ?: Akademik::semesterSekarang();
        $kategori = strtolower((string) $request->input('kategori')) ?: 'tugas';

        if (! array_key_exists($semester, Akademik::SEMESTER)) {
            $semester = Akademik::semesterSekarang();
        }

        if (! array_key_exists($kategori, Akademik::KATEGORI)) {
            $kategori = 'tugas';
        }

        // Admin tanpa jenjang: jangan tampilkan konteks apa pun.
        if (Akses::adminTanpaJenjang($user)) {
            return [
                'jenjang' => null,
                'kelas' => null,
                'mapel' => null,
                'tahun' => $tahun,
                'semester' => $semester,
                'kategori' => $kategori,
                'mismatch' => false,
                'lengkap' => false,
            ];
        }

        $mismatch = $jenjang && $mapel && $mapel->jenjang !== $jenjang;

        return [
            'jenjang' => $jenjang,
            'kelas' => $kelas,
            'mapel' => $mapel,
            'tahun' => $tahun,
            'semester' => $semester,
            'kategori' => $kategori,
            'mismatch' => $mismatch,
            'lengkap' => $jenjang && $kelas && $mapel && ! $mismatch,
        ];
    }

    private function filterFromKonteks(array $konteks): array
    {
        return [
            'jenjang' => $konteks['jenjang'],
            'kelas' => $konteks['kelas'],
            'mapel' => $konteks['mapel'],
            'tahun' => $konteks['tahun'],
            'semester' => $konteks['semester'],
            'kategori' => $konteks['kategori'],
            'mismatch' => false,
            'lengkap' => true,
        ];
    }

    /**
     * Validasi konteks nilai dan scoping jenjang.
     *
     * @return array{jenjang:string,kelas:string,tahun:string,semester:string,kategori:string,mapel:MataPelajaran}
     */
    private function konteks(Request $request): array
    {
        $data = $request->validate([
            'jenjang' => ['nullable', 'string', 'max:100'],
            'kelas' => ['required', 'string', 'max:50'],
            'mata_pelajaran_id' => ['required', 'integer'],
            'tahun_ajaran' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', Rule::in(array_keys(Akademik::SEMESTER))],
            'kategori' => ['required', Rule::in(array_keys(Akademik::KATEGORI))],
        ], [
            'kelas.required' => 'Pilih kelas terlebih dahulu.',
            'mata_pelajaran_id.required' => 'Pilih mata pelajaran.',
            'tahun_ajaran.regex' => 'Format tahun ajaran harus seperti 2026/2027.',
        ]);

        $user = $request->user();

        abort_if(
            Akses::adminTanpaJenjang($user),
            403,
            'Akun admin ini belum memiliki jenjang. Hubungi Super Admin.'
        );

        $mapel = MataPelajaran::find($data['mata_pelajaran_id']);

        if (! $mapel) {
            throw ValidationException::withMessages([
                'mata_pelajaran_id' => 'Mata pelajaran tidak ditemukan.',
            ]);
        }

        $jenjang = Akses::jenjangSlug($user)
            ?: Jenjang::normalisasi($data['jenjang'] ?? '')
            ?: $mapel->jenjang;

        abort_unless(Akses::cocokJenjang($user, $jenjang), 403, 'Jenjang ini di luar kewenangan Anda.');

        if ($mapel->jenjang !== $jenjang) {
            throw ValidationException::withMessages([
                'mata_pelajaran_id' => 'Mata pelajaran ini bukan untuk jenjang yang Anda pilih.',
            ]);
        }

        $adaSantri = Akademik::santriQuery($user, $jenjang)
            ->where('kelas', $data['kelas'])
            ->exists();

        if (! $adaSantri) {
            throw ValidationException::withMessages([
                'kelas' => 'Tidak ada santri pada kelas ini di jenjang Anda.',
            ]);
        }

        return [
            'jenjang' => $jenjang,
            'kelas' => $data['kelas'],
            'tahun' => $data['tahun_ajaran'],
            'semester' => $data['semester'],
            'kategori' => $data['kategori'],
            'mapel' => $mapel,
        ];
    }

    private function queryKonteks(array $konteks): array
    {
        return [
            'jenjang' => $konteks['jenjang'],
            'kelas' => $konteks['kelas'],
            'mata_pelajaran_id' => $konteks['mapel']->id,
            'tahun_ajaran' => $konteks['tahun'],
            'semester' => $konteks['semester'],
            'kategori' => $konteks['kategori'],
        ];
    }

    private function daftarMapel($user, ?string $jenjang)
    {
        return Akademik::mapelQuery($user)
            ->where('aktif', true)
            ->when($jenjang, fn ($q) => $q->where('jenjang', $jenjang))
            ->orderBy('nama')
            ->get();
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
     * Baca CSV menjadi array asosiatif berdasarkan header.
     *
     * @return array<int, array<string, mixed>>
     */
    private function bacaCsv($file): array
    {
        $handle = fopen($file->getRealPath(), 'r');

        if (! $handle) {
            return [];
        }

        $barisPertama = (string) fgets($handle);
        $delimiter = substr_count($barisPertama, ';') > substr_count($barisPertama, ',') ? ';' : ',';
        rewind($handle);

        $header = null;
        $baris = [];
        $nomor = 0;

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $nomor++;

            if ($nomor === 1) {
                $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) ($data[0] ?? ''));
                $header = array_map(
                    fn ($h) => strtolower(preg_replace('/[^a-z0-9]+/i', '_', trim((string) $h))),
                    $data
                );

                continue;
            }

            if (count(array_filter($data, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $row = ['_line' => $nomor];
            foreach ($header as $i => $key) {
                $row[$key] = $data[$i] ?? '';
            }

            $baris[] = $row;
        }

        fclose($handle);

        return $baris;
    }

    private function sessionKey(int $userId): string
    {
        return 'akademik.import.'.$userId;
    }
}
