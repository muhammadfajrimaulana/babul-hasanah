<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\KredensialMail;
use App\Models\Kelas;
use App\Models\RiwayatKelas;
use App\Models\Santri;
use App\Models\User;
use App\Support\Akses;
use App\Support\CatatanAktivitas;
use App\Support\Jenjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SantriAksiController extends Controller
{
    /** Reset password wali menjadi password sementara 24 jam. */
    public function resetPasswordWali($id)
    {
        $santri = Santri::with('user')->findOrFail($id);
        $this->pastikanJenjang($santri);

        $wali = $santri->user;

        if (! $wali || $wali->role !== 'wali_santri') {
            return back()->with('error', 'Santri ini belum memiliki akun wali santri.');
        }

        $password = Str::password(12, symbols: false);

        $wali->forceFill([
            'password' => Hash::make($password),
            'must_change_password' => true,
            'temp_password_expires_at' => now()->addDay(),
            'is_active' => true,
        ])->save();

        $terkirim = false;

        try {
            Mail::to($wali->email, $wali->name)->send(new KredensialMail(
                nama: $wali->name,
                email: $wali->email,
                passwordSementara: $password,
                peran: 'Wali Santri',
                judul: 'Reset Password Portal Wali Santri',
                catatan: 'Password sementara untuk akun wali santri '.$santri->nama_lengkap.'.',
            ));
            $terkirim = true;
        } catch (\Throwable $e) {
            report($e);
        }

        CatatanAktivitas::catat('reset_password_wali', $santri, [
            'wali_id' => $wali->id,
            'email_terkirim' => $terkirim,
        ]);

        return back()->with(
            $terkirim ? 'success' : 'error',
            $terkirim
                ? 'Password wali direset. Password sementara telah dikirim ke email wali dan berlaku 24 jam.'
                : 'Password wali direset, tetapi email gagal dikirim. Periksa konfigurasi email.'
        );
    }

    /** Pindahkan santri ke jenjang lain tanpa membuat data baru. */
    public function pindahJenjang(Request $request, $id)
    {
        $santri = Santri::findOrFail($id);
        $this->pastikanJenjang($santri);

        $data = $request->validate([
            'jenjang' => ['required', 'string'],
            'kelas' => ['required', 'string', 'max:50'],
            'alasan' => ['required', 'string', 'max:500'],
        ], [
            'jenjang.required' => 'Jenjang tujuan wajib dipilih.',
            'kelas.required' => 'Kelas tujuan wajib diisi.',
            'alasan.required' => 'Alasan pindah jenjang wajib diisi.',
        ]);

        $slug = Jenjang::normalisasi($data['jenjang']);

        if (! $slug) {
            return back()->withErrors(['jenjang' => 'Jenjang tujuan tidak valid.']);
        }

        $tujuan = Jenjang::label($slug);

        if (Jenjang::normalisasi($santri->jenjang_pendidikan) === $slug) {
            return back()->with('error', 'Santri sudah berada di jenjang tersebut.');
        }

        DB::transaction(function () use ($santri, $tujuan, $data, $slug) {
            $lama = [
                'jenjang' => $santri->jenjang_pendidikan,
                'kelas' => $santri->kelas,
                'kelas_id' => $santri->kelas_id,
            ];

            $kelas = Kelas::firstOrCreate(
                [
                    'jenjang' => $slug,
                    'nama' => $data['kelas'],
                    'tahun_ajaran' => $this->tahunAjaran(),
                ],
                ['aktif' => true]
            );

            $santri->update([
                'jenjang_pendidikan' => $tujuan,
                'kelas' => $data['kelas'],
                'kelas_id' => $kelas->id,
            ]);

            RiwayatKelas::create([
                'santri_id' => $santri->id,
                'kelas_id' => $kelas->id,
                'jenjang' => $tujuan,
                'kelas' => $data['kelas'],
                'kamar' => $santri->kamar,
                'tahun_ajaran' => $this->tahunAjaran(),
                'semester' => '1',
                'status' => 'pindah',
                'keterangan' => $data['alasan'].' (dari '.$lama['jenjang'].' '.$lama['kelas'].')',
                'diproses_oleh' => auth()->id(),
            ]);

            CatatanAktivitas::catat('pindah_jenjang', $santri, [
                'dari' => $lama,
                'ke' => ['jenjang' => $tujuan, 'kelas' => $data['kelas']],
            ], $data['alasan']);
        });

        return back()->with('success', 'Santri berhasil dipindahkan ke jenjang '.$tujuan.'. Riwayat lama tetap tersimpan.');
    }

    /** Arsipkan santri yang keluar/berhenti (bukan dihapus). */
    public function arsipkan(Request $request, $id)
    {
        $santri = Santri::findOrFail($id);
        $this->pastikanJenjang($santri);

        $data = $request->validate([
            'status' => ['required', 'in:keluar,arsip'],
            'alasan' => ['required', 'string', 'max:500'],
        ]);

        $santri->update([
            'status_kelulusan' => $data['status'],
        ]);

        RiwayatKelas::create([
            'santri_id' => $santri->id,
            'kelas_id' => $santri->kelas_id,
            'jenjang' => $santri->jenjang_pendidikan,
            'kelas' => $santri->kelas,
            'kamar' => $santri->kamar,
            'tahun_ajaran' => $this->tahunAjaran(),
            'semester' => '1',
            'status' => 'keluar',
            'keterangan' => $data['alasan'],
            'diproses_oleh' => auth()->id(),
        ]);

        CatatanAktivitas::catat('arsipkan_santri', $santri, ['status' => $data['status']], $data['alasan']);

        return back()->with('success', 'Data santri diarsipkan. Akun wali tetap aktif untuk melihat riwayat anak.');
    }

    /** Kembalikan santri dari arsip (khusus Superadmin). */
    public function kembalikan($id)
    {
        abort_unless(Akses::superAdmin(auth()->user()), 403);

        $santri = Santri::findOrFail($id);
        $santri->update([
            'status_kelulusan' => Santri::STATUS_AKTIF,
            'tahun_lulus' => null,
        ]);

        CatatanAktivitas::catat('kembalikan_santri', $santri, [], 'Dikembalikan dari arsip oleh Superadmin');

        return back()->with('success', 'Santri dikembalikan ke status aktif.');
    }

    /** Form proses naik kelas. */
    public function naikKelasForm(Request $request)
    {
        $user = auth()->user();
        $jenjang = Akses::jenjangSlug($user) ?: Jenjang::normalisasi($request->input('jenjang'));

        $query = Santri::query()->where('status_kelulusan', Santri::STATUS_AKTIF);

        if (Akses::dibatasiJenjang($user)) {
            $query->whereIn('jenjang_pendidikan', Akses::nilaiJenjangDb($user, Santri::class, 'jenjang_pendidikan') ?: ['__tidak_ada__']);
        } elseif ($jenjang) {
            $query->whereIn('jenjang_pendidikan', \App\Support\RekapJenjang::nilaiDb(Santri::class, 'jenjang_pendidikan', $jenjang) ?: ['__tidak_ada__']);
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->input('kelas'));
        }

        $santris = $request->filled('kelas')
            ? $query->orderBy('nama_lengkap')->get()
            : collect();

        $daftarKelas = (clone $query)->whereNotNull('kelas')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('dashboard.santri.naik-kelas', [
            'santris' => $santris,
            'daftarKelas' => $daftarKelas,
            'jenjang' => $jenjang,
            'tahunAjaran' => $this->tahunAjaran(),
            'bisaPilihJenjang' => ! Akses::dibatasiJenjang($user),
        ]);
    }

    /** Simpan proses naik kelas / tinggal kelas. */
    public function naikKelasProses(Request $request)
    {
        $data = $request->validate([
            'tahun_ajaran' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', 'in:1,2'],
            'santri_id' => ['required', 'array', 'min:1'],
            'santri_id.*' => ['integer'],
            'status' => ['required', 'array'],
            'status.*' => ['required', 'in:naik,tinggal'],
        ], [
            'santri_id.required' => 'Pilih minimal satu santri.',
            'tahun_ajaran.regex' => 'Format tahun ajaran harus 2026/2027.',
        ]);

        $diproses = 0;

        DB::transaction(function () use ($data, &$diproses) {
            foreach ($data['santri_id'] as $id) {
                $santri = Santri::find($id);

                if (! $santri || ! Akses::cocokJenjang(auth()->user(), $santri->jenjang_pendidikan)) {
                    continue;
                }

                $status = $data['status'][$id] ?? null;

                if (! in_array($status, ['naik', 'tinggal'], true)) {
                    continue;
                }

                $kelasLama = (string) $santri->kelas;
                $kelasBaru = $status === 'naik' ? $this->naikkanKelas($kelasLama) : $kelasLama;

                $kelas = Kelas::firstOrCreate(
                    [
                        'jenjang' => Jenjang::normalisasi($santri->jenjang_pendidikan) ?: $santri->jenjang_pendidikan,
                        'nama' => $kelasBaru,
                        'tahun_ajaran' => $data['tahun_ajaran'],
                    ],
                    ['aktif' => true]
                );

                $santri->update(['kelas' => $kelasBaru, 'kelas_id' => $kelas->id]);

                RiwayatKelas::create([
                    'santri_id' => $santri->id,
                    'kelas_id' => $kelas->id,
                    'jenjang' => $santri->jenjang_pendidikan,
                    'kelas' => $kelasBaru,
                    'kamar' => $santri->kamar,
                    'tahun_ajaran' => $data['tahun_ajaran'],
                    'semester' => $data['semester'],
                    'status' => $status,
                    'keterangan' => $status === 'naik'
                        ? 'Naik dari '.$kelasLama.' ke '.$kelasBaru
                        : 'Tinggal di kelas '.$kelasLama,
                    'diproses_oleh' => auth()->id(),
                ]);

                CatatanAktivitas::catat('naik_kelas', $santri, [
                    'dari' => $kelasLama,
                    'ke' => $kelasBaru,
                    'status' => $status,
                    'tahun_ajaran' => $data['tahun_ajaran'],
                    'semester' => $data['semester'],
                ]);

                $diproses++;
            }
        });

        return redirect()->route('admin.santri.index')
            ->with('success', $diproses.' data santri berhasil diproses. Nilai lama tetap terkunci di riwayat.');
    }

    /** Rollback kenaikan kelas (khusus Superadmin, wajib alasan). */
    public function rollbackNaikKelas(Request $request, RiwayatKelas $riwayat)
    {
        abort_unless(Akses::superAdmin(auth()->user()), 403);

        $data = $request->validate([
            'alasan' => ['required', 'string', 'max:500'],
        ]);

        $santri = $riwayat->santri;

        if (! $santri) {
            return back()->with('error', 'Data santri untuk riwayat ini tidak ditemukan.');
        }

        // Kelas sebelum proses ada di keterangan; fallback ke riwayat sebelumnya.
        $sebelumnya = RiwayatKelas::query()
            ->where('santri_id', $santri->id)
            ->where('id', '<', $riwayat->id)
            ->orderByDesc('id')
            ->first();

        $kelasLama = $sebelumnya?->kelas;

        if (! $kelasLama && preg_match('/Naik dari (.+?) ke /', (string) $riwayat->keterangan, $m)) {
            $kelasLama = $m[1];
        }

        if (! $kelasLama) {
            return back()->with('error', 'Kelas sebelum kenaikan tidak dapat ditentukan. Periksa riwayat secara manual.');
        }

        DB::transaction(function () use ($santri, $riwayat, $kelasLama, $data) {
            $santri->update(['kelas' => $kelasLama, 'kelas_id' => null]);
            $riwayat->update(['status' => 'dibatalkan', 'keterangan' => $riwayat->keterangan.' | Dibatalkan: '.$data['alasan']]);

            CatatanAktivitas::catat('rollback_naik_kelas', $santri, [
                'riwayat_id' => $riwayat->id,
                'dikembalikan_ke' => $kelasLama,
            ], $data['alasan']);
        });

        return back()->with('success', 'Kenaikan kelas dibatalkan dan dicatat di log aktivitas.');
    }

    private function pastikanJenjang(Santri $santri): void
    {
        abort_unless(
            Akses::cocokJenjang(auth()->user(), $santri->jenjang_pendidikan),
            403,
            'Data santri ini berada di luar jenjang yang Anda tangani.'
        );
    }

    private function naikkanKelas(string $kelas): string
    {
        if (preg_match('/^(\d+)(.*)$/', trim($kelas), $m)) {
            return ((int) $m[1] + 1).$m[2];
        }

        return $kelas;
    }

    private function tahunAjaran(): string
    {
        return now()->month >= 7
            ? now()->year.'/'.(now()->year + 1)
            : (now()->year - 1).'/'.now()->year;
    }
}
