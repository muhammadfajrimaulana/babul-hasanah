<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranOnline;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PendaftaranOnlineController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'unique:pendaftaran_online,nik'],
            'tempat_tanggal_lahir' => ['required', 'string'],
            'jenjang_tujuan' => ['required', 'string'],
            'asal_sekolah' => ['required', 'string'],
            'nama_ortu_wali' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:pendaftaran_online,email'],
            'no_hp_ortu' => ['required', 'string'],
        ]);

        // Simpan data pendaftaran online saja (Akun belum dibuat!)
        $noPendaftaran = 'PPDB-' . date('Y') . '-' . strtoupper(Str::random(5));

        PendaftaranOnline::create([
            'no_pendaftaran' => $noPendaftaran,
            'nama_lengkap' => $validated['nama_lengkap'],
            'nik' => $validated['nik'],
            'tempat_tanggal_lahir' => $validated['tempat_tanggal_lahir'],
            'jenjang_tujuan' => $validated['jenjang_tujuan'],
            'asal_sekolah' => $validated['asal_sekolah'],
            'nama_ortu_wali' => $validated['nama_ortu_wali'],
            'email' => $validated['email'],
            'no_hp_ortu' => $validated['no_hp_ortu'],
            'status_pendaftaran' => 'pending',
        ]);

        // Arahkan kembali dengan pesan sukses bahwa pendaftaran sedang diproses
        return redirect()->back()->with('success', 'Pendaftaran berhasil dikirim! Nomor Pendaftaran Anda: ' . $noPendaftaran . '. Silakan menunggu proses verifikasi dari pihak pesantren.');
    }
}