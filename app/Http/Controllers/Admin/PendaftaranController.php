<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\PendaftaranOnlineController as PublicPendaftaranController;
use App\Mail\KredensialMail;
use App\Models\Alumni;
use App\Models\PendaftaranOnline;
use App\Models\RiwayatKelas;
use App\Models\Santri;
use App\Models\User;
use App\Support\Akses;
use App\Support\CatatanAktivitas;
use App\Support\Jenjang;
use App\Support\Kelulusan;
use App\Support\KodeUnik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

        // Tandai arsip: pendaftar yang sudah menjadi santri/alumni tetap
        // ditampilkan dan tidak boleh dihapus agar riwayat PPDB utuh.
        $santriArsip = Santri::query()
            ->whereIn('pendaftaran_online_id', $pendaftar->pluck('id'))
            ->get()
            ->keyBy('pendaftaran_online_id');

        $alumniArsip = collect();
        if (Kelulusan::siapTautan() && $santriArsip->isNotEmpty()) {
            $alumniArsip = Alumni::query()
                ->whereIn('santri_id', $santriArsip->pluck('id'))
                ->get()
                ->keyBy('santri_id');
        }

        foreach ($pendaftar as $item) {
            $item->santri_tertaut = $santriArsip->get($item->id);
            $item->alumni_tertaut = $item->santri_tertaut
                ? $alumniArsip->get($item->santri_tertaut->id)
                : null;
        }

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

        if ($pendaftaran->status_pendaftaran === 'diterima') {
            return redirect()->back()->with('error', 'Pendaftar ini sudah diterima sebelumnya dan akunnya sudah dibuat!');
        }

        if ($request->status_pendaftaran === 'ditolak') {
            $pendaftaran->update(['status_pendaftaran' => 'ditolak']);

            CatatanAktivitas::catat('tolak_ppdb', $pendaftaran, [
                'no_pendaftaran' => $pendaftaran->no_pendaftaran,
            ]);

            return redirect()->back()->with('success', 'Status pendaftaran berhasil diperbarui.');
        }

        if (empty($pendaftaran->email)) {
            return redirect()->back()->with('error', 'Gagal membuat akun: Email orang tua/wali pendaftar masih kosong!');
        }

        $userLama = User::where('email', $pendaftaran->email)->first();

        if ($userLama && $userLama->role !== 'wali_santri') {
            return redirect()->back()->with(
                'error',
                "Gagal: email orang tua/wali {$pendaftaran->email} sudah dipakai akun lain (role: {$userLama->role}). Minta wali memakai email berbeda."
            );
        }

        $hasil = DB::transaction(function () use ($pendaftaran, $userLama) {
            $pendaftaran->update(['status_pendaftaran' => 'diterima']);

            $namaWali = $pendaftaran->nama_ortu_wali
                ?: ($pendaftaran->nama_ayah ?: ($pendaftaran->nama_ibu ?: $pendaftaran->nama_lengkap));

            // Wali yang sudah punya akun (anak kedua) memakai akun lama.
            if ($userLama) {
                $user = $userLama;
                $rawPassword = null;
            } else {
                $rawPassword = Str::password(12, symbols: false);

                $user = User::create([
                    'name' => $namaWali,
                    'email' => $pendaftaran->email,
                    'password' => Hash::make($rawPassword),
                    'role' => 'wali_santri',
                    'phone' => $pendaftaran->no_hp_ortu,
                    'is_active' => true,
                    // Kredensial dikirim ke email wali dan langsung dipakai untuk login.
                    'must_change_password' => false,
                    'temp_password_expires_at' => null,
                ]);
            }

            $nomorInduk = KodeUnik::nomorInduk();

            $alamat = $pendaftaran->alamat_domisili ?: collect([
                $pendaftaran->alamat_jalan,
                $pendaftaran->alamat_rt_rw,
                $pendaftaran->alamat_desa_kelurahan,
                $pendaftaran->alamat_kecamatan,
                $pendaftaran->alamat_kabupaten_kota,
                $pendaftaran->alamat_provinsi,
            ])->filter()->implode(', ');

            $dataSantri = [
                'user_id' => $user->id,
                'pendaftaran_online_id' => $pendaftaran->id,
                'nomor_induk' => $nomorInduk,
                'nama_lengkap' => $pendaftaran->nama_lengkap,
                'jenjang_pendidikan' => Jenjang::label($pendaftaran->jenjang_tujuan),
                'kelas' => '1',
                'kamar' => null,
                'status_kelulusan' => Santri::STATUS_AKTIF,
                'alamat' => $alamat ?: 'Asal sekolah: '.$pendaftaran->asal_sekolah,
                'nama_wali' => $namaWali,
            ];

            // Email santri dibawa ke data santri untuk dipakai akun alumni.
            if (Santri::siapEmailSantri()) {
                $dataSantri['email_santri'] = $pendaftaran->email_santri ?: null;
            }

            // Jenis kelamin dari PPDB ikut disalin agar komposisi putra/putri
            // pada dashboard langsung terisi tanpa input ulang.
            if (Santri::siapJenisKelamin()) {
                $dataSantri['jenis_kelamin'] = Santri::normalisasiJenisKelamin($pendaftaran->jenis_kelamin);
            }

            $santri = Santri::create($dataSantri);

            // Dukung ayah & ibu dengan akun berbeda lewat pivot.
            $user->anak()->syncWithoutDetaching([
                $santri->id => ['hubungan' => 'wali', 'utama' => true],
            ]);

            RiwayatKelas::create([
                'santri_id' => $santri->id,
                'jenjang' => $santri->jenjang_pendidikan,
                'kelas' => $santri->kelas,
                'kamar' => $santri->kamar,
                'tahun_ajaran' => now()->month >= 7
                    ? now()->year.'/'.(now()->year + 1)
                    : (now()->year - 1).'/'.now()->year,
                'semester' => '1',
                'status' => 'naik',
                'keterangan' => 'Data awal dari PPDB',
                'diproses_oleh' => auth()->id(),
            ]);

            return ['user' => $user, 'santri' => $santri, 'rawPassword' => $rawPassword];
        });

        $emailTerkirim = false;

        if ($hasil['rawPassword']) {
            try {
                Mail::to($pendaftaran->email, $hasil['user']->name)->send(new KredensialMail(
                    nama: $hasil['user']->name,
                    email: $pendaftaran->email,
                    passwordSementara: $hasil['rawPassword'],
                    peran: 'Wali Santri',
                    judul: 'Akun Portal Wali Santri',
                    catatan: 'Akun ini terhubung dengan santri '.$pendaftaran->nama_lengkap.'.',
                    wajibGanti: false,
                ));
                $emailTerkirim = true;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        CatatanAktivitas::catat('terima_ppdb', $pendaftaran, [
            'santri_id' => $hasil['santri']->id,
            'akun_baru' => (bool) $hasil['rawPassword'],
            'email_terkirim' => $emailTerkirim,
        ]);

        $phone = preg_replace('/\D/', '', $pendaftaran->no_hp_ortu ?: ($pendaftaran->no_hp_aktif ?? ''));
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        $loginUrl = route('login');
        $pesanWa = "Alhamdulillah, Pendaftaran santri atas nama *{$pendaftaran->nama_lengkap}* telah *DITERIMA* di Pesantren Babul Hasanah.\n\n"
            ."Akun Portal Wali Santri: {$pendaftaran->email}\n"
            .($hasil['rawPassword']
                ? "Username dan password telah dikirim ke email wali. Gunakan kredensial tersebut untuk masuk ke portal.\n\n"
                : "Silakan gunakan password akun portal yang sudah ada.\n\n")
            ."Login melalui: {$loginUrl}";

        $waLink = "https://wa.me/{$phone}?text=".urlencode($pesanWa);

        $success = $hasil['rawPassword']
            ? 'Status berhasil diubah menjadi Diterima! Data santri & akun wali dibuat.'
            : 'Status berhasil diubah menjadi Diterima! Santri ditautkan ke akun wali yang sudah ada.';

        if ($hasil['rawPassword'] && ! $emailTerkirim) {
            $success .= ' Namun email kredensial gagal dikirim. Gunakan tombol Reset Password Wali di detail santri.';
        }

        return redirect()->back()->with([
            'success' => $success,
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
            // Email wali sengaja TIDAK unik: satu wali boleh mendaftarkan banyak anak.
            'email' => ['required', 'email', 'max:255'],
            'email_santri' => ['required', 'email', 'max:255', 'different:email'],
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
            'email.required' => 'Email orang tua/wali wajib diisi.',
            'email.email' => 'Format email orang tua/wali tidak valid.',
            'email_santri.required' => 'Email santri wajib diisi untuk akun alumni kelak.',
            'email_santri.email' => 'Format email santri tidak valid.',
            'email_santri.different' => 'Email santri harus berbeda dengan email orang tua/wali.',
            'nama_ortu_wali.required' => 'Nama orang tua/wali wajib diisi.',
            'no_hp_ortu.required' => 'Nomor handphone orang tua wajib diisi.',
            'asal_sekolah.required' => 'Sekolah asal wajib diisi.',
            'alamat_sekolah.required' => 'Alamat sekolah asal wajib diisi.',
            'tahun_lulus.required' => 'Tahun lulus wajib diisi.',
            'tahun_lulus.digits' => 'Tahun lulus harus 4 angka, contoh: 2026.',
            'jenjang_tujuan.required' => 'Pilihan jenjang wajib dipilih.',
            'jenjang_tujuan.in' => 'Pilihan jenjang tidak valid.',
        ]);

        // Pastikan struktur tabel sudah mendukung 13 field PPDB.
        if ($kolomHilang = PendaftaranOnline::kolomFormHilang()) {
            return back()->withInput()->withErrors([
                'database' => PendaftaranOnline::pesanSkemaBelumSiap($kolomHilang),
            ]);
        }

        // Santri hasil verifikasi & akun wali yang tertaut (jika sudah diterima).
        $santri = Santri::where('pendaftaran_online_id', $pendaftaran->id)->first();
        $userTertaut = $santri?->user ?: User::where('email', $pendaftaran->email)->first();

        // Email tidak boleh dipakai akun lain (selain akun wali yang tertaut).
        $emailPemilik = User::where('email', $validated['email'])->first();
        if ($emailPemilik && $emailPemilik->id !== $userTertaut?->id) {
            return back()->withInput()->withErrors([
                'email' => "Email orang tua/wali {$validated['email']} sudah dipakai akun lain. Gunakan email berbeda.",
            ]);
        }

        // Email santri akan jadi username akun alumni. Akun alumni yang sudah
        // terbentuk boleh mempertahankan email yang sama.
        $emailSantri = $validated['email_santri'];
        $pemilikEmailSantri = User::where('email', $emailSantri)->first();
        $pemilikDiperbolehkan = array_filter([
            $userTertaut?->id,
            Kelulusan::siapTautan() ? $santri?->alumni?->user_id : null,
        ]);

        if ($pemilikEmailSantri && ! in_array($pemilikEmailSantri->id, $pemilikDiperbolehkan, true)) {
            return back()->withInput()->withErrors([
                'email_santri' => "Email santri {$emailSantri} sudah dipakai akun lain.",
            ]);
        }

        if (PendaftaranOnline::where('email_santri', $emailSantri)
            ->whereKeyNot($pendaftaran->id)
            ->exists()
        ) {
            return back()->withInput()->withErrors([
                'email_santri' => 'Email santri ini sudah dipakai pendaftaran lain.',
            ]);
        }

        $namaWali = $validated['nama_ortu_wali'] ?: $validated['nama_lengkap'];

        DB::transaction(function () use ($validated, $pendaftaran, $santri, $userTertaut, $namaWali, $emailSantri) {
            $pendaftaran->update([
                'nama_lengkap' => $validated['nama_lengkap'],
                'tempat_tanggal_lahir' => $validated['tempat_tanggal_lahir'],
                'no_hp_aktif' => $validated['no_hp_aktif'],
                'alamat_domisili' => $validated['alamat_domisili'],
                'email' => $validated['email'],
                'email_santri' => $emailSantri,
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
                $userTertaut->update([
                    'name' => $namaWali,
                    'email' => $validated['email'],
                    'phone' => $validated['no_hp_ortu'],
                ]);
            }

            // Sinkronkan data santri yang sudah diverifikasi.
            if ($santri) {
                $dataSantri = [
                    'nama_lengkap' => $validated['nama_lengkap'],
                    'jenjang_pendidikan' => $validated['jenjang_tujuan'],
                    'nama_wali' => $namaWali,
                    'alamat' => $validated['alamat_domisili'],
                ];

                if (Santri::siapEmailSantri()) {
                    $dataSantri['email_santri'] = $emailSantri;
                }

                $santri->update($dataSantri);
            }
        });

        return redirect()
            ->route('admin.pendaftaran.index')
            ->with('success', 'Data pendaftaran berhasil diperbarui.');
    }

    /**
     * Hapus data pendaftaran yang belum pernah diverifikasi.
     *
     * Pendaftar yang sudah menjadi santri (termasuk yang kini berstatus
     * alumni) dipertahankan sebagai arsip PPDB sehingga tidak bisa dihapus.
     */
    public function destroy($id)
    {
        $pendaftaran = PendaftaranOnline::findOrFail($id);
        $this->pastikanJenjang($pendaftaran);

        $santri = Santri::where('pendaftaran_online_id', $pendaftaran->id)->first();

        if ($santri) {
            return back()->with(
                'error',
                'Data pendaftaran '.$pendaftaran->no_pendaftaran.' sudah tertaut Data Santri ('
                .$santri->nama_lengkap.') sehingga disimpan sebagai arsip PPDB dan tidak bisa dihapus. '
                .'Hapus data santri terlebih dahulu bila memang data ini keliru.'
            );
        }

        $pendaftaran->delete();

        return redirect()
            ->route('admin.pendaftaran.index')
            ->with('success', 'Data pendaftaran berhasil dihapus.');
    }
}
