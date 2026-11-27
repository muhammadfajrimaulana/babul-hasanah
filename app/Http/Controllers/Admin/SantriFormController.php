<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\User;
use App\Support\Akses;
use App\Support\Kelulusan;
use App\Support\KodeUnik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
        Gate::authorize('create', Santri::class);

        $user = auth()->user();
        $jenjangList = self::JENJANG;

        // Admin Jenjang hanya boleh memilih jenjang yang ditanganinya.
        if (Akses::dibatasiJenjang($user)) {
            $jenjangList = [Akses::namaJenjang($user)];
        }

        return view('dashboard.santri.create', [
            'jenjangList' => $jenjangList,
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Santri::class);

        $data = $request->validate([
            'nama_lengkap'       => ['required', 'string', 'max:255'],
            'jenis_kelamin'      => ['nullable', Rule::in(array_keys(Santri::JENIS_KELAMIN))],
            'nomor_induk'        => ['nullable', 'string', 'max:50', 'unique:santri,nomor_induk'],
            'jenjang_pendidikan' => ['required', 'string', 'max:100'],
            'kelas'              => ['nullable', 'string', 'max:50'],
            'nama_wali'          => ['nullable', 'string', 'max:255'],
            'alamat'             => ['nullable', 'string'],
            'email_wali'         => ['nullable', 'email', 'max:255'],
            'email_santri'       => ['nullable', 'email', 'max:255', 'different:email_wali'],
            'no_hp_wali'         => ['nullable', 'string', 'max:20'],
        ]);

        // Admin Jenjang tidak boleh menambah santri di luar jenjangnya.
        Gate::authorize('jenjang', [Santri::class, $data['jenjang_pendidikan']]);
        abort_unless(
            Akses::cocokJenjang(auth()->user(), $data['jenjang_pendidikan']),
            403,
            'Jenjang yang dipilih berada di luar wewenang Anda.'
        );

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

        if (! empty($data['email_santri'])) {
            if (! Santri::siapEmailSantri()) {
                return back()->withInput()->withErrors([
                    'email_santri' => 'Kolom email santri belum tersedia. Jalankan php artisan migrate terlebih dahulu.',
                ]);
            }

            if (User::where('email', $data['email_santri'])->exists()) {
                return back()->withInput()->withErrors([
                    'email_santri' => 'Email santri sudah dipakai akun lain. Gunakan email berbeda.',
                ]);
            }

            if (Santri::where('email_santri', $data['email_santri'])->exists()) {
                return back()->withInput()->withErrors([
                    'email_santri' => 'Email santri sudah dipakai santri lain.',
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
                $nomorInduk = KodeUnik::nomorInduk();
            }

            $dataSantri = [
                'user_id'            => $user?->id,
                'nomor_induk'        => $nomorInduk,
                'nama_lengkap'       => $data['nama_lengkap'],
                'jenjang_pendidikan' => $data['jenjang_pendidikan'],
                'kelas'              => $data['kelas'] ?? null,
                'alamat'             => $data['alamat'] ?? null,
                'nama_wali'          => $data['nama_wali'] ?? null,
            ];

            if (Santri::siapEmailSantri() && ! empty($data['email_santri'])) {
                $dataSantri['email_santri'] = $data['email_santri'];
            }

            if (Santri::siapJenisKelamin()) {
                $dataSantri['jenis_kelamin'] = Santri::normalisasiJenisKelamin($data['jenis_kelamin'] ?? null);
            }

            Santri::create($dataSantri);

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

        Gate::authorize('update', $santri);
        abort_unless(
            Akses::cocokJenjang(auth()->user(), $santri->jenjang_pendidikan),
            403,
            'Data santri ini berada di luar jenjang yang Anda tangani.'
        );

        return view('dashboard.santri.edit', compact('santri'));
    }

    public function update(Request $request, $id)
    {
        $santri = Santri::findOrFail($id);

        Gate::authorize('update', $santri);
        abort_unless(
            Akses::cocokJenjang(auth()->user(), $santri->jenjang_pendidikan),
            403,
            'Data santri ini berada di luar jenjang yang Anda tangani.'
        );

        $data = $request->validate([
            'nomor_induk'        => ['nullable', 'string', 'max:50', 'unique:santri,nomor_induk,' . $santri->id],
            'nama_lengkap'       => ['required', 'string', 'max:255'],
            'jenis_kelamin'      => ['nullable', Rule::in(array_keys(Santri::JENIS_KELAMIN))],
            'jenjang_pendidikan' => ['required', 'string', 'max:100'],
            'kelas'              => ['nullable', 'string', 'max:50'],
            'nama_wali'          => ['nullable', 'string', 'max:255'],
            'email_santri'       => ['nullable', 'email', 'max:255'],
            'alamat'             => ['nullable', 'string'],
        ]);

        if (! empty($data['email_santri'])) {
            if (! Santri::siapEmailSantri()) {
                return back()->withInput()->withErrors([
                    'email_santri' => 'Kolom email santri belum tersedia. Jalankan php artisan migrate terlebih dahulu.',
                ]);
            }

            if (Santri::where('email_santri', $data['email_santri'])
                ->whereKeyNot($santri->id)
                ->exists()
            ) {
                return back()->withInput()->withErrors([
                    'email_santri' => 'Email santri sudah dipakai santri lain.',
                ]);
            }

            // Akun alumni yang sudah ada boleh mempertahankan email yang sama.
            $pemilikEmail = User::where('email', $data['email_santri'])->first();
            $userIdAlumni = Kelulusan::siapTautan() ? $santri->alumni?->user_id : null;

            if ($pemilikEmail && $pemilikEmail->id !== $userIdAlumni) {
                return back()->withInput()->withErrors([
                    'email_santri' => 'Email santri sudah dipakai akun lain.',
                ]);
            }
        }

        // Jenjang baru juga harus tetap di dalam wewenang user.
        Gate::authorize('jenjang', [Santri::class, $data['jenjang_pendidikan']]);
        abort_unless(
            Akses::cocokJenjang(auth()->user(), $data['jenjang_pendidikan']),
            403,
            'Jenjang yang dipilih berada di luar wewenang Anda.'
        );

        if (! Santri::siapEmailSantri()) {
            unset($data['email_santri']);
        }

        if (! Santri::siapJenisKelamin()) {
            unset($data['jenis_kelamin']);
        } else {
            $data['jenis_kelamin'] = Santri::normalisasiJenisKelamin($data['jenis_kelamin'] ?? null);
        }

        $santri->update($data);

        return redirect()
            ->route('admin.santri.detail', $santri->id)
            ->with('success', 'Data santri berhasil diperbarui.');
    }
}
