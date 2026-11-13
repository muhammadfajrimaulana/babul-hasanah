<?php

namespace App\Support;

use App\Models\AcademicYear;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Logika bersama Modul PPDB & Plotting Santri.
 *
 * Semua proses yang menyentuh beberapa tabel sekaligus (verifikasi PPDB,
 * pembuatan akun wali, penempatan kelas) dikumpulkan di sini agar controller
 * lama dan halaman baru memakai aturan yang sama.
 */
class Ppdb
{
    /** Slug jenjang resmi dari nilai bebas (label, kode, atau alias). */
    public static function jenjangSlug(?string $nilai): ?string
    {
        return Jenjang::normalisasi($nilai);
    }

    /**
     * Semua penulisan jenjang yang mungkin tersimpan di database untuk satu
     * jenjang (slug, kode, nama, dan alias lama).
     *
     * @return array<int, string>
     */
    public static function variasiJenjang(?string $nilai): array
    {
        $slug = self::jenjangSlug($nilai);

        if (! $slug) {
            return array_values(array_filter([(string) $nilai], fn ($v) => $v !== ''));
        }

        $data = Jenjang::cari($slug) ?: [];
        $kandidat = array_merge(
            [$slug],
            $data['alias'] ?? [],
            [$data['kode'] ?? null, $data['nama'] ?? null]
        );

        return array_values(array_unique(array_filter(
            array_map(fn ($v) => trim((string) $v), $kandidat),
            fn ($v) => $v !== ''
        )));
    }

    /** Email wali yang dipakai untuk akun portal. */
    public static function emailWali(PendaftaranOnline $pendaftaran): string
    {
        return strtolower(trim((string) ($pendaftaran->parent_email ?: $pendaftaran->email)));
    }

    /**
     * Buat akun wali santri berstatus `pending` saat pendaftaran PPDB.
     *
     * Akun belum bisa dipakai login. Kata sandi diisi acak (tidak diketahui
     * siapa pun) dan akan diganti otomatis saat Admin Jenjang memverifikasi
     * pendaftaran (lihat AkunPengguna::aktifkan()).
     *
     * Metode ini tidak pernah melempar exception agar pengiriman formulir
     * PPDB tetap berhasil meskipun pembuatan akun gagal (mis. email sudah
     * dipakai akun peran lain).
     */
    public static function daftarkanAkunWali(PendaftaranOnline $pendaftaran): ?User
    {
        $email = self::emailWali($pendaftaran);

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $namaWali = $pendaftaran->nama_ortu_wali
            ?: ($pendaftaran->nama_ayah ?: ($pendaftaran->nama_ibu ?: $pendaftaran->nama_lengkap));

        try {
            $user = User::withTrashed()->where('email', $email)->first();

            if ($user) {
                if ($user->trashed()) {
                    $user->restore();
                }

                // Email sudah dipakai akun selain wali santri: jangan diubah.
                if ($user->peranSistem() !== 'walisantri') {
                    return null;
                }

                // Segarkan identitas tanpa mengubah status atau kata sandi.
                $user->fill([
                    'name' => $namaWali ?: $user->name,
                    'phone' => $pendaftaran->no_hp_ortu ?: $user->phone,
                ])->save();

                return $user;
            }

            $user = User::create([
                'name' => $namaWali,
                'email' => $email,
                'password' => Hash::make(Str::password(32, symbols: false)),
                'role' => 'wali_santri',
                'status' => 'pending',
                'phone' => $pendaftaran->no_hp_ortu,
            ]);

            RoleSistem::sinkron($user, 'walisantri');

            return $user;
        } catch (Throwable $e) {
            Log::warning('Gagal membuat akun wali santri dari pendaftaran PPDB.', [
                'pendaftaran_id' => $pendaftaran->id,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Terima pendaftar PPDB.
     *
     * Efek samping (dalam satu transaksi):
     * - status pendaftaran menjadi `diterima`;
     * - akun wali Santri dibuat/dipakai ulang (peran Spatie `walisantri`);
     * - akun wali yang masih `pending` diaktifkan dan diberi kata sandi baru;
     * - kata sandi baru dikirim ke email wali (di luar transaksi);
     * - baris `santri` lama dibuat/diperbarui (kompatibilitas portal wali);
     * - baris `students` baru/terbarui dibuat agar bisa diplot ke kelas.
     *
     * @return array{rawPassword: ?string, akunBaru: bool, diaktifkan: bool, emailTerkirim: bool, user: User, student: Student, santri: Santri}
     *
     * @throws RuntimeException bila email/jenjang tidak valid atau sudah dipakai akun lain.
     */
    public static function terima(PendaftaranOnline $pendaftaran): array
    {
        $email = self::emailWali($pendaftaran);

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Email orang tua/wali pendaftar masih kosong atau tidak valid.');
        }

        $slug = self::jenjangSlug($pendaftaran->jenjang_tujuan);

        if (! $slug) {
            throw new RuntimeException('Jenjang tujuan pendaftar tidak dikenali. Perbaiki data pendaftaran terlebih dahulu.');
        }

        if ($pendaftaran->status_pendaftaran === 'diterima'
            && Student::where('pendaftaran_online_id', $pendaftaran->id)->exists()) {
            throw new RuntimeException('Pendaftar ini sudah diterima sebelumnya dan datanya sudah dibuat.');
        }

        $userLama = User::withTrashed()->where('email', $email)->first();

        if ($userLama && $userLama->trashed()) {
            $userLama->restore();
        }

        if ($userLama && $userLama->peranSistem() !== 'walisantri') {
            throw new RuntimeException(
                "Email {$email} sudah dipakai akun lain (peran: {$userLama->labelPeran()}). Minta wali memakai email berbeda."
            );
        }

        $hasil = DB::transaction(function () use ($pendaftaran, $email, $slug, $userLama) {
            $namaWali = $pendaftaran->nama_ortu_wali
                ?: ($pendaftaran->nama_ayah ?: ($pendaftaran->nama_ibu ?: $pendaftaran->nama_lengkap));

            $akunBaru = false;
            $rawPassword = null;
            $diaktifkan = false;

            if ($userLama) {
                $user = $userLama;

                // Segarkan identitas akun yang sudah ada.
                $user->fill([
                    'name' => $namaWali ?: $user->name,
                    'phone' => $pendaftaran->no_hp_ortu ?: $user->phone,
                ])->save();
            } else {
                $akunBaru = true;

                // Kata sandi unik per akun; akan diganti saat aktivasi di bawah.
                $user = User::create([
                    'name' => $namaWali,
                    'email' => $email,
                    'password' => Hash::make(AkunPengguna::passwordAcak()),
                    'role' => 'wali_santri',
                    'status' => 'pending',
                    'phone' => $pendaftaran->no_hp_ortu,
                ]);

                RoleSistem::sinkron($user, 'walisantri');
            }

            // Wali yang mendaftar mandiri masih `pending`: aktifkan sekarang
            // dan buat kata sandi baru. Email dikirim setelah transaksi sukses.
            if (! $user->sudahAktif()) {
                $aktivasi = AkunPengguna::aktifkan($user, null, false, $akunBaru);
                $rawPassword = $aktivasi['password'];
                $diaktifkan = true;
            }

            $alamat = $pendaftaran->alamat_domisili ?: collect([
                $pendaftaran->alamat_jalan,
                $pendaftaran->alamat_rt_rw,
                $pendaftaran->alamat_desa_kelurahan,
                $pendaftaran->alamat_kecamatan,
                $pendaftaran->alamat_kabupaten_kota,
                $pendaftaran->alamat_provinsi,
            ])->filter()->implode(', ');

            $santri = Santri::firstOrNew(['pendaftaran_online_id' => $pendaftaran->id]);

            if (! $santri->exists) {
                do {
                    $nomorInduk = 'SANTRI-'.date('Y').'-'.strtoupper(Str::random(4));
                } while (Santri::where('nomor_induk', $nomorInduk)->exists());

                $santri->nomor_induk = $nomorInduk;
            }

            $santri->fill([
                'user_id' => $user->id,
                'nama_lengkap' => $pendaftaran->nama_lengkap,
                'jenjang_pendidikan' => $pendaftaran->jenjang_tujuan,
                'kelas' => $santri->kelas ?: '1',
                'alamat' => $alamat ?: 'Asal sekolah: '.$pendaftaran->asal_sekolah,
                'nama_wali' => $namaWali,
            ])->save();

            $nisn = trim((string) $pendaftaran->nisn) ?: 'PPDB-'.$pendaftaran->no_pendaftaran;
            $student = Student::withTrashed()->where('nisn', $nisn)->first();

            if ($student) {
                if ($student->trashed()) {
                    $student->restore();
                }

                if ($student->pendaftaran_online_id
                    && (int) $student->pendaftaran_online_id !== (int) $pendaftaran->id) {
                    throw new RuntimeException(
                        "NISN {$nisn} sudah dipakai santri lain dari pendaftaran berbeda. Periksa data NISN pendaftar."
                    );
                }

                if ($student->user_id && (int) $student->user_id !== (int) $user->id) {
                    throw new RuntimeException(
                        "Santri dengan NISN {$nisn} sudah tertaut ke akun wali lain. Periksa data sebelum menerima pendaftar."
                    );
                }
            } else {
                $student = new Student([
                    'nisn' => $nisn,
                    'status' => 'calon',
                ]);
            }

            $student->fill([
                'name' => $pendaftaran->nama_lengkap,
                'parent_email' => $email,
                'jenjang' => $slug,
                'user_id' => $user->id,
                'pendaftaran_online_id' => $pendaftaran->id,
                'santri_id' => $santri->id,
            ]);

            if (! $student->exists) {
                $student->status = 'calon';
            }

            $student->save();

            $pendaftaran->update([
                'status_pendaftaran' => 'diterima',
                'parent_email' => $email,
            ]);

            return [
                'rawPassword' => $rawPassword,
                'akunBaru' => $akunBaru,
                'diaktifkan' => $diaktifkan,
                'emailTerkirim' => false,
                'user' => $user,
                'student' => $student,
                'santri' => $santri,
            ];
        });

        // Kirim kredensial setelah transaksi commit agar email tidak terkirim
        // bila penyimpanan data gagal.
        if ($hasil['diaktifkan'] && $hasil['rawPassword']) {
            $hasil['emailTerkirim'] = AkunPengguna::kirimPassword(
                $hasil['user'],
                $hasil['rawPassword'],
                $hasil['akunBaru'],
            );
        }

        return $hasil;
    }

    /**
     * Tolak pendaftar. Pendaftar yang sudah diterima tidak boleh ditolak agar
     * akun wali & data santri tidak menjadi yatim.
     *
     * @throws RuntimeException
     */
    public static function tolak(PendaftaranOnline $pendaftaran): void
    {
        if ($pendaftaran->status_pendaftaran === 'diterima') {
            throw new RuntimeException('Pendaftar yang sudah diterima tidak bisa ditolak. Hapus data jika memang keliru.');
        }

        $pendaftaran->update(['status_pendaftaran' => 'ditolak']);
    }

    /**
     * Plot satu siswa ke satu kelas.
     *
     * @throws RuntimeException bila jenjang siswa dan kelas tidak sama.
     */
    public static function plot(Student $student, SchoolClass $kelas): Student
    {
        $slugSiswa = self::jenjangSlug($student->jenjang);
        $slugKelas = self::jenjangSlug($kelas->jenjang);

        if ($slugSiswa && $slugKelas && $slugSiswa !== $slugKelas) {
            throw new RuntimeException(
                'Jenjang santri ('.Jenjang::label($student->jenjang).') tidak sama dengan jenjang kelas ('
                .Jenjang::label($kelas->jenjang).').'
            );
        }

        return DB::transaction(function () use ($student, $kelas, $slugKelas) {
            // Satu siswa berada pada satu kelas aktif. Bila kolom tahun ajaran
            // tersedia, penempatan dicatat pada tahun ajaran berjalan.
            $tahunId = self::tahunAjaranAktifId();
            $pivot = $tahunId && Schema::hasColumn('class_student', 'academic_year_id')
                ? [$kelas->id => ['academic_year_id' => $tahunId]]
                : [$kelas->id];

            $student->classes()->sync($pivot);

            $student->forceFill([
                'class_id' => $kelas->id,
                'jenjang' => $slugKelas ?: ($student->jenjang ?: $kelas->jenjang),
                'status' => $student->status === 'calon' ? 'aktif' : $student->status,
            ])->save();

            return $student->refresh();
        });
    }

    /**
     * Plot banyak siswa sekaligus. Baris yang gagal tidak membatalkan baris lain.
     *
     * @param  array<int, int|string>  $studentIds
     * @return array{berhasil:int, dilewati: array<int, array{nama:string, nisn:string, alasan:string}>}
     */
    public static function plotBanyak(array $studentIds, SchoolClass $kelas): array
    {
        $berhasil = 0;
        $dilewati = [];

        $students = Student::whereIn('id', $studentIds)->get()->keyBy('id');

        foreach (array_unique(array_map('intval', $studentIds)) as $id) {
            $student = $students->get($id);

            if (! $student) {
                $dilewati[] = [
                    'nama' => "ID {$id}",
                    'nisn' => '-',
                    'alasan' => 'Data santri tidak ditemukan.',
                ];

                continue;
            }

            try {
                self::plot($student, $kelas);
                $berhasil++;
            } catch (Throwable $e) {
                $dilewati[] = [
                    'nama' => $student->name,
                    'nisn' => $student->nisn,
                    'alasan' => $e->getMessage(),
                ];
            }
        }

        return ['berhasil' => $berhasil, 'dilewati' => $dilewati];
    }

    /**
     * Kelas yang boleh dipilih untuk satu jenjang.
     *
     * @return Collection<int, SchoolClass>
     */
    public static function kelasUntuk(?string $slug): Collection
    {
        $query = SchoolClass::query()->orderBy('jenjang')->orderBy('name');

        if ($slug) {
            $query->whereIn('jenjang', self::variasiJenjang($slug));
        }

        return $query->get();
    }

    /** Id tahun ajaran aktif untuk pivot `class_student` (null bila belum ada). */
    private static function tahunAjaranAktifId(): ?int
    {
        try {
            if (! Schema::hasTable('academic_years')) {
                return null;
            }

            return AcademicYear::berjalan()?->id;
        } catch (Throwable $e) {
            return null;
        }
    }
}
