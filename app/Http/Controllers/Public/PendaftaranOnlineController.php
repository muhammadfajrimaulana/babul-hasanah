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
            'nama_panggilan' => ['sometimes', 'string', 'max:255'],
            'nik_santri' => ['sometimes', 'string', 'unique:pendaftaran_online,nik_santri'],
            'nisn' => ['sometimes', 'string'],
            'jenis_kelamin' => ['sometimes', 'in:Laki-laki,Perempuan'],
            'tempat_tanggal_lahir' => ['required', 'string'],
            'asal_sekolah' => ['required', 'string'],
            'nik_ortu' => ['sometimes', 'string'],
            'pekerjaan_ortu' => ['sometimes', 'string'],
            'alamat_jalan' => ['sometimes', 'string'],
            'alamat_rt_rw' => ['sometimes', 'string'],
            'alamat_desa_kelurahan' => ['sometimes', 'string'],
            'alamat_kecamatan' => ['sometimes', 'string'],
            'alamat_kabupaten_kota' => ['sometimes', 'string'],
            'alamat_provinsi' => ['sometimes', 'string'],
            'nama_ayah' => ['sometimes', 'string'],
            'nama_ibu' => ['sometimes', 'string'],
            'email' => ['required', 'email', 'unique:pendaftaran_online,email'],
            'no_hp_ortu' => ['required', 'string'],
        ]);

        // Simpan data pendaftaran online saja (Akun belum dibuat!)
        $noPendaftaran = 'PPDB-' . date('Y') . '-' . strtoupper(Str::random(5));

        PendaftaranOnline::create([
            'no_pendaftaran' => $noPendaftaran,
            'nama_lengkap' => $validated['nama_lengkap'],
            'nama_panggilan' => $validated['nama_panggilan'] ?? null,
            'nik_santri' => $validated['nik_santri'] ?? null,
            'nisn' => $validated['nisn'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'tempat_tanggal_lahir' => $validated['tempat_tanggal_lahir'],
            'asal_sekolah' => $validated['asal_sekolah'],
            'nik_ortu' => $validated['nik_ortu'] ?? null,
            'pekerjaan_ortu' => $validated['pekerjaan_ortu'] ?? null,
            'alamat_jalan' => $validated['alamat_jalan'] ?? null,
            'alamat_rt_rw' => $validated['alamat_rt_rw'] ?? null,
            'alamat_desa_kelurahan' => $validated['alamat_desa_kelurahan'] ?? null,
            'alamat_kecamatan' => $validated['alamat_kecamatan'] ?? null,
            'alamat_kabupaten_kota' => $validated['alamat_kabupaten_kota'] ?? null,
            'alamat_provinsi' => $validated['alamat_provinsi'] ?? null,
            'nama_ayah' => $validated['nama_ayah'] ?? null,
            'nama_ibu' => $validated['nama_ibu'] ?? null,
            'email' => $validated['email'],
            'no_hp_ortu' => $validated['no_hp_ortu'],
            'status_pendaftaran' => 'pending',
        ]);

        // Arahkan kembali dengan pesan sukses bahwa pendaftaran sedang diproses
        return redirect()->back()->with('success', 'Pendaftaran berhasil dikirim! Nomor Pendaftaran Anda: ' . $noPendaftaran . '. Silakan menunggu proses verifikasi dari pihak pesantren.');
    }
}