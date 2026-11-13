<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\PendaftaranOnlineController as PublicPendaftaranController;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Models\Student;
use App\Models\User;
use App\Support\Akses;
use App\Support\Ppdb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PendaftaranController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = PendaftaranOnline::latest();

        // Admin Jenjang hanya melihat pendaftar pada jenjangnya.
        if (Akses::dibatasiJenjang($user)) {
            $query->whereIn(
                'jenjang_tujuan',
                Akses::nilaiJenjangDb($user, PendaftaranOnline::class, 'jenjang_tujuan') ?: ['__tidak_ada__']
            );
        }

        $pendaftar = $query->get();

        return view('dashboard.pendaftaran.index', compact('pendaftar'));
    }

    /**
     * Tolak akses bila data pendaftar berada di luar jenjang user.
     * Role global (Super Admin, Pengasuh, Pembina) selalu lolos.
     */
    private function pastikanJenjang(PendaftaranOnline $pendaftaran): void
    {
        abort_unless(
            Akses::cocokJenjang(auth()->user(), $pendaftaran->jenjang_tujuan),
            403,
            'Data pendaftar ini berada di luar jenjang yang Anda tangani.'
        );
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_pendaftaran' => ['required', 'in:diterima,ditolak'],
        ]);

        $pendaftaran = PendaftaranOnline::findOrFail($id);
        $this->pastikanJenjang($pendaftaran);

        try {
            if ($request->status_pendaftaran === 'ditolak') {
                Ppdb::tolak($pendaftaran);

                return redirect()->back()->with('success', 'Status pendaftaran berhasil diperbarui.');
            }

            $hasil = Ppdb::terima($pendaftaran);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        // Normalisasi nomor WhatsApp (utamakan nomor orang tua)
        $phone = preg_replace('/\D/', '', (string) ($pendaftaran->no_hp_ortu ?: ($pendaftaran->no_hp_aktif ?? '')));
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        $email = Ppdb::emailWali($pendaftaran);

        $infoLogin = $hasil['rawPassword']
            ? "Email: {$email}\nPassword: {$hasil['rawPassword']}"
            : "Email: {$email}\nPassword: sama seperti akun portal Anda sebelumnya";

        // Pesan yang akan otomatis terisi di WhatsApp
        $pesanWa = "Alhamdulillah, Pendaftaran santri atas nama *{$pendaftaran->nama_lengkap}* telah *DITERIMA* di Pesantren Babul Hasanah.\n\nSilakan login ke Portal Wali Santri:\n{$infoLogin}\n\nSilakan login melalui link berikut:\n".route('login');

        $waLink = $phone !== '' ? "https://wa.me/{$phone}?text=".urlencode($pesanWa) : null;

        $pesan = $hasil['akunBaru']
            ? 'Status berhasil diubah menjadi Diterima! Akun telah dibuat.'
            : 'Status berhasil diubah menjadi Diterima! Santri ditautkan ke akun wali yang sudah ada.';

        if ($hasil['diaktifkan']) {
            $pesan .= $hasil['emailTerkirim']
                ? ' Akun wali diaktifkan dan kata sandi dikirim ke '.$email.'.'
                : ' Akun wali diaktifkan, tetapi email gagal dikirim. Sampaikan kata sandi melalui WhatsApp.';
        }

        return redirect()->back()->with([
            'success' => $pesan,
            'wa_link' => $waLink,
            'nama_santri' => $pendaftaran->nama_lengkap,
        ]);
    }

    /** Form edit data pendaftar (12 field PPDB). */
    public function edit($id)
    {
        $pendaftaran = PendaftaranOnline::findOrFail($id);
        $this->pastikanJenjang($pendaftaran);

        return view('dashboard.pendaftaran.edit', [
            'pendaftaran' => $pendaftaran,
            'jenjangList' => PublicPendaftaranController::JENJANG,
        ]);
    }

    /** Simpan perubahan data pendaftar sekaligus menyelaraskan data santri. */
    public function update(Request $request, $id)
    {
        $pendaftaran = PendaftaranOnline::findOrFail($id);
        $this->pastikanJenjang($pendaftaran);

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tempat_tanggal_lahir' => ['required', 'string', 'max:255'],
            'no_hp_aktif' => ['required', 'string', 'max:30'],
            'alamat_domisili' => ['required', 'string', 'max:1000'],
            'parent_email' => ['required', 'email', 'max:255'],
            'nama_ortu_wali' => ['required', 'string', 'max:255'],
            'no_hp_ortu' => ['required', 'string', 'max:30'],
            'asal_sekolah' => ['required', 'string', 'max:255'],
            'alamat_sekolah' => ['required', 'string', 'max:1000'],
            'tahun_lulus' => ['required', 'digits:4'],
            'nisn' => ['nullable', 'string', 'max:20'],
            'jenjang_tujuan' => ['required', 'in:'.implode(',', PublicPendaftaranController::JENJANG)],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'tempat_tanggal_lahir.required' => 'Tempat tanggal lahir wajib diisi.',
            'no_hp_aktif.required' => 'Nomor handphone aktif wajib diisi.',
            'alamat_domisili.required' => 'Alamat tinggal (domisili) wajib diisi.',
            'parent_email.required' => 'Email orang tua/wali wajib diisi.',
            'parent_email.email' => 'Format email orang tua/wali tidak valid.',
            'nama_ortu_wali.required' => 'Nama orang tua/wali wajib diisi.',
            'no_hp_ortu.required' => 'Nomor handphone orang tua wajib diisi.',
            'asal_sekolah.required' => 'Sekolah asal wajib diisi.',
            'alamat_sekolah.required' => 'Alamat sekolah asal wajib diisi.',
            'tahun_lulus.required' => 'Tahun lulus wajib diisi.',
            'tahun_lulus.digits' => 'Tahun lulus harus 4 angka, contoh: 2026.',
            'jenjang_tujuan.required' => 'Pilihan jenjang wajib dipilih.',
            'jenjang_tujuan.in' => 'Pilihan jenjang tidak valid.',
        ]);

        // Pastikan struktur tabel sudah mendukung 12 field PPDB.
        if ($kolomHilang = PendaftaranOnline::kolomFormHilang()) {
            return back()->withInput()->withErrors([
                'database' => PendaftaranOnline::pesanSkemaBelumSiap($kolomHilang),
            ]);
        }

        $emailWali = strtolower((string) $validated['parent_email']);

        // Santri hasil verifikasi & akun wali yang tertaut (jika sudah diterima).
        $santri = Santri::where('pendaftaran_online_id', $pendaftaran->id)->first();
        $student = Student::where('pendaftaran_online_id', $pendaftaran->id)->first();
        $userTertaut = $santri?->user ?: User::withTrashed()->where('email', $pendaftaran->email)->first();

        // Email tidak boleh dipakai akun lain (selain akun wali yang tertaut).
        $emailPemilik = User::withTrashed()->where('email', $emailWali)->first();
        if ($emailPemilik && $emailPemilik->id !== $userTertaut?->id) {
            return back()->withInput()->withErrors([
                'parent_email' => "Email orang tua/wali {$emailWali} sudah dipakai akun lain. Gunakan email berbeda.",
            ]);
        }

        $namaWali = $validated['nama_ortu_wali'] ?: $validated['nama_lengkap'];
        $slugJenjang = Ppdb::jenjangSlug($validated['jenjang_tujuan']);

        // NISN pada tabel students memakai fallback yang sama dengan Ppdb::terima().
        // Bila admin mengoreksi NISN, pastikan tidak bentrok dengan santri lain.
        $nisnBaru = trim((string) ($validated['nisn'] ?? '')) ?: 'PPDB-'.$pendaftaran->no_pendaftaran;

        if ($student && $student->nisn !== $nisnBaru) {
            $bentrok = Student::withTrashed()
                ->where('nisn', $nisnBaru)
                ->whereKeyNot($student->id)
                ->exists();

            if ($bentrok) {
                return back()->withInput()->withErrors([
                    'nisn' => "NISN {$nisnBaru} sudah dipakai santri lain.",
                ]);
            }
        }

        DB::transaction(function () use ($validated, $pendaftaran, $santri, $student, $userTertaut, $namaWali, $emailWali, $slugJenjang, $nisnBaru) {
            $pendaftaran->update([
                'nama_lengkap' => $validated['nama_lengkap'],
                'tempat_tanggal_lahir' => $validated['tempat_tanggal_lahir'],
                'no_hp_aktif' => $validated['no_hp_aktif'],
                'alamat_domisili' => $validated['alamat_domisili'],
                'email' => $emailWali,
                'parent_email' => $emailWali,
                'nama_ortu_wali' => $validated['nama_ortu_wali'],
                'no_hp_ortu' => $validated['no_hp_ortu'],
                'asal_sekolah' => $validated['asal_sekolah'],
                'alamat_sekolah' => $validated['alamat_sekolah'],
                'tahun_lulus' => $validated['tahun_lulus'],
                'nisn' => $validated['nisn'] ?? null,
                'jenjang_tujuan' => $validated['jenjang_tujuan'],
            ]);

            // Sinkronkan akun portal wali agar login tetap memakai data terbaru.
            if ($userTertaut) {
                if ($userTertaut->trashed()) {
                    $userTertaut->restore();
                }

                $userTertaut->update([
                    'name' => $namaWali,
                    'email' => $emailWali,
                    'phone' => $validated['no_hp_ortu'],
                ]);
            }

            // Sinkronkan data santri yang sudah diverifikasi.
            if ($santri) {
                $santri->update([
                    'nama_lengkap' => $validated['nama_lengkap'],
                    'jenjang_pendidikan' => $validated['jenjang_tujuan'],
                    'nama_wali' => $namaWali,
                    'alamat' => $validated['alamat_domisili'],
                ]);
            }

            // Sinkronkan data siswa (Modul 1/PPDB).
            if ($student) {
                $perubahan = [
                    'nisn' => $nisnBaru,
                    'name' => $validated['nama_lengkap'],
                    'parent_email' => $emailWali,
                ];

                // Jenjang hanya diubah bila santri belum diplot ke kelas,
                // supaya kelas yang sudah dipilih tidak menjadi tidak cocok.
                if (! $student->class_id && ! $student->classes()->exists() && $slugJenjang) {
                    $perubahan['jenjang'] = $slugJenjang;
                }

                $student->update($perubahan);
            }
        });

        return redirect()
            ->route('admin.pendaftaran.index')
            ->with('success', 'Data pendaftaran berhasil diperbarui.');
    }

    /** Hapus data pendaftaran beserta data santri hasil verifikasinya. */
    public function destroy($id)
    {
        $pendaftaran = PendaftaranOnline::findOrFail($id);
        $this->pastikanJenjang($pendaftaran);

        DB::transaction(function () use ($pendaftaran) {
            // Santri yang lahir dari pendaftaran ini ikut dihapus agar tidak
            // menyisakan data yatim; akun portal wali tetap dipertahankan.
            Santri::where('pendaftaran_online_id', $pendaftaran->id)->delete();

            Student::where('pendaftaran_online_id', $pendaftaran->id)
                ->get()
                ->each(function (Student $student) {
                    $student->classes()->detach();
                    $student->delete();
                });

            $pendaftaran->delete();
        });

        return redirect()
            ->route('admin.pendaftaran.index')
            ->with('success', 'Data pendaftaran berhasil dihapus.');
    }
}
