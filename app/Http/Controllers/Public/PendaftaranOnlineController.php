<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\PendaftaranMasukMail;
use App\Models\PendaftaranOnline;
use App\Models\User;
use App\Support\KodeUnik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PendaftaranOnlineController extends Controller
{
    /** Pilihan jenjang resmi (7 jenjang). */
    public const JENJANG = [
        'MDTA',
        'PPS Wustha',
        'MTs',
        'MAS',
        'PDF Wustha',
        'PDF Ulya',
        "Ma'had 'Aly",
    ];

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tempat_tanggal_lahir' => ['required', 'string', 'max:255'],
            'no_hp_aktif' => ['required', 'string', 'regex:/^[0-9\+\-\s]+$/', 'max:16'],
            'alamat_domisili' => ['required', 'string', 'max:1000'],
            'email' => ['required', 'email', 'max:255'],
            'email_santri' => ['required', 'email', 'max:255', 'different:email'],
            'nama_ortu_wali' => ['required', 'string', 'max:255'],
            'no_hp_ortu' => ['required', 'string', 'regex:/^[0-9\+\-\s]+$/', 'max:16'],
            'asal_sekolah' => ['required', 'string', 'max:255'],
            'alamat_sekolah' => ['required', 'string', 'max:1000'],
            'tahun_lulus' => ['required', 'digits:4'],
            'nisn' => ['nullable', 'string', 'max:20'],
            'jenjang_tujuan' => ['required', 'in:'.implode(',', self::JENJANG)],
            // Honeypot: kolom tersembunyi yang hanya diisi bot.
            'website' => ['prohibited'],
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
            'website.prohibited' => 'Pengiriman formulir ditolak.',
        ]);

        if ($kolomHilang = PendaftaranOnline::kolomFormHilang()) {
            return redirect()->back()->withInput()->withErrors([
                'database' => PendaftaranOnline::pesanSkemaBelumSiap($kolomHilang),
            ]);
        }

        $email = $validated['email'];

        // Email staf/akun non-wali tidak boleh dipakai untuk pendaftaran wali.
        $akunLama = User::query()->where('email', $email)->first();

        if ($akunLama && ! in_array($akunLama->role, ['wali_santri', 'alumni'], true)) {
            return redirect()->back()->withInput()->withErrors([
                'email' => 'Email ini sudah dipakai untuk akun lain. Silakan gunakan email berbeda untuk akun wali santri.',
            ]);
        }

        // Email santri akan jadi username akun alumni, jadi tidak boleh
        // bentrok dengan akun yang sudah ada maupun pendaftaran lain.
        $emailSantri = $validated['email_santri'];

        if (User::query()->where('email', $emailSantri)->exists()) {
            return redirect()->back()->withInput()->withErrors([
                'email_santri' => 'Email santri ini sudah dipakai akun lain. Gunakan email berbeda.',
            ]);
        }

        if (PendaftaranOnline::query()->where('email_santri', $emailSantri)->exists()) {
            return redirect()->back()->withInput()->withErrors([
                'email_santri' => 'Email santri ini sudah dipakai pendaftaran lain.',
            ]);
        }

        $noPendaftaran = KodeUnik::pendaftaran();

        $pendaftaran = PendaftaranOnline::create([
            'no_pendaftaran' => $noPendaftaran,
            'nama_lengkap' => $validated['nama_lengkap'],
            'tempat_tanggal_lahir' => $validated['tempat_tanggal_lahir'],
            'no_hp_aktif' => $validated['no_hp_aktif'],
            'alamat_domisili' => $validated['alamat_domisili'],
            'email' => $email,
            'email_santri' => $emailSantri,
            'nama_ortu_wali' => $validated['nama_ortu_wali'],
            'no_hp_ortu' => $validated['no_hp_ortu'],
            'asal_sekolah' => $validated['asal_sekolah'],
            'alamat_sekolah' => $validated['alamat_sekolah'],
            'tahun_lulus' => $validated['tahun_lulus'],
            'nisn' => $validated['nisn'] ?? null,
            'jenjang_tujuan' => $validated['jenjang_tujuan'],
            'status_pendaftaran' => 'pending',
        ]);

        $emailTerkirim = false;

        try {
            Mail::to($email, $validated['nama_ortu_wali'])->send(new PendaftaranMasukMail($pendaftaran));
            $emailTerkirim = true;
        } catch (\Throwable $e) {
            report($e);
        }

        $pesan = 'Pendaftaran berhasil dikirim! Nomor Pendaftaran Anda: '.$noPendaftaran.'. '
            .'Data Anda masuk ke antrian verifikasi Admin Jenjang '.$validated['jenjang_tujuan'].'. '
            .'Akun Wali Santri akan dibuat otomatis setelah pendaftaran diterima, dan email santri '
            .'dipakai untuk akun alumni saat kelulusan. ';

        $pesan .= $emailTerkirim
            ? 'Konfirmasi telah dikirim ke '.$email.'.'
            : 'Namun email konfirmasi gagal dikirim. Simpan nomor pendaftaran Anda.';

        return redirect()->back()->with('success', $pesan);
    }
}
