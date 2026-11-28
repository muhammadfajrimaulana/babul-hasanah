<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Alumni;
use App\Models\DokumenSantri;
use App\Models\Legalisir;
use App\Models\NilaiSantri;
use App\Models\Pelanggaran;
use App\Models\Pengumuman;
use App\Models\TracerStudy;
use App\Support\Akademik;
use App\Support\CatatanAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Portal Alumni.
 *
 * Alumni hanya melihat data dirinya sendiri, mengunduh dokumen, mengajukan
 * legalisir, dan memperbarui data kontak/karier. Riwayat akademik & pelanggaran
 * bersifat terkunci (read-only) tetapi tetap terlihat.
 */
class AlumniPortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $alumni = $this->alumni($request);
        $santri = $alumni->santri;

        if ($santri) {
            $santri->load([
                'kelasModel.waliKelas',
                'dokumen',
                'hafalan' => fn ($q) => $q->latest('tanggal'),
                'pelanggaran' => fn ($q) => $q->latest('tanggal'),
                'riwayatKelas' => fn ($q) => $q->latest(),
                'nilai' => fn ($q) => $q->with('mataPelajaran.guru')->latest(),
            ]);
        }

        $alumni->load(['tracer', 'legalisir']);

        $nilaiAkhir = collect();

        if ($santri) {
            $nilaiAkhir = NilaiSantri::query()
                ->with('mataPelajaran.guru')
                ->where('santri_id', $santri->id)
                ->where('status', 'published')
                ->get()
                ->groupBy('mata_pelajaran_id')
                ->map(function ($baris) {
                    $mapel = $baris->first()->mataPelajaran;

                    return [
                        'mapel' => $mapel?->nama ?? 'Mata Pelajaran',
                        'rata' => round((float) $baris->avg('nilai'), 1),
                        'jumlah' => $baris->count(),
                    ];
                })
                ->sortByDesc('rata')
                ->values();
        }

        $presensi = collect();

        if ($santri) {
            $presensi = Absensi::query()
                ->where('santri_id', $santri->id)
                ->select('status_kehadiran', DB::raw('COUNT(*) as jumlah'))
                ->groupBy('status_kehadiran')
                ->pluck('jumlah', 'status_kehadiran');
        }

        $riwayatTerakhir = $santri?->riwayatKelas->first();

        return view('alumni.dashboard', [
            'alumni' => $alumni,
            'santri' => $santri,
            'nilaiAkhir' => $nilaiAkhir,
            'presensi' => $presensi,
            'riwayatTerakhir' => $riwayatTerakhir,
            'totalHafalan' => $santri?->hafalan->count() ?? 0,
            'hafalanTerakhir' => $santri?->hafalan->first(),
            'pelanggaran' => $santri?->pelanggaran ?? collect(),
            'dokumen' => $santri?->dokumen->keyBy('jenis') ?? collect(),
            'pengumuman' => Pengumuman::query()
                ->untukJenjang($alumni->jenjang)
                ->orderByDesc('tanggal')
                ->limit(5)
                ->get(),
        ]);
    }

    /**
     * History nilai alumni — hanya baca dan terkunci.
     *
     * Nilai dikelompokkan per tahun ajaran/semester agar alumni dapat
     * melihat seluruh riwayat akademiknya, bukan hanya ringkasan dashboard.
     */
    public function nilai(Request $request)
    {
        $alumni = $this->alumni($request);
        $santri = $alumni->santri;

        abort_unless($santri, 404, 'Data santri asal alumni tidak ditemukan.');

        $nilai = NilaiSantri::query()
            ->with('mataPelajaran.guru')
            ->where('santri_id', $santri->id)
            ->where('status', 'published')
            ->orderByDesc('tahun_ajaran')
            ->orderByDesc('semester')
            ->get();

        $periode = $nilai
            ->groupBy(fn (NilaiSantri $n) => $n->tahun_ajaran.'|'.$n->semester)
            ->map(function ($baris, string $kunci) {
                [$tahun, $semester] = array_pad(explode('|', $kunci), 2, '');

                $mapel = $baris
                    ->groupBy('mata_pelajaran_id')
                    ->map(function ($grup) {
                        $ambil = fn (string $kategori) => $grup->firstWhere('kategori', $kategori)?->nilai;
                        $akhir = round((float) $grup->avg('nilai'), 2);

                        return [
                            'mapel' => $grup->first()->mataPelajaran,
                            'tugas' => $ambil('tugas'),
                            'uts' => $ambil('uts'),
                            'uas' => $ambil('uas'),
                            'hafalan' => $ambil('hafalan'),
                            'akhir' => $akhir,
                            'predikat' => Akademik::predikat($akhir),
                        ];
                    })
                    ->sortBy(fn ($r) => $r['mapel']?->nama)
                    ->values();

                return [
                    'tahun' => $tahun,
                    'semester' => $semester,
                    'mapel' => $mapel,
                    'rata' => round((float) $baris->avg('nilai'), 2),
                ];
            })
            ->values();

        return view('alumni.nilai', [
            'alumni' => $alumni,
            'santri' => $santri,
            'periode' => $periode,
            'totalNilai' => $nilai->count(),
            'rataKeseluruhan' => round((float) $nilai->avg('nilai'), 2),
        ]);
    }

    public function profil(Request $request)
    {
        $alumni = $this->alumni($request);
        $tracer = TracerStudy::firstOrNew(['alumni_id' => $alumni->id]);

        return view('alumni.profil', [
            'alumni' => $alumni,
            'tracer' => $tracer,
            'kegiatan' => Alumni::KEGIATAN,
        ]);
    }

    public function updateProfil(Request $request)
    {
        $alumni = $this->alumni($request);
        $user = $request->user();

        $data = $request->validate([
            'no_hp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'kegiatan' => ['nullable', Rule::in(Alumni::KEGIATAN)],
            'instansi' => ['nullable', 'string', 'max:255'],
            'pekerjaan' => ['nullable', 'string', 'max:150'],
            'testimoni' => ['nullable', 'string', 'max:1000'],
            'kontak' => ['nullable', 'string', 'max:100'],
            'tahun_mulai' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ], [
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah dipakai akun lain.',
            'kegiatan.in' => 'Pilihan kegiatan tidak dikenali.',
            'testimoni.max' => 'Testimoni maksimal 1000 karakter.',
            'kontak.max' => 'Kontak maksimal 100 karakter.',
            'tahun_mulai.integer' => 'Tahun mulai harus berupa angka.',
            'catatan.max' => 'Catatan maksimal 2000 karakter.',
        ]);

        $alumni->update([
            'no_hp' => $data['no_hp'] ?? null,
            'email' => $data['email'] ?? null,
            'alamat' => $data['alamat'] ?? null,
            'kegiatan' => $data['kegiatan'] ?? null,
            'instansi' => $data['instansi'] ?? null,
            'pekerjaan' => $data['pekerjaan'] ?? null,
            'testimoni' => $data['testimoni'] ?? null,
        ]);

        // Sinkronkan email akun agar bisa dipakai login berikutnya.
        if (! empty($data['email']) && $data['email'] !== $user->email) {
            $user->update(['email' => $data['email']]);
        }

        // Tracer study kini disimpan dari satu form yang sama dengan profil.
        // Kegiatan, instansi, dan alamat memakai nilai profil yang sudah disimpan.
        $tracer = TracerStudy::updateOrCreate(['alumni_id' => $alumni->id], [
            'kegiatan' => $data['kegiatan'] ?? null,
            'instansi' => $data['instansi'] ?? null,
            'alamat' => $data['alamat'] ?? null,
            'kontak' => $data['kontak'] ?? null,
            'tahun_mulai' => $data['tahun_mulai'] ?? null,
            'catatan' => $data['catatan'] ?? null,
        ]);

        CatatanAktivitas::catat('alumni_update_profil', $alumni, [
            'kegiatan' => $alumni->kegiatan,
            'instansi' => $alumni->instansi,
            'testimoni' => filled($alumni->testimoni) ? 'diisi' : 'kosong',
            'tracer' => filled($tracer->kegiatan) ? 'diisi' : 'kosong',
        ]);

        return back()->with('success', 'Profil, tracer study, dan testimoni berhasil diperbarui. Riwayat akademik tetap terkunci.');
    }

    public function dokumen(Request $request)
    {
        $alumni = $this->alumni($request);
        $santri = $alumni->santri;
        $dokumen = $santri?->dokumen->keyBy('jenis') ?? collect();

        return view('alumni.dokumen', [
            'alumni' => $alumni,
            'dokumen' => $dokumen,
            'jenisDokumen' => DokumenSantri::JENIS,
        ]);
    }

    public function unduhDokumen(Request $request, DokumenSantri $dokumen)
    {
        $alumni = $this->alumni($request);

        abort_unless($alumni->santri_id === $dokumen->santri_id, 403,
            'Dokumen ini bukan milik data alumni Anda.');
        Gate::authorize('portal.lihatDokumenAlumni', [$dokumen, $alumni]);

        abort_unless($dokumen->status === 'published', 404, 'Dokumen belum diterbitkan.');

        abort_unless(
            filled($dokumen->file_path) && Storage::disk('public')->exists($dokumen->file_path),
            404,
            'Berkas dokumen tidak ditemukan.'
        );

        return Storage::disk('public')->download(
            $dokumen->file_path,
            $dokumen->file_name ?: $dokumen->jenis.'.pdf'
        );
    }

    public function legalisir(Request $request)
    {
        $alumni = $this->alumni($request);

        return view('alumni.legalisir', [
            'alumni' => $alumni,
            'legalisir' => Legalisir::query()->where('alumni_id', $alumni->id)->latest()->paginate(10),
            'jenisDokumen' => DokumenSantri::JENIS,
        ]);
    }

    public function ajukanLegalisir(Request $request)
    {
        $alumni = $this->alumni($request);
        Gate::authorize('portal.ajukanLegalisir', $alumni);

        $data = $request->validate([
            'jenis_dokumen' => ['required', 'string', 'max:100'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:10'],
        ], [
            'jenis_dokumen.required' => 'Pilih dokumen yang akan dilegalisir.',
            'jumlah.required' => 'Jumlah salinan wajib diisi.',
            'jumlah.max' => 'Maksimal 10 salinan per pengajuan.',
        ]);

        $legalisir = Legalisir::create([
            'alumni_id' => $alumni->id,
            'jenis_dokumen' => $data['jenis_dokumen'],
            'jumlah' => $data['jumlah'],
            'status' => 'menunggu',
        ]);

        CatatanAktivitas::catat('ajukan_legalisir', $legalisir, [
            'jenis_dokumen' => $legalisir->jenis_dokumen,
            'jumlah' => $legalisir->jumlah,
        ]);

        return back()->with('success', 'Pengajuan legalisir terkirim. Hasil dapat diunduh setelah diproses admin.');
    }

    public function unduhLegalisir(Request $request, Legalisir $legalisir)
    {
        $alumni = $this->alumni($request);

        abort_unless($legalisir->alumni_id === $alumni->id, 403, 'Berkas ini bukan milik Anda.');
        Gate::authorize('portal.unduhLegalisir', [$legalisir, $alumni]);

        abort_unless($legalisir->status === 'selesai', 404, 'Legalisir belum selesai diproses.');
        abort_unless(
            filled($legalisir->file_hasil) && Storage::disk('public')->exists($legalisir->file_hasil),
            404,
            'Berkas hasil legalisir tidak ditemukan.'
        );

        return Storage::disk('public')->download($legalisir->file_hasil);
    }

    public function pengumuman(Request $request)
    {
        $alumni = $this->alumni($request);

        return view('alumni.pengumuman', [
            'alumni' => $alumni,
            'pengumuman' => Pengumuman::query()
                ->untukJenjang($alumni->jenjang)
                ->orderByDesc('tanggal')
                ->paginate(12),
        ]);
    }

    private function alumni(Request $request): Alumni
    {
        $alumni = $request->user()->alumni;

        abort_unless($alumni, 403,
            'Akun ini belum tertaut ke data alumni. Hubungi Admin Jenjang.');
        Gate::authorize('portal.lihatAlumni', $alumni);

        return $alumni;
    }
}
