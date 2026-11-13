<?php

namespace App\Support;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Aturan Manajemen Nilai Akademik pada skema baru:
 * `classes` + `subjects` + `students` + `grades`.
 *
 * Seluruh data nilai yang ditulis di sini berstatus `draft` sampai admin
 * menerbitkannya. Portal wali hanya membaca baris `published`.
 */
class NilaiAkademik
{
    public static function siap(): bool
    {
        try {
            return Schema::hasTable('classes')
                && Schema::hasTable('subjects')
                && Schema::hasTable('students')
                && Schema::hasTable('grades')
                && Schema::hasColumn('grades', 'status')
                && Schema::hasColumn('grades', 'published_at');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Kelas yang boleh diakses admin (dibatasi jenjang bila perlu). */
    public static function kelasQuery(?User $user): Builder
    {
        $query = SchoolClass::query();

        if (Akses::adminTanpaJenjang($user)) {
            return $query->whereRaw('1 = 0');
        }

        $slug = Akses::jenjangSlug($user);

        if ($slug) {
            $query->whereIn('jenjang', Ppdb::variasiJenjang($slug));
        }

        return $query->orderBy('jenjang')->orderBy('name');
    }

    /** Mata pelajaran yang boleh diakses admin. */
    public static function mapelQuery(?User $user): Builder
    {
        $query = Subject::query();

        if (Akses::adminTanpaJenjang($user)) {
            return $query->whereRaw('1 = 0');
        }

        $slug = Akses::jenjangSlug($user);

        if ($slug) {
            $query->whereIn('jenjang', Ppdb::variasiJenjang($slug));
        }

        return $query->orderBy('jenjang')->orderBy('nama');
    }

    /** @return Collection<int, SchoolClass> */
    public static function daftarKelas(?User $user): Collection
    {
        return self::kelasQuery($user)->get();
    }

    /** @return Collection<int, Subject> */
    public static function daftarMapel(?User $user): Collection
    {
        return self::mapelQuery($user)->get();
    }

    /** Siswa yang duduk di satu kelas (kolom cache dan/atau pivot). */
    public static function siswaKelas(int $classId): Builder
    {
        return Student::query()
            ->where(function (Builder $query) use ($classId) {
                $query->where('class_id', $classId);

                if (Schema::hasTable('class_student')) {
                    $query->orWhereHas('classes', fn (Builder $q) => $q->where('classes.id', $classId));
                }
            })
            ->orderBy('name');
    }

    /**
     * Baca filter halaman admin.
     *
     * @return array{
     *     kelas:?SchoolClass,mapel:?Subject,class_id:?int,subject_id:?int,
     *     tahun:string,semester:string,lengkap:bool,mismatch:bool
     * }
     */
    public static function filter(Request $request, ?User $user): array
    {
        $classId = (int) $request->input('class_id');
        $subjectId = (int) $request->input('subject_id');

        $kelas = null;
        if ($classId > 0) {
            $kelas = self::kelasQuery($user)->whereKey($classId)->first();
            abort_unless($kelas, 403, 'Kelas ini berada di luar jenjang yang Anda tangani.');
        }

        $mapel = null;
        if ($subjectId > 0) {
            $mapel = self::mapelQuery($user)->whereKey($subjectId)->first();
            abort_unless($mapel, 403, 'Mata pelajaran ini berada di luar jenjang yang Anda tangani.');
        }

        $mismatch = $kelas && $mapel
            && Jenjang::normalisasi($kelas->jenjang) !== Jenjang::normalisasi($mapel->jenjang);

        $tahun = trim((string) $request->input('tahun_ajaran')) ?: Akademik::tahunAjaranSekarang();
        $semester = strtolower((string) $request->input('semester'));

        if (! array_key_exists($semester, Akademik::SEMESTER)) {
            $semester = Akademik::semesterSekarang();
        }

        return [
            'kelas' => $kelas,
            'mapel' => $mapel,
            'class_id' => $kelas?->id,
            'subject_id' => $mapel?->id,
            'tahun' => $tahun,
            'semester' => $semester,
            'lengkap' => $kelas !== null && $mapel !== null && ! $mismatch,
            'mismatch' => $mismatch,
        ];
    }

    /**
     * Nilai satu mapel untuk sekumpulan siswa, dikelompokkan per siswa lalu
     * per kategori.
     *
     * @param  Collection<int, Student>  $siswa
     * @return Collection<int, Collection<string, Grade>>
     */
    public static function matriks(Collection $siswa, int $subjectId, string $tahun, string $semester): Collection
    {
        return Grade::query()
            ->whereIn('student_id', $siswa->pluck('id'))
            ->where('subject_id', $subjectId)
            ->where('tahun_ajaran', $tahun)
            ->where('semester', $semester)
            ->get()
            ->groupBy('student_id')
            ->map(fn (Collection $rows) => $rows->keyBy('kategori'));
    }

    /**
     * @param  Collection<int, Student>  $siswa
     * @param  Collection<int, Collection<string, Grade>>  $matriks
     * @return array{total:int,terisi:int,draft:int,terbit:int,rata:?float}
     */
    public static function ringkasan(Collection $siswa, Collection $matriks): array
    {
        $nilaiAkhir = $siswa->map(function (Student $student) use ($matriks) {
            $rows = $matriks->get($student->id);

            return $rows && $rows->isNotEmpty() ? self::nilaiAkhir($rows) : null;
        })->filter(fn ($nilai) => $nilai !== null);

        $semua = $matriks->flatten(1);

        return [
            'total' => $siswa->count(),
            'terisi' => $matriks->filter(fn (Collection $rows) => $rows->isNotEmpty())->count(),
            'draft' => $semua->where('status', 'draft')->count(),
            'terbit' => $semua->where('status', 'published')->count(),
            'rata' => $nilaiAkhir->isEmpty() ? null : round((float) $nilaiAkhir->avg(), 2),
        ];
    }

    /** Rata-rata kategori yang terisi. */
    public static function nilaiAkhir(Collection $rows): ?float
    {
        $nilai = $rows
            ->map(fn (Grade $grade) => (float) $grade->nilai)
            ->filter(fn ($n) => $n !== null);

        return $nilai->isEmpty() ? null : round((float) $nilai->avg(), 2);
    }

    /** Status satu siswa pada konteks ini. */
    public static function statusSiswa(Collection $rows): ?string
    {
        if ($rows->isEmpty()) {
            return null;
        }

        return $rows->contains(fn (Grade $grade) => $grade->status !== 'published')
            ? 'draft'
            : 'published';
    }

    /**
     * Simpan nilai manual. Nilai yang berubah kembali menjadi draft.
     *
     * @param  Collection<int, Student>  $siswa
     * @param  array<int|string, array<string, mixed>>  $input
     * @return array{jumlah:int,dikembalikan:int}
     */
    public static function simpan(
        Collection $siswa,
        int $classId,
        int $subjectId,
        string $tahun,
        string $semester,
        array $input,
    ): array {
        $jumlah = 0;
        $dikembalikan = 0;
        $academicYearId = self::academicYearId($tahun);

        DB::transaction(function () use ($siswa, $classId, $subjectId, $tahun, $semester, $input, $academicYearId, &$jumlah, &$dikembalikan) {
            foreach ($siswa as $student) {
                foreach (array_keys(Akademik::KATEGORI) as $kategori) {
                    $mentah = $input[$student->id][$kategori] ?? null;
                    $nilaiBaru = is_numeric($mentah) ? round((float) $mentah, 2) : null;

                    $kunci = [
                        'student_id' => $student->id,
                        'subject_id' => $subjectId,
                        'tahun_ajaran' => $tahun,
                        'semester' => $semester,
                        'kategori' => $kategori,
                    ];

                    $row = Grade::withTrashed()->firstOrNew($kunci);

                    if ($nilaiBaru === null) {
                        if ($row->exists && $row->status === 'draft') {
                            $row->delete();
                        }

                        continue;
                    }

                    $dipulihkan = $row->trashed();

                    if ($dipulihkan) {
                        $row->restore();
                    }

                    $baru = ! $row->exists || $dipulihkan;
                    $berubah = $baru
                        || abs(((float) $row->nilai) - $nilaiBaru) > 0.001
                        || (int) $row->class_id !== $classId;

                    if ($berubah && ! $baru && $row->status === 'published') {
                        $dikembalikan++;
                    }

                    $atribut = [
                        'class_id' => $classId,
                        'nilai' => $nilaiBaru,
                    ];

                    if ($academicYearId !== null) {
                        $atribut['academic_year_id'] = $academicYearId;
                    }

                    $row->fill($atribut);

                    if ($berubah) {
                        $row->status = 'draft';
                        $row->published_at = null;
                    }

                    $row->save();
                    $jumlah++;
                }
            }
        });

        return ['jumlah' => $jumlah, 'dikembalikan' => $dikembalikan];
    }

    /**
     * Terbitkan seluruh nilai draft pada satu konteks.
     *
     * @param  Collection<int, Student>  $siswa
     */
    public static function terbitkan(Collection $siswa, int $classId, int $subjectId, string $tahun, string $semester): int
    {
        $perubahan = [
            'class_id' => $classId,
            'status' => 'published',
            'published_at' => now(),
        ];

        $academicYearId = self::academicYearId($tahun);

        if ($academicYearId !== null) {
            $perubahan['academic_year_id'] = $academicYearId;
        }

        return Grade::query()
            ->whereIn('student_id', $siswa->pluck('id'))
            ->where('subject_id', $subjectId)
            ->where('tahun_ajaran', $tahun)
            ->where('semester', $semester)
            ->where('status', 'draft')
            ->update($perubahan);
    }

    /**
     * Baris template Excel.
     *
     * @param  Collection<int, Student>  $siswa
     * @param  Collection<int, Collection<string, Grade>>  $matriks
     * @return array<int, array<int, string|float|null>>
     */
    public static function barisTemplate(Collection $siswa, Collection $matriks): array
    {
        return $siswa->map(function (Student $student) use ($matriks) {
            $rows = $matriks->get($student->id) ?? collect();
            $ambil = fn (string $kategori) => $rows->get($kategori)?->nilai;
            $catatan = $rows->first(fn (Grade $grade) => filled($grade->catatan))?->catatan;

            return [
                (string) $student->nisn,
                (string) $student->name,
                self::teksNilai($ambil('tugas')),
                self::teksNilai($ambil('uts')),
                self::teksNilai($ambil('uas')),
                self::teksNilai($ambil('hafalan')),
                (string) ($catatan ?? ''),
            ];
        })->values()->all();
    }

    private static function teksNilai(mixed $nilai): ?float
    {
        if ($nilai === null || $nilai === '') {
            return null;
        }

        return round((float) $nilai, 2);
    }

    /** Id tahun ajaran untuk `grades.academic_year_id` (null bila belum tersedia). */
    private static function academicYearId(string $tahun): ?int
    {
        try {
            if (! Schema::hasTable('academic_years') || ! Schema::hasColumn('grades', 'academic_year_id')) {
                return null;
            }

            return AcademicYear::untuk($tahun)->id;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
