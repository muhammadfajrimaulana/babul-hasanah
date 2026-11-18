<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\PendaftaranBerhasilMail;
use App\Models\PendaftaranOnline;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PendaftaranOnlineController extends Controller
{
    /** Pilihan jenjang yang tersedia di form PPDB. */
    public const JENJANG = [
        'MTs',
        'MA',
        'Madrasah Diniyah Takmiliyah',
        'PPS Wustha',
        'Diniyah Formal Wustha',
        'Diniyah Formal Ulya',
        "Ma'had 'Aly",
    ];

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap'         => ['required', 'string', 'max:255'],
            'tempat_tanggal_lahir' => ['required', 'string', 'max:255'],
            'no_hp_aktif'          => ['required', 'string', 'regex:/^[0-9\+\-\s]+$/', 'max:16'],
            'alamat_domisili'      => ['required', 'string', 'max:1000'],
            'email'                => ['required', 'email', 'max:255'], // multi-anak: email boleh dipakai beberapa pendaftaran
            'nama_ortu_wali'       => ['required', 'string', 'max:255'],
            'no_hp_ortu'           => ['required', 'string', 'regex:/^[0-9\+\-\s]+$/', 'max:16'],
            'asal_sekolah'         => ['required', 'string', 'max:255'],
            'alamat_sekolah'       => ['required', 'string', 'max:1000'],
            'tahun_lulus'          => ['required', 'digits:4'],
            'nisn'                 => ['nullable', 'string', 'max:20'],
            'jenjang_tujuan'       => ['required', 'in:' . implode(',', self::JENJANG)],
        ], [
            'nama_lengkap.required'         => 'Nama lengkap wajib diisi.',
            'tempat_tanggal_lahir.required' => 'Tempat tanggal lahir wajib diisi.',
            'no_hp_aktif.required'          => 'Nomor handphone aktif wajib diisi.',
            'alamat_domisili.required'      => 'Alamat tinggal (domisili) wajib diisi.',
            'email.required'                => 'Email orang tua/wali wajib diisi.',
            'email.email'                   => 'Format email orang tua/wali tidak valid.',
            'nama_ortu_wali.required'       => 'Nama orang tua/wali wajib diisi.',
            'no_hp_ortu.required'           => 'Nomor handphone orang tua wajib diisi.',
            'asal_sekolah.required'         => 'Sekolah asal wajib diisi.',
            'alamat_sekolah.required'       => 'Alamat sekolah asal wajib diisi.',
            'tahun_lulus.required'          => 'Tahun lulus wajib diisi.',
            'tahun_lulus.digits'            => 'Tahun lulus harus 4 angka, contoh: 2026.',
            'jenjang_tujuan.required'       => 'Pilihan jenjang wajib dipilih.',
            'jenjang_tujuan.in'             => 'Pilihan jenjang tidak valid.',
        ]);

        // Pastikan struktur tabel sudah mendukung 12 field PPDB.
        if ($kolomHilang = PendaftaranOnline::kolomFormHilang()) {
            return redirect()->back()->withInput()->withErrors([
                'database' => PendaftaranOnline::pesanSkemaBelumSiap($kolomHilang),
            ]);
        }

        $email = $validated['email'];
        $namaWali = $validated['nama_ortu_wali'] ?: $validated['nama_lengkap'];

        // Email yang sudah terpakai akun non-wali tidak boleh dipakai untuk akun wali.
        $akunLama = User::query()->where('email', $email)->first();

        if ($akunLama && $akunLama->role !== 'wali_santri') {
            return redirect()->back()->withInput()->withErrors([
                'email' => 'Email ini sudah dipakai untuk akun lain. Silakan gunakan email berbeda untuk akun wali santri.',
            ]);
        }

        // Nomor pendaftaran unik.
        do {
            $noPendaftaran = 'PPDB-' . date('Y') . '-' . strtoupper(Str::random(5));
        } while (PendaftaranOnline::where('no_pendaftaran', $noPendaftaran)->exists());

        // Simpan pendaftaran + buat akun portal wali (jika email belum punya akun).
        [$pendaftaran, $akunBaru, $kataSandi] = DB::transaction(function () use ($validated, $noPendaftaran, $akunLama, $email, $namaWali) {
            $pendaftaran = PendaftaranOnline::create([
                'no_pendaftaran'       => $noPendaftaran,
                'nama_lengkap'         => $validated['nama_lengkap'],
                'tempat_tanggal_lahir' => $validated['tempat_tanggal_lahir'],
                'no_hp_aktif'          => $validated['no_hp_aktif'],
                'alamat_domisili'      => $validated['alamat_domisili'],
                'email'                => $email,
                'nama_ortu_wali'       => $validated['nama_ortu_wali'],
                'no_hp_ortu'           => $validated['no_hp_ortu'],
                'asal_sekolah'         => $validated['asal_sekolah'],
                'alamat_sekolah'       => $validated['alamat_sekolah'],
                'tahun_lulus'          => $validated['tahun_lulus'],
                'nisn'                 => $validated['nisn'] ?? null,
                'jenjang_tujuan'       => $validated['jenjang_tujuan'],
                'status_pendaftaran'   => 'pending',
            ]);

            $akunBaru = false;
            $kataSandi = null;

            // Email yang sudah punya akun wali (misalnya mendaftarkan anak kedua)
            // memakai akun lama; kata sandi tidak diubah.
            if (! $akunLama) {
                $kataSandi = Str::password(8, symbols: false);
                $akunBaru = true;

                User::create([
                    'name'     => $namaWali,
                    'email'    => $email,
                    'password' => Hash::make($kataSandi),
                    'role'     => 'wali_santri',
                    'phone'    => $validated['no_hp_ortu'],
                ]);
            }

            return [$pendaftaran, $akunBaru, $kataSandi];
        });

        // Kirim notifikasi email berisi username & kata sandi (akun baru).
        // Kegagalan email tidak membatalkan pendaftaran yang sudah tersimpan.
        $emailTerkirim = false;

        try {
            Mail::to($email, $namaWali)->send(
                new PendaftaranBerhasilMail($pendaftaran, $kataSandi, $akunBaru)
            );
            $emailTerkirim = true;
        } catch (\Throwable $e) {
            report($e);
        }

        $pesan = 'Pendaftaran berhasil dikirim! Nomor Pendaftaran Anda: ' . $noPendaftaran . '. ';

        if ($akunBaru) {
            $pesan .= $emailTerkirim
                ? 'Username dan kata sandi Portal Wali Santri telah dikirim ke ' . $email . '. Silakan periksa kotak masuk atau folder spam. '
                : 'Akun Portal Wali Santri berhasil dibuat, tetapi email kredensial gagal dikirim. Silakan hubungi Sekretariat Pesantren untuk mendapatkan akses login. ';
        } else {
            $pesan .= 'Akun Portal Wali Santri dengan email ini sudah aktif; gunakan kata sandi yang sudah ada atau fitur Lupa Kata Sandi. ';
        }

        $pesan .= 'Silakan menunggu proses verifikasi dari pihak pesantren.';

        return redirect()->back()->with('success', $pesan);
    }
}
