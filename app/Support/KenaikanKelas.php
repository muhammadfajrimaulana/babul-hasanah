<?php

namespace App\Support;

use App\Models\AcademicYear;
use App\Models\Alumni;
use App\Models\Santri;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Aturan Kenaikan Kelas dan Kelulusan Santri (skema `students`).
 *
 * Prinsip penting:
 * - Kenaikan kelas hanya memindahkan penempatan aktif (`students.class_id` dan
 *   pivot `class_student`). Baris `grades` TIDAK diubah/dihapus sehingga nilai
 *   lama tetap tersimpan pada tahun ajarannya (`grades.academic_year_id`).
 * - Kelulusan mengubah `students.status` menjadi `graduated`, menautkan data
 *   `alumni`, dan mengalihkan peran akun wali menjadi `alumni` bila akun tidak
 *   lagi memantau anak lain.
 */
class KenaikanKelas
{
    /** Santri yang masih menempati satu kelas. */
    public static function siswaKelas(int $classId): Collection
    {
        return Student::query()
            ->with(['kelas', 'alumni'])
            ->where(function ($query) use ($classId) {
                $query->where('class_id', $classId);

                if (Schema::hasTable('class_student')) {
                    $query->orWhereHas('classes', fn ($q) => $q->where('classes.id', $classId));
                }
            })
            ->whereIn('status', ['aktif', 'calon'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Pindahkan santri ke kelas baru pada tahun ajaran tertentu.
     *
     * @param  array<int, int|string>  $studentIds
     * @return array{berhasil:int,dilewati:array<int,array{nama:string,nisn:string,alasan:string}>}
     */
    public static function naikkan(array $studentIds, SchoolClass $asal, SchoolClass $tujuan, AcademicYear $tahun): array
    {
        if ($asal->id === $tujuan->id) {
            throw new RuntimeException('Kelas asal dan kelas tujuan tidak boleh sama.');
        }

        $slugAsal = Jenjang::normalisasi($asal->jenjang);
        $slugTujuan = Jenjang::normalisasi($tujuan->jenjang);

        if ($slugAsal && $slugTujuan && $slugAsal !== $slugTujuan) {
            throw new RuntimeException(
                'Kelas tujuan harus berada pada jenjang yang sama ('
                .Jenjang::label($asal->jenjang).').'
            );
        }

        $ids = array_values(array_unique(array_map('intval', $studentIds)));
        $students = Student::query()->whereIn('id', $ids)->get()->keyBy('id');
        $berhasil = 0;
        $dilewati = [];

        foreach ($ids as $id) {
            /** @var Student|null $student */
            $student = $students->get($id);

            if (! $student) {
                $dilewati[] = ['nama' => "ID {$id}", 'nisn' => '—', 'alasan' => 'Data santri tidak ditemukan.'];

                continue;
            }

            if ($student->status === 'graduated') {
                $dilewati[] = self::lewati($student, 'Santri sudah berstatus lulus/alumni.');

                continue;
            }

            if ((int) $student->class_id !== (int) $asal->id
                && ! $student->classes()->whereKey($asal->id)->exists()) {
                $dilewati[] = self::lewati($student, 'Santri tidak berada di kelas asal yang dipilih.');

                continue;
            }

            DB::transaction(function () use ($student, $tujuan, $tahun, $slugTujuan) {
                // Catat penempatan baru beserta tahun ajarannya.
                $pivot = Schema::hasColumn('class_student', 'academic_year_id')
                    ? [$tujuan->id => ['academic_year_id' => $tahun->id]]
                    : [$tujuan->id];

                $student->classes()->sync($pivot);

                $student->forceFill([
                    'class_id' => $tujuan->id,
                    'jenjang' => $slugTujuan ?: ($student->jenjang ?: $tujuan->jenjang),
                    'status' => 'aktif',
                ])->save();
            });

            $berhasil++;
        }

        return ['berhasil' => $berhasil, 'dilewati' => $dilewati];
    }

    /**
     * Luluskan santri: status `graduated`, data alumni dibuat, peran akun
     * wali dialihkan menjadi `alumni` bila tidak ada anak lain yang aktif.
     *
     * @param  array<int, int|string>  $studentIds
     * @return array{
     *     berhasil:int,
     *     role_diubah:int,
     *     role_ditahan:array<int,array{nama:string,alasan:string}>,
     *     dilewati:array<int,array{nama:string,nisn:string,alasan:string}>
     * }
     */
    public static function luluskan(array $studentIds, AcademicYear $tahun): array
    {
        $ids = array_values(array_unique(array_map('intval', $studentIds)));
        $students = Student::query()
            ->with(['user', 'santri.user', 'alumni'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $berhasil = 0;
        $roleDiubah = 0;
        $roleDitahan = [];
        $dilewati = [];
        $tahunLulus = $tahun->tahunLulus();

        foreach ($ids as $id) {
            /** @var Student|null $student */
            $student = $students->get($id);

            if (! $student) {
                $dilewati[] = ['nama' => "ID {$id}", 'nisn' => '—', 'alasan' => 'Data santri tidak ditemukan.'];

                continue;
            }

            if ($student->status === 'graduated') {
                $dilewati[] = self::lewati($student, 'Santri sudah berstatus lulus/alumni.');

                continue;
            }

            $user = self::akunWali($student);
            $alumni = DB::transaction(function () use ($student, $tahunLulus, $user) {
                $perubahan = [
                    'status' => 'graduated',
                    'class_id' => null,
                ];

                if (Schema::hasColumn('students', 'graduated_at')) {
                    $perubahan['graduated_at'] = now();
                }

                $student->forceFill($perubahan)->save();

                $student->classes()->detach();

                return self::catatAlumni($student, $user, $tahunLulus);
            });

            if (! $user) {
                $roleDitahan[] = [
                    'nama' => $student->name,
                    'alasan' => 'Tidak ada akun login tertaut; data alumni tetap dibuat.',
                ];
            } elseif (! self::peranBolehDialihkan($user)) {
                $roleDitahan[] = [
                    'nama' => $student->name,
                    'alasan' => 'Akun tertaut bukan wali santri/alumni (tidak diubah otomatis).',
                ];
            } elseif (self::punyaAnakLain($student, $user)) {
                $roleDitahan[] = [
                    'nama' => $student->name,
                    'alasan' => 'Akun masih memantau anak lain; peran tetap wali santri.',
                ];
            } else {
                if ($alumni && Schema::hasColumn('alumni', 'user_id') && ! $alumni->user_id) {
                    $alumni->forceFill(['user_id' => $user->id])->save();
                }

                try {
                    RoleSistem::sinkron($user, 'alumni');
                    $roleDiubah++;
                } catch (\Throwable $e) {
                    $roleDitahan[] = [
                        'nama' => $student->name,
                        'alasan' => 'Peran gagal dialihkan otomatis; atur manual melalui menu Pengguna.',
                    ];
                }
            }

            $berhasil++;
        }

        return [
            'berhasil' => $berhasil,
            'role_diubah' => $roleDiubah,
            'role_ditahan' => $roleDitahan,
            'dilewati' => $dilewati,
        ];
    }

    /** Akun wali tertaut: students.user_id, santri.user_id, atau email wali. */
    private static function akunWali(Student $student): ?User
    {
        if ($student->user) {
            return $student->user;
        }

        if ($student->santri?->user) {
            return $student->santri->user;
        }

        $email = strtolower(trim((string) $student->parent_email));

        return $email !== ''
            ? User::query()->whereRaw('LOWER(email) = ?', [$email])->first()
            : null;
    }

    private static function peranBolehDialihkan(User $user): bool
    {
        $mentah = strtolower(str_replace([' ', '-'], '_', trim((string) $user->role)));
        $peran = Akses::normalisasi($user->role)
            ?: RoleSistem::legacy($user->getRoleNames()->first());

        // `santri` adalah penulisan lama untuk akun santri/wali; keduanya
        // sama-sama boleh dialihkan menjadi alumni saat lulus.
        if (in_array($mentah, ['santri', 'wali_santri', 'walisantri'], true)
            || in_array($peran, ['wali_santri', 'alumni'], true)) {
            return true;
        }

        return method_exists($user, 'hasRole')
            && ($user->hasRole('santri') || $user->hasRole('walisantri') || $user->hasRole('alumni'));
    }

    /** Apakah akun masih memantau anak lain yang belum lulus. */
    private static function punyaAnakLain(Student $student, User $user): bool
    {
        $email = strtolower(trim((string) $user->email));

        $adaDiStudents = Student::query()
            ->whereKeyNot($student->id)
            ->where('status', '!=', 'graduated')
            ->where(function ($query) use ($user, $email) {
                $query->where('user_id', $user->id);

                if ($email !== '') {
                    $query->orWhereRaw('LOWER(parent_email) = ?', [$email]);
                }
            })
            ->exists();

        if ($adaDiStudents) {
            return true;
        }

        // Data lama: anak lain bisa hanya terdaftar di tabel `santri`.
        if (Schema::hasTable('santri') && Schema::hasColumn('santri', 'user_id')) {
            return Santri::query()
                ->where('user_id', $user->id)
                ->when($student->santri_id, fn ($query) => $query->whereKeyNot($student->santri_id))
                ->exists();
        }

        return false;
    }

    /** Buat/perbarui baris `alumni` untuk santri yang lulus. */
    private static function catatAlumni(Student $student, ?User $user, int $tahunLulus): Alumni
    {
        if (! Schema::hasTable('alumni')) {
            throw new RuntimeException('Tabel alumni belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $email = strtolower(trim((string) ($user?->email ?: $student->parent_email)));
        $punyaStudent = Schema::hasColumn('alumni', 'student_id');
        $punyaUser = Schema::hasColumn('alumni', 'user_id');

        $alumni = $punyaStudent
            ? Alumni::query()->where('student_id', $student->id)->first()
            : Alumni::query()->whereRaw('1 = 0')->first();

        if (! $alumni && $email !== '') {
            $alumni = Alumni::query()
                ->when($punyaStudent, fn ($query) => $query->whereNull('student_id'))
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();
        }

        $alumni ??= new Alumni;

        $infoJenjang = Jenjang::cari(Jenjang::normalisasi($student->jenjang));

        $data = [
            'nama' => $student->name,
            'tahun_lulus' => $alumni->tahun_lulus ?: $tahunLulus,
            'jenjang' => $infoJenjang['kode'] ?? $student->jenjang,
            'email' => $alumni->email ?: ($email ?: null),
            'alamat' => $alumni->alamat ?: $student->santri?->alamat,
        ];

        if ($punyaStudent) {
            $data['student_id'] = $student->id;
        }

        if ($punyaUser) {
            $data['user_id'] = $user?->id;
        }

        $alumni->fill($data)->save();

        return $alumni;
    }

    /** @return array{nama:string,nisn:string,alasan:string} */
    private static function lewati(Student $student, string $alasan): array
    {
        return [
            'nama' => (string) $student->name,
            'nisn' => (string) $student->nisn,
            'alasan' => $alasan,
        ];
    }
}
