<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PendaftaranController extends Controller
{
    public function index()
    {
        $pendaftar = PendaftaranOnline::latest()->get();
        return view('dashboard.pendaftaran.index', compact('pendaftar'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_pendaftaran' => ['required', 'in:diterima,ditolak'],
        ]);

        $pendaftaran = PendaftaranOnline::findOrFail($id);

        // CEK 1: Jika sudah pernah diterima sebelumnya, tolak proses duplikasi
        if ($pendaftaran->status_pendaftaran === 'diterima') {
            return redirect()->back()->with('error', 'Pendaftar ini sudah diterima sebelumnya dan akunnya sudah dibuat!');
        }

        // CEK 2: Pastikan email tidak kosong sebelum membuat akun
        if (empty($pendaftaran->email)) {
            return redirect()->back()->with('error', 'Gagal membuat akun: Email pendaftar kosong!');
        }

        $pendaftaran->status_pendaftaran = $request->status_pendaftaran;
        $pendaftaran->save();

        // JIKA DITERIMA: Baru buatkan akun user & masukkan ke tabel santri
        if ($request->status_pendaftaran === 'diterima') {

            $rawPassword = 'babul' . rand(1000, 9999);

            $user = User::create([
                'name' => $pendaftaran->nama_ortu_wali,
                'email' => $pendaftaran->email,
                'password' => Hash::make($rawPassword),
                'role' => 'wali_santri',
                'phone' => $pendaftaran->no_hp_ortu,
            ]);

            Santri::create([
                'user_id' => $user->id,
                'nomor_induk' => 'SANTRI-' . date('Y') . '-' . strtoupper(Str::random(4)),
                'nama_lengkap' => $pendaftaran->nama_lengkap,
                'jenjang_pendidikan' => $pendaftaran->jenjang_tujuan,
                'kelas' => '1',
                'alamat' => 'Asal sekolah: ' . $pendaftaran->asal_sekolah,
                'nama_wali' => $pendaftaran->nama_ortu_wali,
            ]);

            $phone = $pendaftaran->no_hp_ortu;
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }

            $loginUrl = route('login');

            // Pesan yang akan otomatis terisi di WhatsApp
            $pesanWa = "Alhamdulillah, Pendaftaran santri atas nama *{$pendaftaran->nama_lengkap}* telah *DITERIMA* di Pesantren Babul Hasanah.\n\nSilakan login ke Portal Wali Santri:\n Email: {$pendaftaran->email}\n Password: {$rawPassword}\n\nSilakan login melalui link login ke portal wali santri berikut ini\n{$loginUrl}\n\n.";

            $waLink = "https://wa.me/{$phone}?text=" . urlencode($pesanWa);

            return redirect()->back()->with([
                'success' => 'Status berhasil diubah menjadi Diterima! Akun telah dibuat.',
                'wa_link' => $waLink,
                'nama_santri' => $pendaftaran->nama_lengkap
            ]);
        }

        return redirect()->back()->with('success', 'Status pendaftaran berhasil diperbarui.');
    }
}