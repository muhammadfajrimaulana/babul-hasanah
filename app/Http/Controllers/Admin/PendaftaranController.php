<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\PendaftaranOnlineController as PublicPendaftaranController;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // Jika DITOLAK: cukup ubah status, tidak perlu membuat akun
        if ($request->status_pendaftaran === 'ditolak') {
            $pendaftaran->update(['status_pendaftaran' => 'ditolak']);

            return redirect()->back()->with('success', 'Status pendaftaran berhasil diperbarui.');
        }

        // CEK 2: Email wajib ada untuk membuat akun portal (hanya saat diterima)
        if (empty($pendaftaran->email)) {
            return redirect()->back()->with('error', 'Gagal membuat akun: Email pendaftar kosong!');
        }

        // CEK 3: Email yang sudah dipakai akun non-wali tidak boleh dipakai ulang
        $userLama = User::where('email', $pendaftaran->email)->first();

        if ($userLama && $userLama->role !== 'wali_santri') {
            return redirect()->back()->with(
                'error',
                "Gagal: email {$pendaftaran->email} sudah dipakai akun lain (role: {$userLama->role}). Minta wali memakai email berbeda."
            );
        }

        // Semua proses ditulis dalam transaction agar tidak setengah jadi
        $hasil = DB::transaction(function () use ($pendaftaran, $userLama) {
            $pendaftaran->update(['status_pendaftaran' => 'diterima']);

            $namaWali = $pendaftaran->nama_ortu_wali
                ?: ($pendaftaran->nama_ayah ?: ($pendaftaran->nama_ibu ?: $pendaftaran->nama_lengkap));

            // Wali yang sudah punya akun (misalnya mendaftarkan anak kedua)
            // memakai akun lama, tanpa membuat atau mengganti password.
            if ($userLama) {
                $user = $userLama;
                $rawPassword = null;
            } else {
                $rawPassword = 'babul' . rand(1000, 9999);

                $user = User::create([
                    'name'     => $namaWali,
                    'email'    => $pendaftaran->email,
                    'password' => Hash::make($rawPassword),
                    'role'     => 'wali_santri',
                    'phone'    => $pendaftaran->no_hp_ortu,
                ]);
            }

            // Nomor induk: ulangi sampai benar-benar unik
            do {
                $nomorInduk = 'SANTRI-' . date('Y') . '-' . strtoupper(Str::random(4));
            } while (Santri::where('nomor_induk', $nomorInduk)->exists());

            // Alamat lengkap: utamakan kolom domisili baru, lalu fallback ke
            // kolom alamat lama bila data pendaftar masih menggunakan form lama.
            $alamat = $pendaftaran->alamat_domisili ?: collect([
                $pendaftaran->alamat_jalan,
                $pendaftaran->alamat_rt_rw,
                $pendaftaran->alamat_desa_kelurahan,
                $pendaftaran->alamat_kecamatan,
                $pendaftaran->alamat_kabupaten_kota,
                $pendaftaran->alamat_provinsi,
            ])->filter()->implode(', ');

            Santri::create([
                'user_id'               => $user->id,
                'pendaftaran_online_id' => $pendaftaran->id,
                'nomor_induk'           => $nomorInduk,
                'nama_lengkap'          => $pendaftaran->nama_lengkap,
                'jenjang_pendidikan'    => $pendaftaran->jenjang_tujuan,
                'kelas'                 => '1',
                'alamat'                => $alamat ?: 'Asal sekolah: ' . $pendaftaran->asal_sekolah,
                'nama_wali'             => $namaWali,
            ]);

            return ['rawPassword' => $rawPassword];
        });

        // Normalisasi nomor WhatsApp (utamakan nomor orang tua)
        $phone = preg_replace('/\D/', '', $pendaftaran->no_hp_ortu ?: ($pendaftaran->no_hp_aktif ?? ''));
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $loginUrl = route('login');

        $infoLogin = $hasil['rawPassword']
            ? "Email: {$pendaftaran->email}\nPassword: {$hasil['rawPassword']}"
            : "Email: {$pendaftaran->email}\nPassword: sama seperti akun portal Anda sebelumnya";

        // Pesan yang akan otomatis terisi di WhatsApp
        $pesanWa = "Alhamdulillah, Pendaftaran santri atas nama *{$pendaftaran->nama_lengkap}* telah *DITERIMA* di Pesantren Babul Hasanah.\n\nSilakan login ke Portal Wali Santri:\n{$infoLogin}\n\nSilakan login melalui link berikut:\n{$loginUrl}";

        $waLink = "https://wa.me/{$phone}?text=" . urlencode($pesanWa);

        return redirect()->back()->with([
            'success'     => $hasil['rawPassword']
                ? 'Status berhasil diubah menjadi Diterima! Akun telah dibuat.'
                : 'Status berhasil diubah menjadi Diterima! Santri ditautkan ke akun wali yang sudah ada.',
            'wa_link'     => $waLink,
            'nama_santri' => $pendaftaran->nama_lengkap,
        ]);
    }

    /** Form edit data pendaftar (12 field PPDB). */
    public function edit($id)
    {
        $pendaftaran = PendaftaranOnline::findOrFail($id);

        return view('dashboard.pendaftaran.edit', [
            'pendaftaran' => $pendaftaran,
            'jenjangList' => PublicPendaftaranController::JENJANG,
        ]);
    }

    /** Simpan perubahan data pendaftar sekaligus menyelaraskan data santri. */
    public function update(Request $request, $id)
    {
        $pendaftaran = PendaftaranOnline::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap'         => ['required', 'string', 'max:255'],
            'tempat_tanggal_lahir' => ['required', 'string', 'max:255'],
            'no_hp_aktif'          => ['required', 'string', 'max:30'],
            'alamat_domisili'      => ['required', 'string', 'max:1000'],
            'email'                => ['required', 'email', 'max:255', 'unique:pendaftaran_online,email,' . $pendaftaran->id],
            'nama_ortu_wali'       => ['required', 'string', 'max:255'],
            'no_hp_ortu'           => ['required', 'string', 'max:30'],
            'asal_sekolah'         => ['required', 'string', 'max:255'],
            'alamat_sekolah'       => ['required', 'string', 'max:1000'],
            'tahun_lulus'          => ['required', 'digits:4'],
            'nisn'                 => ['nullable', 'string', 'max:20'],
            'jenjang_tujuan'       => ['required', 'in:' . implode(',', PublicPendaftaranController::JENJANG)],
        ], [
            'nama_lengkap.required'         => 'Nama lengkap wajib diisi.',
            'tempat_tanggal_lahir.required' => 'Tempat tanggal lahir wajib diisi.',
            'no_hp_aktif.required'          => 'Nomor handphone aktif wajib diisi.',
            'alamat_domisili.required'      => 'Alamat tinggal (domisili) wajib diisi.',
            'email.required'                => 'Email aktif wajib diisi.',
            'email.unique'                  => 'Email ini sudah dipakai pendaftar lain.',
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
                'email' => "Email {$validated['email']} sudah dipakai akun lain. Gunakan email berbeda.",
            ]);
        }

        $namaWali = $validated['nama_ortu_wali'] ?: $validated['nama_lengkap'];

        DB::transaction(function () use ($validated, $pendaftaran, $santri, $userTertaut, $namaWali) {
            $pendaftaran->update([
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
            ]);

            // Sinkronkan akun portal wali agar login tetap memakai data terbaru.
            if ($userTertaut) {
                $userTertaut->update([
                    'name'  => $namaWali,
                    'email' => $validated['email'],
                    'phone' => $validated['no_hp_ortu'],
                ]);
            }

            // Sinkronkan data santri yang sudah diverifikasi.
            if ($santri) {
                $santri->update([
                    'nama_lengkap'       => $validated['nama_lengkap'],
                    'jenjang_pendidikan' => $validated['jenjang_tujuan'],
                    'nama_wali'          => $namaWali,
                    'alamat'             => $validated['alamat_domisili'],
                ]);
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

        DB::transaction(function () use ($pendaftaran) {
            // Santri yang lahir dari pendaftaran ini ikut dihapus agar tidak
            // menyisakan data yatim; akun portal wali tetap dipertahankan.
            Santri::where('pendaftaran_online_id', $pendaftaran->id)->delete();

            $pendaftaran->delete();
        });

        return redirect()
            ->route('admin.pendaftaran.index')
            ->with('success', 'Data pendaftaran berhasil dihapus.');
    }
}
