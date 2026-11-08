<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranOnline;
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
            'nama_lengkap'         => ['required', 'string', 'max:255'],
            'tempat_tanggal_lahir' => ['required', 'string', 'max:255'],
            'no_hp_aktif'          => ['required', 'string', 'max:30'],
            'alamat_domisili'      => ['required', 'string', 'max:1000'],
            'email'                => ['required', 'email', 'max:255', 'unique:pendaftaran_online,email'],
            'nama_ortu_wali'       => ['required', 'string', 'max:255'],
            'no_hp_ortu'           => ['required', 'string', 'max:30'],
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
            'email.unique'                  => 'Email orang tua/wali ini sudah pernah dipakai untuk mendaftar.',
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

        // Nomor pendaftaran unik.
        do {
            $noPendaftaran = 'PPDB-' . date('Y') . '-' . strtoupper(Str::random(5));
        } while (PendaftaranOnline::where('no_pendaftaran', $noPendaftaran)->exists());

        PendaftaranOnline::create([
            'no_pendaftaran'       => $noPendaftaran,
            'nama_lengkap'         => $validated['nama_lengkap'],
            'tempat_tanggal_lahir' => $validated['tempat_tanggal_lahir'],
            'no_hp_aktif'          => $validated['no_hp_aktif'],
            'alamat_domisili'      => $validated['alamat_domisili'],
            'email'                => $validated['email'],
            'nama_ortu_wali'       => $validated['nama_ortu_wali'],
            'no_hp_ortu'           => $validated['no_hp_ortu'],
            'asal_sekolah'         => $validated['asal_sekolah'],
            'alamat_sekolah'       => $validated['alamat_sekolah'],
            'tahun_lulus'          => $validated['tahun_lulus'],
            'nisn'                 => $validated['nisn'] ?? null,
            'jenjang_tujuan'       => $validated['jenjang_tujuan'],
            'status_pendaftaran'   => 'pending',
        ]);

        return redirect()->back()->with(
            'success',
            'Pendaftaran berhasil dikirim! Nomor Pendaftaran Anda: ' . $noPendaftaran
            . '. Silakan menunggu proses verifikasi dari pihak pesantren.'
        );
    }
}
