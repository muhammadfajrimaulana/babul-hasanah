<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranOnline;
use App\Support\Ppdb;
use Illuminate\Http\Request;
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
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tempat_tanggal_lahir' => ['required', 'string', 'max:255'],
            'no_hp_aktif' => ['required', 'string', 'regex:/^[0-9\+\-\s]+$/', 'max:16'],
            'alamat_domisili' => ['required', 'string', 'max:1000'],
            // Kolom utama email wali. `email` tetap diterima untuk kompatibilitas
            // formulir/tautan lama; keduanya disimpan dengan nilai yang sama.
            'parent_email' => ['nullable', 'email', 'max:255', 'required_without:email'],
            'email' => ['nullable', 'email', 'max:255', 'required_without:parent_email'],
            'nama_ortu_wali' => ['required', 'string', 'max:255'],
            'no_hp_ortu' => ['required', 'string', 'regex:/^[0-9\+\-\s]+$/', 'max:16'],
            'asal_sekolah' => ['required', 'string', 'max:255'],
            'alamat_sekolah' => ['required', 'string', 'max:1000'],
            'tahun_lulus' => ['required', 'digits:4'],
            'nisn' => ['nullable', 'string', 'max:20'],
            'jenjang_tujuan' => ['required', 'in:'.implode(',', self::JENJANG)],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'tempat_tanggal_lahir.required' => 'Tempat tanggal lahir wajib diisi.',
            'no_hp_aktif.required' => 'Nomor handphone aktif wajib diisi.',
            'alamat_domisili.required' => 'Alamat tinggal (domisili) wajib diisi.',
            'parent_email.required_without' => 'Email orang tua/wali wajib diisi.',
            'parent_email.email' => 'Format email orang tua/wali tidak valid.',
            'email.email' => 'Format email orang tua/wali tidak valid.',
            'nama_ortu_wali.required' => 'Nama orang tua/wali wajib diisi.',
            'no_hp_ortu.required' => 'Nomor handphone orang tua wajib diisi.',
            'asal_sekolah.required' => 'Sekolah asal wajib diisi.',
            'alamat_sekolah.required' => 'Alamat sekolah asal wajib diisi.',
            'tahun_lulus.required' => 'Tahun lulus wajib diisi.',
            'tahun_lulus.digits' => 'Tahun lulus harus 4 angka, contoh: 2026.',
            'jenjang_tujuan.required' => 'Pilihan jenjang wajib dipilih.',
            'jenjang_tujuan.in' => 'Pilihan jenjang tidak valid.',
        ]);

        $emailWali = strtolower(trim((string) ($validated['parent_email'] ?: $validated['email'])));

        // Pastikan struktur tabel sudah mendukung 12 field PPDB.
        if ($kolomHilang = PendaftaranOnline::kolomFormHilang()) {
            return redirect()->back()->withInput()->withErrors([
                'database' => PendaftaranOnline::pesanSkemaBelumSiap($kolomHilang),
            ]);
        }

        // Nomor pendaftaran unik.
        do {
            $noPendaftaran = 'PPDB-'.date('Y').'-'.strtoupper(Str::random(5));
        } while (PendaftaranOnline::where('no_pendaftaran', $noPendaftaran)->exists());

        $pendaftaran = PendaftaranOnline::create([
            'no_pendaftaran' => $noPendaftaran,
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
            'status_pendaftaran' => 'pending',
        ]);

        // Buat akun portal wali berstatus PENDING. Akun belum bisa dipakai
        // login sampai Admin Jenjang memverifikasi pendaftaran; kata sandi
        // dibuat otomatis dan dikirim ke email pada saat verifikasi.
        $akunWali = Ppdb::daftarkanAkunWali($pendaftaran);

        $pesan = 'Pendaftaran berhasil dikirim! Nomor Pendaftaran Anda: '.$noPendaftaran.'.';

        if ($akunWali) {
            if ($akunWali->menungguVerifikasi()) {
                $pesan .= ' Akun portal wali santri Anda berstatus PENDING dan akan aktif otomatis'
                    .' setelah data diverifikasi oleh Admin Jenjang. Kata sandi akan dikirim ke email '
                    .$akunWali->email.'.';
            } else {
                $pesan .= ' Akun portal wali santri Anda sudah aktif. Gunakan email '.$akunWali->email
                    .' dan kata sandi yang pernah dikirimkan untuk memantau data anak Anda.';
            }
        } else {
            $pesan .= ' Silakan menunggu proses verifikasi dari pihak pesantren.'
                .' Pastikan email orang tua/wali aktif untuk menerima informasi akun.';
        }

        return redirect()->back()->with('success', $pesan);
    }
}
