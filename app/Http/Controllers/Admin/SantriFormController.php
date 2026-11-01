<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Input manual santri (tombol "Tambah Santri Baru") serta edit/update.
 * Import Excel tetap ditangani SantriController yang sudah ada.
 */
class SantriFormController extends Controller
{
    private const JENJANG = [
        'MTs',
        'MA',
        'Madrasah Diniyah Takmiliyah',
        'PPS Wustha',
        'Diniyah Formal Wustha',
        'Diniyah Formal Ulya',
        "Ma'had 'Aly",
    ];

    public function create()
    {
        return view('dashboard.santri.create', [
            'jenjangList' => self::JENJANG,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap'       => ['required', 'string', 'max:255'],
            'nomor_induk'        => ['nullable', 'string', 'max:50', 'unique:santri,nomor_induk'],
            'jenjang_pendidikan' => ['required', 'string', 'max:100'],
            'kelas'              => ['nullable', 'string', 'max:50'],
            'nama_wali'          => ['nullable', 'string', 'max:255'],
            'alamat'             => ['nullable', 'string'],
            'email_wali'         => ['nullable', 'email', 'max:255'],
            'no_hp_wali'         => ['nullable', 'string', 'max:20'],
        ]);

        // Jika email wali diisi, cek apakah sudah dipakai akun lain
        $userLama = null;
        if (! empty($data['email_wali'])) {
            $userLama = User::where('email', $data['email_wali'])->first();

            if ($userLama && $userLama->role !== 'wali_santri') {
                return back()->withInput()->withErrors([
                    'email_wali' => "Email ini sudah dipakai akun lain (role: {$userLama->role}). Gunakan email berbeda.",
                ]);
            }
        }

        $hasil = DB::transaction(function () use ($data, $userLama) {
            $user = null;
            $rawPassword = null;

            if (! empty($data['email_wali'])) {
                if ($userLama) {
                    // Wali sudah punya akun (misalnya anak kedua): tautkan saja
                    $user = $userLama;
                } else {
                    $rawPassword = 'babul' . Str::random(6);

                    $user = User::create([
                        'name'     => $data['nama_wali'] ?: $data['nama_lengkap'],
                        'email'    => $data['email_wali'],
                        'password' => Hash::make($rawPassword),
                        'role'     => 'wali_santri',
                        'phone'    => $data['no_hp_wali'] ?? null,
                    ]);
                }
            }

            // Nomor induk otomatis jika dikosongkan admin
            $nomorInduk = $data['nomor_induk'] ?? null;
            if (! $nomorInduk) {
                do {
                    $nomorInduk = 'SANTRI-' . date('Y') . '-' . strtoupper(Str::random(4));
                } while (Santri::where('nomor_induk', $nomorInduk)->exists());
            }

            Santri::create([
                'user_id'            => $user?->id,
                'nomor_induk'        => $nomorInduk,
                'nama_lengkap'       => $data['nama_lengkap'],
                'jenjang_pendidikan' => $data['jenjang_pendidikan'],
                'kelas'              => $data['kelas'] ?? null,
                'alamat'             => $data['alamat'] ?? null,
                'nama_wali'          => $data['nama_wali'] ?? null,
            ]);

            return ['rawPassword' => $rawPassword];
        });

        $flash = [
            'success' => 'Santri baru berhasil ditambahkan.',
        ];

        // Link WhatsApp hanya dibuat jika akun wali baru dibuat dan nomor HP ada
        if ($hasil['rawPassword'] && ! empty($data['no_hp_wali'])) {
            $phone = preg_replace('/\D/', '', $data['no_hp_wali']);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }

            $pesan = "Assalamu'alaikum, data santri atas nama *{$data['nama_lengkap']}* telah terdaftar di Pesantren Babul Hasanah.\n\n"
                . "Silakan login ke Portal Wali Santri:\n"
                . "Email: {$data['email_wali']}\n"
                . "Password: {$hasil['rawPassword']}\n\n"
                . "Link login:\n" . route('login');

            $flash['wa_link'] = "https://wa.me/{$phone}?text=" . urlencode($pesan);
            $flash['nama_santri'] = $data['nama_lengkap'];
            $flash['success'] .= ' Akun portal wali telah dibuat.';
        }

        return redirect()->route('admin.santri.index')->with($flash);
    }

    public function edit($id)
    {
        $santri = Santri::findOrFail($id);

        return view('dashboard.santri.edit', compact('santri'));
    }

    public function update(Request $request, $id)
    {
        $santri = Santri::findOrFail($id);

        $data = $request->validate([
            'nomor_induk'        => ['nullable', 'string', 'max:50', 'unique:santri,nomor_induk,' . $santri->id],
            'nama_lengkap'       => ['required', 'string', 'max:255'],
            'jenjang_pendidikan' => ['required', 'string', 'max:100'],
            'kelas'              => ['nullable', 'string', 'max:50'],
            'nama_wali'          => ['nullable', 'string', 'max:255'],
            'alamat'             => ['nullable', 'string'],
        ]);

        $santri->update($data);

        return redirect()
            ->route('admin.santri.detail', $santri->id)
            ->with('success', 'Data santri berhasil diperbarui.');
    }
}
