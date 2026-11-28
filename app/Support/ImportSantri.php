<?php

namespace App\Support;

use App\Mail\KredensialMail;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Import data santri dari CSV tanpa dependency package.
 *
 * Kolom boleh ditulis dengan nama kunci (snake_case) atau label Indonesia.
 * Baris yang tidak valid dilewati satu per satu dan dilaporkan, sehingga
 * berkas berisi ratusan santri tetap bisa diproses sekaligus.
 */
class ImportSantri
{
    /** @var array<string, array<int, string>> */
    private const KOLOM = [
        'nama_lengkap'       => ['nama_lengkap', 'nama', 'nama_santri'],
        'jenis_kelamin'      => ['jenis_kelamin', 'jenis_kelamin_santri', 'kelamin', 'gender'],
        'nomor_induk'        => ['nomor_induk', 'nis', 'nip', 'nomor_induk_santri'],
        'jenjang_pendidikan' => ['jenjang_pendidikan', 'jenjang', 'jenjang_tujuan'],
        'kelas'              => ['kelas'],
        'nama_wali'          => ['nama_wali', 'nama_orang_tua', 'wali'],
        'alamat'             => ['alamat'],
        'email_wali'         => ['email_wali', 'email_orang_tua', 'email'],
        'email_santri'       => ['email_santri', 'email_santri_login'],
        'no_hp_wali'         => ['no_hp_wali', 'no_hp', 'telepon_wali', 'whatsapp'],
    ];

    /**
     * Header resmi untuk berkas template.
     *
     * @return array<int, string>
     */
    public static function kolomTemplate(): array
    {
        return array_keys(self::KOLOM);
    }

    /**
     * Proses berkas CSV.
     *
     * @return array{berhasil: int, gagal: int, akunWaliBaru: int, emailGagal: int, errors: array<int, string>}
     */
    public static function proses(string $path, ?User $operator): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new \RuntimeException('Berkas CSV tidak dapat dibaca.');
        }

        $hasil = [
            'berhasil'     => 0,
            'gagal'        => 0,
            'akunWaliBaru' => 0,
            'emailGagal'   => 0,
            'errors'       => [],
        ];

        try {
            $barisPertama = fgets($handle);
            if ($barisPertama === false || trim($barisPertama) === '') {
                throw new \RuntimeException('Berkas CSV kosong.');
            }

            // CSV dari Excel Windows memakai titik koma; dari tooling lain koma.
            $pemisah = substr_count($barisPertama, ';') > substr_count($barisPertama, ',') ? ';' : ',';
            rewind($handle);

            $header = fgetcsv($handle, 0, $pemisah);
            if (! $header) {
                throw new \RuntimeException('Baris header CSV tidak terbaca.');
            }

            $peta = [];
            foreach (self::KOLOM as $kunci => $alias) {
                $aliasBersih = array_map([self::class, 'bersihkan'], $alias);

                foreach ($header as $indeks => $nama) {
                    if (in_array(self::bersihkan((string) $nama), $aliasBersih, true)) {
                        $peta[$kunci] = $indeks;
                        break;
                    }
                }
            }

            foreach (['nama_lengkap', 'jenjang_pendidikan'] as $wajib) {
                if (! array_key_exists($wajib, $peta)) {
                    throw new \RuntimeException("Kolom wajib {$wajib} tidak ditemukan pada header CSV.");
                }
            }

            $siapEmailSantri = Santri::siapEmailSantri();
            $nomorMap = [];
            // Unique index tetap dipakai baris soft delete, jadi ikut dimasukkan.
            foreach (Santri::withTrashed()->whereNotNull('nomor_induk')->pluck('nomor_induk') as $nomor) {
                $nomorMap[strtoupper(trim((string) $nomor))] = true;
            }

            $emailSantriMap = [];
            if ($siapEmailSantri) {
                foreach (Santri::whereNotNull('email_santri')->pluck('email_santri') as $email) {
                    $emailSantriMap[strtolower(trim((string) $email))] = true;
                }
            }

            $emailAkunMap = [];
            foreach (User::whereNotNull('email')->pluck('email') as $email) {
                $emailAkunMap[strtolower(trim((string) $email))] = true;
            }

            $baris = 1;
            while (($row = fgetcsv($handle, 0, $pemisah)) !== false) {
                $baris++;

                $kosong = collect($row)->every(fn ($nilai) => trim((string) $nilai) === '');
                if ($kosong) {
                    continue;
                }

                $data = [];
                foreach ($peta as $kunci => $indeks) {
                    $nilai = trim((string) ($row[$indeks] ?? ''));
                    $data[$kunci] = $nilai === '' ? null : $nilai;
                }

                if (array_key_exists('jenis_kelamin', $data)) {
                    $data['jenis_kelamin'] = Santri::normalisasiJenisKelamin($data['jenis_kelamin']);
                }

                $validator = Validator::make($data, [
                    'nama_lengkap'       => ['required', 'string', 'max:255'],
                    'jenis_kelamin'      => ['nullable', 'in:L,P'],
                    'nomor_induk'        => ['nullable', 'string', 'max:50', 'unique:santri,nomor_induk'],
                    'jenjang_pendidikan' => ['required', 'string', 'max:100'],
                    'kelas'              => ['nullable', 'string', 'max:50'],
                    'nama_wali'          => ['nullable', 'string', 'max:255'],
                    'alamat'             => ['nullable', 'string'],
                    'email_wali'         => ['nullable', 'email', 'max:255'],
                    'email_santri'       => ['nullable', 'email', 'max:255', 'different:email_wali'],
                    'no_hp_wali'         => ['nullable', 'string', 'max:20'],
                ]);

                if ($validator->fails()) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: ".$validator->errors()->first();
                    continue;
                }

                // Jenjang diseragamkan ke nama resmi agar filter Admin Jenjang bekerja.
                $slug = Jenjang::normalisasi($data['jenjang_pendidikan']);
                if (! $slug) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: jenjang \"{$data['jenjang_pendidikan']}\" tidak dikenali.";
                    continue;
                }

                $data['jenjang_pendidikan'] = Jenjang::cari($slug)['nama'];

                if (! Akses::cocokJenjang($operator, $data['jenjang_pendidikan'])) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: jenjang di luar wewenang Anda.";
                    continue;
                }

                $nomorInduk = $data['nomor_induk'];
                $kunciNomor = $nomorInduk ? strtoupper($nomorInduk) : null;

                if ($kunciNomor && isset($nomorMap[$kunciNomor])) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: nomor induk {$nomorInduk} sudah dipakai.";
                    continue;
                }

                if (! $kunciNomor) {
                    $nomorInduk = KodeUnik::nomorInduk(
                        null,
                        fn (string $kode): bool => ! isset($nomorMap[strtoupper($kode)])
                    );
                    $kunciNomor = strtoupper($nomorInduk);
                }

                if (! empty($data['email_santri'])) {
                    $data['email_santri'] = strtolower($data['email_santri']);

                    if (! $siapEmailSantri) {
                        $hasil['gagal']++;
                        $hasil['errors'][] = "Baris {$baris}: kolom email_santri belum tersedia. Jalankan php artisan migrate terlebih dahulu.";
                        continue;
                    }

                    if (isset($emailAkunMap[$data['email_santri']]) || isset($emailSantriMap[$data['email_santri']])) {
                        $hasil['gagal']++;
                        $hasil['errors'][] = "Baris {$baris}: email santri {$data['email_santri']} sudah dipakai.";
                        continue;
                    }
                }

                $userWali = null;
                if (! empty($data['email_wali'])) {
                    $data['email_wali'] = strtolower($data['email_wali']);

                    if (isset($emailAkunMap[$data['email_wali']])) {
                        $userWali = User::where('email', $data['email_wali'])->first();

                        if (! $userWali || $userWali->role !== 'wali_santri') {
                            $hasil['gagal']++;
                            $hasil['errors'][] = "Baris {$baris}: email wali sudah dipakai akun dengan role lain.";
                            continue;
                        }
                    }
                }

                $kredensialBaru = null;

                try {
                    DB::transaction(function () use ($data, $nomorInduk, $siapEmailSantri, &$userWali, &$kredensialBaru) {
                        if ($data['email_wali'] && ! $userWali) {
                            $password = Str::password(10, symbols: false);

                            $userWali = User::create([
                                'name'                    => $data['nama_wali'] ?: $data['nama_lengkap'],
                                'email'                   => $data['email_wali'],
                                'password'                => Hash::make($password),
                                'role'                    => 'wali_santri',
                                'phone'                   => $data['no_hp_wali'] ?? null,
                                'is_active'               => true,
                                // Sama seperti PPDB: kredensial di email langsung dipakai.
                                'must_change_password'    => false,
                                'temp_password_expires_at' => null,
                            ]);

                            $kredensialBaru = ['user' => $userWali, 'password' => $password];
                        }

                        $dataSantri = [
                            'user_id'            => $userWali?->id,
                            'nomor_induk'        => $nomorInduk,
                            'nama_lengkap'       => $data['nama_lengkap'],
                            'jenjang_pendidikan' => $data['jenjang_pendidikan'],
                            'kelas'              => $data['kelas'] ?? null,
                            'alamat'             => $data['alamat'] ?? null,
                            'nama_wali'          => $data['nama_wali'] ?? null,
                        ];

                        if ($siapEmailSantri && ! empty($data['email_santri'])) {
                            $dataSantri['email_santri'] = $data['email_santri'];
                        }

                        if (Santri::siapJenisKelamin()) {
                            $dataSantri['jenis_kelamin'] = $data['jenis_kelamin'] ?? null;
                        }

                        Santri::create($dataSantri);
                    });
                } catch (\Throwable $e) {
                    $hasil['gagal']++;
                    $hasil['errors'][] = "Baris {$baris}: gagal menyimpan (".$e->getMessage().')';
                    continue;
                }

                $nomorMap[$kunciNomor] = true;
                if (! empty($data['email_santri'])) {
                    $emailSantriMap[$data['email_santri']] = true;
                }
                if ($data['email_wali']) {
                    $emailAkunMap[$data['email_wali']] = true;
                }

                $hasil['berhasil']++;

                if ($kredensialBaru) {
                    $hasil['akunWaliBaru']++;

                    try {
                        Mail::to($kredensialBaru['user']->email, $kredensialBaru['user']->name)
                            ->send(new KredensialMail(
                                nama: $kredensialBaru['user']->name,
                                email: $kredensialBaru['user']->email,
                                passwordSementara: $kredensialBaru['password'],
                                peran: 'Wali Santri',
                                judul: 'Akun Portal Wali Santri',
                                catatan: 'Akun ini terhubung dengan santri hasil import.',
                                wajibGanti: false,
                            ));
                    } catch (\Throwable $e) {
                        $hasil['emailGagal']++;
                    }
                }
            }
        } finally {
            fclose($handle);
        }

        return $hasil;
    }

    /** Normalisasi header: BOM dihapus, tanda baca jadi underscore. */
    private static function bersihkan(?string $nilai): string
    {
        $nilai = str_replace("\xEF\xBB\xBF", '', (string) $nilai);
        $nilai = strtolower($nilai);
        $nilai = preg_replace('/[^a-z0-9]+/', '_', $nilai);

        return trim((string) $nilai, '_');
    }
}
