<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Support\Jenjang;
use App\Support\Ppdb;
use App\Support\RoleSistem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
use RuntimeException;
use Throwable;

/**
 * Import data santri dari Excel/CSV ke tabel `students`.
 *
 * Kolom wajib (baris pertama = judul):
 *   nisn | nama_santri | jenjang | parent_email
 *
 * Aturan aman:
 * - NISN menjadi kunci; baris yang sudah ada diperbarui, bukan diduplikasi.
 * - NISN ganda di dalam satu file ditolak.
 * - Admin jenjang hanya boleh mengimpor jenjang yang menjadi tanggung jawabnya.
 * - Email wali yang sudah dipakai akun non-wali ditolak.
 * - Santri yang sudah tertaut ke akun wali lain tidak diambil alih.
 * - Satu baris gagal tidak membatalkan baris lainnya.
 */
class StudentsImport implements OnEachRow, SkipsEmptyRows, WithCalculatedFormulas, WithHeadingRow
{
    /** @var array<int, array<string, mixed>> */
    public array $berhasil = [];

    /** @var array<int, array<string, mixed>> */
    public array $gagal = [];

    public int $dibuat = 0;

    public int $diperbarui = 0;

    /** @var array<string, int> NISN => nomor baris (deteksi duplikat dalam file). */
    private array $nisnTerlihat = [];

    /**
     * @param  string|null  $jenjangAdmin  Slug jenjang admin yang sedang login.
     *                                     Null berarti Super Admin (semua jenjang).
     */
    public function __construct(private ?string $jenjangAdmin = null)
    {
        $this->jenjangAdmin = Ppdb::jenjangSlug($this->jenjangAdmin);
    }

    public function onRow(Row $row): void
    {
        $nomorBaris = $row->getIndex();
        $data = $this->bacaBaris($row->toArray());

        // Lewati baris kosong.
        if ($data['nisn'] === '' && $data['nama_santri'] === ''
            && $data['jenjang'] === '' && $data['parent_email'] === '') {
            return;
        }

        try {
            $this->proses($data, $nomorBaris);
        } catch (Throwable $e) {
            $this->gagal[] = [
                'baris' => $nomorBaris,
                'nisn' => $data['nisn'],
                'nama' => $data['nama_santri'],
                'pesan' => $e->getMessage(),
            ];
        }
    }

    /**
     * @return array{nisn:string, nama_santri:string, jenjang:string, parent_email:string}
     */
    private function bacaBaris(mixed $row): array
    {
        $baris = collect($row instanceof Collection ? $row->all() : (array) $row)
            ->mapWithKeys(fn ($nilai, $kunci) => [Str::slug((string) $kunci, '_') => $nilai]);

        $ambil = fn (array $kandidat): string => $this->teks(
            collect($kandidat)->map(fn ($k) => $baris->get($k))->first(fn ($v) => $v !== null && $v !== '')
        );

        return [
            'nisn' => $ambil(['nisn', 'nis', 'nisn_santri']),
            'nama_santri' => $ambil(['nama_santri', 'nama', 'nama_lengkap']),
            'jenjang' => $ambil(['jenjang', 'jenjang_pendidikan']),
            'parent_email' => strtolower($ambil(['parent_email', 'email', 'email_wali', 'email_orang_tua'])),
        ];
    }

    /** Ubah nilai sel menjadi teks tanpa notasi ilmiah untuk NISN angka. */
    private function teks(mixed $nilai): string
    {
        if (is_float($nilai) && floor($nilai) === $nilai) {
            return number_format($nilai, 0, '', '');
        }

        return trim((string) $nilai);
    }

    /**
     * @param  array{nisn:string, nama_santri:string, jenjang:string, parent_email:string}  $data
     */
    private function proses(array $data, int $nomorBaris): void
    {
        // 1. Validasi kolom wajib.
        if ($data['nisn'] === '') {
            throw new RuntimeException('NISN wajib diisi.');
        }

        if (mb_strlen($data['nisn']) > 30) {
            throw new RuntimeException('NISN maksimal 30 karakter.');
        }

        if ($data['nama_santri'] === '') {
            throw new RuntimeException('Nama santri wajib diisi.');
        }

        if (mb_strlen($data['nama_santri']) > 255) {
            throw new RuntimeException('Nama santri maksimal 255 karakter.');
        }

        if ($data['parent_email'] === '' || ! filter_var($data['parent_email'], FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Email orang tua/wali wajib diisi dengan format email yang valid.');
        }

        // 2. Validasi jenjang & batas kewenangan admin.
        $slug = Ppdb::jenjangSlug($data['jenjang']);

        if (! $slug) {
            throw new RuntimeException(
                'Jenjang "'.$data['jenjang'].'" tidak dikenali. Gunakan salah satu: '
                .implode(', ', array_column(Jenjang::semua(), 'kode')).'.'
            );
        }

        if ($this->jenjangAdmin && $slug !== $this->jenjangAdmin) {
            throw new RuntimeException(
                'Baris ini untuk jenjang '.Jenjang::label($slug)
                .', sedangkan Anda hanya boleh mengimpor jenjang '.Jenjang::label($this->jenjangAdmin).'.'
            );
        }

        // 3. Deteksi NISN ganda di dalam file.
        if (isset($this->nisnTerlihat[$data['nisn']])) {
            throw new RuntimeException(
                'NISN '.$data['nisn'].' duplikat dengan baris '.$this->nisnTerlihat[$data['nisn']].' di file ini.'
            );
        }

        $this->nisnTerlihat[$data['nisn']] = $nomorBaris;

        // 4. Siapkan akun wali (di luar transaksi baris agar bisa dipakai ulang).
        ['user' => $user, 'password' => $passwordBaru] = $this->akunWali($data['parent_email'], $data['nama_santri']);

        $status = DB::transaction(function () use ($data, $slug, $user) {
            $student = Student::withTrashed()->where('nisn', $data['nisn'])->first();

            if ($student) {
                if ($student->trashed()) {
                    $student->restore();
                }

                $slugLama = Ppdb::jenjangSlug($student->jenjang);

                if ($this->jenjangAdmin && $slugLama && $slugLama !== $this->jenjangAdmin) {
                    throw new RuntimeException(
                        'NISN '.$data['nisn'].' sudah terdaftar pada jenjang '
                        .Jenjang::label($student->jenjang).' di luar kewenangan Anda.'
                    );
                }

                if ($student->user_id && (int) $student->user_id !== (int) $user->id) {
                    $pemilik = User::withTrashed()->find($student->user_id);

                    throw new RuntimeException(
                        'NISN '.$data['nisn'].' sudah tertaut ke akun wali lain'
                        .($pemilik?->email ? ' ('.$pemilik->email.')' : '').'.'
                    );
                }

                if ($student->class_id && $slugLama && $slugLama !== $slug) {
                    throw new RuntimeException(
                        'Santri sudah diplot ke kelas. Ubah jenjang melalui menu plotting, bukan import.'
                    );
                }

                $student->fill([
                    'name' => $data['nama_santri'],
                    'parent_email' => $data['parent_email'],
                    'jenjang' => $slug,
                    'user_id' => $user->id,
                ])->save();

                return 'diperbarui';
            }

            Student::create([
                'nisn' => $data['nisn'],
                'name' => $data['nama_santri'],
                'parent_email' => $data['parent_email'],
                'jenjang' => $slug,
                'status' => 'aktif',
                'user_id' => $user->id,
            ]);

            return 'dibuat';
        });

        if ($status === 'dibuat') {
            $this->dibuat++;
        } else {
            $this->diperbarui++;
        }

        $this->berhasil[] = [
            'baris' => $nomorBaris,
            'nisn' => $data['nisn'],
            'nama' => $data['nama_santri'],
            'jenjang' => Jenjang::label($slug),
            'email' => $data['parent_email'],
            'password' => $passwordBaru,
            'status' => $status,
        ];
    }

    /**
     * Cari atau buat akun wali berperan `walisantri`.
     *
     * @return array{user: User, password: ?string} password hanya diisi bila akun baru dibuat.
     */
    private function akunWali(string $email, string $namaSantri): array
    {
        $user = User::withTrashed()->where('email', $email)->first();

        if ($user && $user->trashed()) {
            $user->restore();
        }

        if ($user) {
            if ($user->peranSistem() !== 'walisantri') {
                throw new RuntimeException(
                    "Email {$email} sudah dipakai akun lain (peran: {$user->labelPeran()})."
                );
            }

            return ['user' => $user, 'password' => null];
        }

        // Kata sandi unik per akun; ditampilkan sekali pada laporan impor.
        $passwordBaru = Str::password(12, symbols: false);

        $user = User::create([
            'name' => 'Wali '.$namaSantri,
            'email' => $email,
            'password' => Hash::make($passwordBaru),
            'role' => 'wali_santri',
            'status' => 'active',
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();
        RoleSistem::sinkron($user, 'walisantri');

        return ['user' => $user, 'password' => $passwordBaru];
    }
}
