<?php

namespace App\Support;

use App\Models\Alumni;
use App\Models\Grade;
use App\Models\NilaiSantri;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Data transkrip lengkap dan ijazah digital untuk Portal Alumni.
 *
 * Sumber nilai:
 * 1. `grades` yang berstatus `published` (skema baru), dikelompokkan per
 *    tahun ajaran + semester lalu per mata pelajaran.
 * 2. `nilai_santri` published sebagai cadangan untuk santri data lama.
 *
 * Nilai draft tidak pernah dimuat ke dokumen resmi alumni.
 */
class TranskripAlumni
{
    /**
     * Identitas alumni yang sedang login.
     *
     * @return array{
     *     alumni:?Alumni,student:?Student,nama:string,nis:string,jenjang:?string,
     *     tahun_lulus:?int,email:?string,jenis_kelamin:?string
     * }
     */
    public static function konteks(User $user): array
    {
        $email = strtolower(trim((string) $user->email));
        $alumni = null;
        $punyaUser = Schema::hasTable('alumni') && Schema::hasColumn('alumni', 'user_id');
        $punyaStudent = Schema::hasTable('alumni') && Schema::hasColumn('alumni', 'student_id');

        if (Schema::hasTable('alumni')) {
            $alumni = Alumni::query()
                ->when($punyaStudent, fn ($query) => $query->with('student'))
                ->when($punyaUser, fn ($query) => $query->where('user_id', $user->id), fn ($query) => $query->whereRaw('1 = 0'))
                ->when($email !== '', fn ($query) => $query->orWhereRaw('LOWER(email) = ?', [$email]))
                ->when($punyaUser, fn ($query) => $query->orderByRaw('CASE WHEN user_id = ? THEN 0 ELSE 1 END', [$user->id]))
                ->first();
        }

        $student = $punyaStudent ? $alumni?->student : null;

        if (! $student && Schema::hasTable('students')) {
            $student = Student::query()
                ->where(function ($query) use ($user, $email) {
                    $query->where('user_id', $user->id);

                    if ($email !== '') {
                        $query->orWhereRaw('LOWER(parent_email) = ?', [$email]);
                    }
                })
                ->orderByRaw("CASE WHEN status = 'graduated' THEN 0 ELSE 1 END")
                ->orderByDesc('graduated_at')
                ->orderByDesc('id')
                ->first();
        }

        if (! $alumni && $student && Schema::hasTable('alumni')) {
            $alumni = Alumni::query()
                ->when($punyaStudent, fn ($query) => $query->where('student_id', $student->id), fn ($query) => $query->whereRaw('1 = 0'))
                ->when($email !== '', fn ($query) => $query->orWhereRaw('LOWER(email) = ?', [$email]))
                ->first();
        }

        $nama = $alumni?->nama ?: $student?->name ?: (string) $user->name;
        $nis = (string) ($student?->nisn ?: '—');
        $jenjang = $alumni?->jenjang ?: ($student?->jenjang ? Jenjang::label($student->jenjang) : null);

        $tahunLulus = $alumni?->tahun_lulus
            ? (int) $alumni->tahun_lulus
            : ($student?->graduated_at?->year);

        return [
            'alumni' => $alumni,
            'student' => $student,
            'nama' => $nama,
            'nis' => $nis,
            'jenjang' => $jenjang,
            'tahun_lulus' => $tahunLulus,
            'email' => $alumni?->email ?: ($student?->parent_email ?: (string) $user->email),
            'jenis_kelamin' => $alumni?->jenis_kelamin,
        ];
    }

    /**
     * Transkrip lengkap semua semester yang sudah terbit.
     *
     * @param  array<string, mixed>  $konteks
     * @return array{
     *     periode:Collection<int,array<string,mixed>>,
     *     rata:?float,predikat:string,jumlah_mapel:int,jumlah_periode:int,
     *     terakhir:mixed,sumber:string
     * }
     */
    public static function untuk(array $konteks): array
    {
        /** @var Student|null $student */
        $student = $konteks['student'] ?? null;
        $baru = collect();
        $lama = collect();

        if ($student && self::siapGrades()) {
            $punyaTahun = Schema::hasTable('academic_years')
                && Schema::hasColumn('grades', 'academic_year_id');

            $baru = Grade::query()
                ->with($punyaTahun ? ['subject', 'academicYear'] : ['subject'])
                ->where('student_id', $student->id)
                ->where('status', 'published')
                ->orderByDesc('tahun_ajaran')
                ->orderByDesc('semester')
                ->get();
        }

        if ($baru->isEmpty() && $student?->santri_id && Schema::hasTable('nilai_santri')) {
            $lama = NilaiSantri::query()
                ->with('mataPelajaran')
                ->where('santri_id', $student->santri_id)
                ->where('status', 'published')
                ->orderByDesc('tahun_ajaran')
                ->orderByDesc('semester')
                ->get();
        }

        $sumber = $baru->isNotEmpty() ? 'baru' : ($lama->isNotEmpty() ? 'lama' : 'kosong');

        $periode = $sumber === 'baru'
            ? self::periodeBaru($baru)
            : ($sumber === 'lama' ? self::periodeLama($lama) : collect());

        $rata = self::rataKeseluruhan($periode);
        $terakhir = $sumber === 'baru' ? $baru->max('published_at') : $lama->max('published_at');
        $jumlahMapel = $periode->sum(fn (array $p) => count($p['mapel']));

        return [
            'periode' => $periode,
            'rata' => $rata,
            'predikat' => Akademik::predikat($rata),
            'jumlah_mapel' => $jumlahMapel,
            'jumlah_periode' => $periode->count(),
            'terakhir' => $terakhir,
            'sumber' => $sumber,
        ];
    }

    /** Nomor ijazah digital yang deterministik. */
    public static function nomorIjazah(array $konteks): string
    {
        $tahun = $konteks['tahun_lulus'] ?? now()->year;
        $nis = preg_replace('/[^A-Za-z0-9]/', '', (string) ($konteks['nis'] ?? '')) ?: 'ALUMNI';

        return 'IJZ/BH/'.$tahun.'/'.$nis;
    }

    /** Kode verifikasi singkat yang dapat dicocokkan pesantren. */
    public static function kodeVerifikasi(array $konteks): string
    {
        $bahan = mb_strtolower(trim(($konteks['nis'] ?? '').'|'.($konteks['nama'] ?? '').'|'.($konteks['tahun_lulus'] ?? '')));

        return strtoupper(substr(hash('sha256', $bahan), 0, 12));
    }

    /** Apakah data alumni sudah layak dicetak sebagai ijazah. */
    public static function siapIjazah(array $konteks): bool
    {
        $student = $konteks['student'] ?? null;

        return (bool) ($konteks['alumni'] ?? null)
            || $student?->status === 'graduated'
            || $student?->graduated_at !== null;
    }

    /**
     * @param  Collection<int, Grade>  $baris
     * @return Collection<int, array<string, mixed>>
     */
    private static function periodeBaru(Collection $baris): Collection
    {
        return $baris
            ->groupBy(fn (Grade $grade) => self::tahunGrade($grade).'|'.$grade->semester)
            ->map(function (Collection $grupPeriode) {
                $mapel = $grupPeriode
                    ->groupBy('subject_id')
                    ->map(function (Collection $grup) {
                        $subject = $grup->first()->subject;
                        $nilai = $grup->pluck('nilai')->map(fn ($n) => (float) $n);
                        $akhir = round((float) $nilai->avg(), 2);
                        $kkm = (float) ($subject?->kkm ?? 75);
                        $ambil = fn (string $kategori) => $grup->firstWhere('kategori', $kategori)?->nilai;

                        return [
                            'nama' => $subject?->nama ?? 'Mata pelajaran terhapus',
                            'kkm' => $kkm,
                            'tugas' => $ambil('tugas'),
                            'uts' => $ambil('uts'),
                            'uas' => $ambil('uas'),
                            'hafalan' => $ambil('hafalan'),
                            'akhir' => $akhir,
                            'predikat' => Akademik::predikat($akhir),
                            'tuntas' => $akhir >= $kkm,
                            'catatan' => $grup->first(fn (Grade $g) => filled($g->catatan))?->catatan,
                        ];
                    })
                    ->sortBy('nama')
                    ->values();

                $pertama = $grupPeriode->first();

                return [
                    'tahun' => self::tahunGrade($pertama),
                    'semester' => (string) $pertama->semester,
                    'mapel' => $mapel,
                    'rata' => $mapel->isEmpty() ? null : round((float) $mapel->avg('akhir'), 2),
                    'predikat' => Akademik::predikat($mapel->isEmpty() ? null : $mapel->avg('akhir')),
                ];
            })
            ->sortByDesc(fn (array $p) => $p['tahun'].'|'.$p['semester'])
            ->values();
    }

    /**
     * @param  Collection<int, NilaiSantri>  $baris
     * @return Collection<int, array<string, mixed>>
     */
    private static function periodeLama(Collection $baris): Collection
    {
        return $baris
            ->groupBy(fn (NilaiSantri $nilai) => $nilai->tahun_ajaran.'|'.$nilai->semester)
            ->map(function (Collection $grupPeriode) {
                $mapel = $grupPeriode
                    ->groupBy('mata_pelajaran_id')
                    ->map(function (Collection $grup) {
                        $mapelModel = $grup->first()->mataPelajaran;
                        $nilai = $grup->pluck('nilai')->map(fn ($n) => (float) $n);
                        $akhir = round((float) $nilai->avg(), 2);
                        $kkm = (float) ($mapelModel?->kkm ?? 70);
                        $ambil = fn (string $kategori) => $grup->firstWhere('kategori', $kategori)?->nilai;

                        return [
                            'nama' => $mapelModel?->nama ?? 'Mata pelajaran terhapus',
                            'kkm' => $kkm,
                            'tugas' => $ambil('tugas'),
                            'uts' => $ambil('uts'),
                            'uas' => $ambil('uas'),
                            'hafalan' => $ambil('hafalan'),
                            'akhir' => $akhir,
                            'predikat' => Akademik::predikat($akhir),
                            'tuntas' => $akhir >= $kkm,
                            'catatan' => $grup->first(fn (NilaiSantri $n) => filled($n->catatan))?->catatan,
                        ];
                    })
                    ->sortBy('nama')
                    ->values();

                $pertama = $grupPeriode->first();

                return [
                    'tahun' => (string) $pertama->tahun_ajaran,
                    'semester' => (string) $pertama->semester,
                    'mapel' => $mapel,
                    'rata' => $mapel->isEmpty() ? null : round((float) $mapel->avg('akhir'), 2),
                    'predikat' => Akademik::predikat($mapel->isEmpty() ? null : $mapel->avg('akhir')),
                ];
            })
            ->sortByDesc(fn (array $p) => $p['tahun'].'|'.$p['semester'])
            ->values();
    }

    /** Nama tahun ajaran dari relasi bila dimuat, atau kolom teks lama. */
    private static function tahunGrade(Grade $grade): string
    {
        $dariRelasi = $grade->relationLoaded('academicYear')
            ? $grade->academicYear?->nama
            : null;

        return (string) ($dariRelasi ?: $grade->tahun_ajaran);
    }

    private static function rataKeseluruhan(Collection $periode): ?float
    {
        $nilai = $periode
            ->flatMap(fn (array $p) => collect($p['mapel'])->pluck('akhir'))
            ->filter(fn ($n) => $n !== null);

        return $nilai->isEmpty() ? null : round((float) $nilai->avg(), 2);
    }

    private static function siapGrades(): bool
    {
        try {
            return Schema::hasTable('grades') && Schema::hasColumn('grades', 'status');
        } catch (\Throwable $e) {
            return false;
        }
    }
}
