<?php

namespace App\Support;

use App\Models\Absensi;
use App\Models\Grade;
use App\Models\NilaiSantri;
use App\Models\Santri;
use App\Models\Student;
use App\Models\TagihanSantri;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use stdClass;

/**
 * Sumber data tunggal untuk Portal Wali Santri.
 *
 * Anak dicari dari dua sumber agar data lama tetap tampil:
 * 1. `students.parent_email` (atau `students.user_id`) — skema Modul 1/PPDB.
 * 2. `santri.user_id` — data lama yang belum punya pasangan di `students`.
 *
 * Nilai yang boleh tampil selalu hanya yang berstatus `published`.
 */
class PortalWali
{
    /** @return Collection<int, AnakPortal> */
    public static function anak(User $user): Collection
    {
        $daftarStudent = collect();

        if (Schema::hasTable('students')) {
            $email = strtolower(trim((string) $user->email));

            $daftarStudent = Student::query()
                ->with(['kelas', 'santri'])
                ->where(function ($query) use ($user, $email) {
                    // Tautan eksplisit ke akun ini selalu diutamakan.
                    $query->where('user_id', $user->id);

                    // Email wali hanya dipakai untuk santri yang BELUM ditautkan
                    // ke akun lain. Ini mencegah wali melihat anak yang sudah
                    // menjadi tanggung jawab akun wali lain.
                    if ($email !== '') {
                        $query->orWhere(function ($q) use ($email) {
                            $q->whereNull('user_id')
                                ->whereRaw('LOWER(parent_email) = ?', [$email]);
                        });
                    }
                })
                ->orderBy('name')
                ->get()
                // Bila dua baris students menunjuk santri lama yang sama,
                // tampilkan satu anak saja.
                ->unique(fn (Student $student) => $student->santri_id
                    ? 'santri-'.$student->santri_id
                    : 'student-'.$student->id)
                ->map(fn (Student $student) => AnakPortal::dariStudent($student))
                ->values();
        }

        $santriTerpakai = $daftarStudent
            ->map(fn (AnakPortal $anak) => $anak->santriId())
            ->filter()
            ->all();

        $daftarLama = collect();

        if (Schema::hasTable('santri')) {
            $daftarLama = Santri::query()
                ->where('user_id', $user->id)
                ->when($santriTerpakai !== [], fn ($query) => $query->whereNotIn('id', $santriTerpakai))
                ->orderBy('nama_lengkap')
                ->get()
                ->map(fn (Santri $santri) => AnakPortal::dariSantri($santri))
                ->values();
        }

        return $daftarStudent
            ->concat($daftarLama)
            ->sortBy(fn (AnakPortal $anak) => mb_strtolower($anak->nama))
            ->values();
    }

    /**
     * Anak aktif. Query `?anak=` diutamakan, lalu pilihan terakhir di sesi.
     * Kepemilikan selalu diverifikasi di server.
     *
     * @param  Collection<int, AnakPortal>  $daftar
     */
    public static function aktif(mixed $request, Collection $daftar): ?AnakPortal
    {
        if ($daftar->isEmpty()) {
            return null;
        }

        $kunci = $request->input('anak');

        if (filled($kunci)) {
            $anak = $daftar->firstWhere('key', (string) $kunci);
            abort_unless($anak, 403, 'Data anak ini bukan milik akun Anda.');

            $request->session()->put('portal.anak', $anak->key);

            return $anak;
        }

        $dariSesi = $request->session()->get('portal.anak');

        if (filled($dariSesi)) {
            $anak = $daftar->firstWhere('key', (string) $dariSesi);

            if ($anak) {
                return $anak;
            }
        }

        return $daftar->first();
    }

    /**
     * Jumlah nilai terbit per anak untuk indikator dashboard.
     *
     * @param  Collection<int, AnakPortal>  $daftar
     * @return Collection<string, stdClass>
     */
    public static function ringkasanNilaiTerbit(Collection $daftar): Collection
    {
        $hasil = collect();
        $idStudent = $daftar->map(fn (AnakPortal $a) => $a->studentId())->filter()->unique()->all();
        $idSantri = $daftar->map(fn (AnakPortal $a) => $a->santriId())->filter()->unique()->all();

        $perStudent = collect();
        if ($idStudent !== [] && self::siapGrades()) {
            $perStudent = Grade::query()
                ->whereIn('student_id', $idStudent)
                ->where('status', 'published')
                ->selectRaw('student_id, COUNT(*) as jumlah, MAX(published_at) as terakhir')
                ->groupBy('student_id')
                ->get()
                ->keyBy('student_id');
        }

        $perSantri = collect();
        if ($idSantri !== [] && Schema::hasTable('nilai_santri')) {
            $perSantri = NilaiSantri::query()
                ->whereIn('santri_id', $idSantri)
                ->where('status', 'published')
                ->selectRaw('santri_id, COUNT(*) as jumlah, MAX(published_at) as terakhir')
                ->groupBy('santri_id')
                ->get()
                ->keyBy('santri_id');
        }

        foreach ($daftar as $anak) {
            $baru = $anak->studentId() ? $perStudent->get($anak->studentId()) : null;
            $lama = $anak->santriId() ? $perSantri->get($anak->santriId()) : null;

            $jumlah = (int) ($baru->jumlah ?? 0) + (int) ($lama->jumlah ?? 0);
            $tanggal = collect([$baru->terakhir ?? null, $lama->terakhir ?? null])
                ->filter()
                ->map(fn ($t) => Carbon::parse($t))
                ->sortDesc()
                ->first();

            $hasil->put($anak->key, (object) [
                'jumlah' => $jumlah,
                'terakhir' => $tanggal,
            ]);
        }

        return $hasil;
    }

    /**
     * Pilihan periode yang punya nilai terbit untuk satu anak.
     *
     * @return Collection<int, array{tahun_ajaran:string,semester:string}>
     */
    public static function opsiPeriode(AnakPortal $anak): Collection
    {
        $baris = collect();

        if ($anak->studentId() && self::siapGrades()) {
            $baris = $baris->concat(
                Grade::query()
                    ->where('student_id', $anak->studentId())
                    ->where('status', 'published')
                    ->select('tahun_ajaran', 'semester')
                    ->distinct()
                    ->get()
            );
        }

        if ($anak->santriId() && Schema::hasTable('nilai_santri')) {
            $baris = $baris->concat(
                NilaiSantri::query()
                    ->where('santri_id', $anak->santriId())
                    ->where('status', 'published')
                    ->select('tahun_ajaran', 'semester')
                    ->distinct()
                    ->get()
            );
        }

        $opsi = $baris
            ->map(fn ($row) => [
                'tahun_ajaran' => (string) $row->tahun_ajaran,
                'semester' => (string) $row->semester,
            ])
            ->unique(fn (array $row) => $row['tahun_ajaran'].'|'.$row['semester'])
            ->sortByDesc(fn (array $row) => $row['tahun_ajaran'].'|'.$row['semester'])
            ->values();

        if ($opsi->isEmpty()) {
            $opsi->push([
                'tahun_ajaran' => Akademik::tahunAjaranSekarang(),
                'semester' => Akademik::semesterSekarang(),
            ]);
        }

        return $opsi;
    }

    /**
     * Transkrip seluruh periode: hanya nilai yang sudah diterbitkan.
     *
     * Satu entri = satu periode (tahun ajaran + semester), berisi nilai akhir
     * per mata pelajaran. Skema baru (`grades`) diutamakan; periode lama yang
     * belum ada di skema baru diambil dari `nilai_santri`.
     *
     * Bila jenjang anak diketahui, baris dengan jenjang berbeda tidak
     * ditampilkan agar wali hanya melihat data dari jenjang tempat anak
     * terdaftar.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public static function transkrip(AnakPortal $anak): Collection
    {
        $periode = collect();
        $jenjangAnak = Jenjang::normalisasi($anak->jenjang);

        if ($anak->studentId() && self::siapGrades()) {
            $baru = Grade::query()
                ->with('subject')
                ->where('student_id', $anak->studentId())
                ->where('status', 'published')
                ->get();

            $periode = $baru
                ->groupBy(fn (Grade $g) => $g->tahun_ajaran.'|'.$g->semester)
                ->map(function (Collection $rows, string $kunci) use ($jenjangAnak) {
                    [$tahun, $semester] = explode('|', $kunci, 2);

                    $rows = $rows->filter(function (Grade $g) use ($jenjangAnak) {
                        $jenjangBaris = Jenjang::normalisasi($g->subject?->jenjang);

                        return ! $jenjangAnak || ! $jenjangBaris || $jenjangBaris === $jenjangAnak;
                    });

                    if ($rows->isEmpty()) {
                        return null;
                    }

                    return self::barisPeriode($tahun, $semester, self::kelompokkanBaru($rows), 'baru');
                })
                ->filter()
                ->values();
        }

        $sudahAda = $periode
            ->map(fn (array $p) => $p['tahun_ajaran'].'|'.$p['semester'])
            ->all();

        if ($anak->santriId() && Schema::hasTable('nilai_santri')) {
            $lama = NilaiSantri::query()
                ->with('mataPelajaran')
                ->where('santri_id', $anak->santriId())
                ->where('status', 'published')
                ->get()
                ->groupBy(fn (NilaiSantri $n) => $n->tahun_ajaran.'|'.$n->semester);

            foreach ($lama as $kunci => $rows) {
                $kunci = (string) $kunci;

                if (in_array($kunci, $sudahAda, true)) {
                    continue;
                }

                [$tahun, $semester] = explode('|', $kunci, 2);

                $rows = $rows->filter(function (NilaiSantri $n) use ($jenjangAnak) {
                    $jenjangBaris = Jenjang::normalisasi($n->jenjang ?: $n->mataPelajaran?->jenjang);

                    return ! $jenjangAnak || ! $jenjangBaris || $jenjangBaris === $jenjangAnak;
                });

                if ($rows->isEmpty()) {
                    continue;
                }

                $periode->push(self::barisPeriode($tahun, $semester, self::kelompokkanLama($rows), 'lama'));
            }
        }

        // Urutkan periode terbaru lebih dulu. Semester genap berada setelah ganjil.
        return $periode
            ->sortByDesc(fn (array $p) => $p['tahun_ajaran'].'|'.($p['semester'] === 'genap' ? '2' : '1'))
            ->values();
    }

    /**
     * Ringkasan satu periode untuk tampilan transkrip.
     *
     * @param  Collection<int, array<string, mixed>>  $mapel
     * @return array<string, mixed>
     */
    private static function barisPeriode(string $tahun, string $semester, Collection $mapel, string $sumber): array
    {
        $nilai = $mapel->pluck('akhir');
        $rata = $nilai->isNotEmpty() ? round((float) $nilai->avg(), 2) : null;

        return [
            'tahun_ajaran' => $tahun,
            'semester' => $semester,
            'label_semester' => Akademik::labelSemester($semester),
            'mapel' => $mapel->map(fn (array $row) => [
                'nama' => $row['nama'],
                'kkm' => $row['kkm'],
                'nilai' => $row['akhir'],
                'predikat' => $row['predikat'],
                'tuntas' => $row['tuntas'],
            ])->values(),
            'rata' => $rata,
            'predikat' => Akademik::predikat($rata),
            'jumlah' => $mapel->count(),
            'tuntas' => $mapel->where('tuntas', true)->count(),
            'sumber' => $sumber,
        ];
    }

    /**
     * Nilai rapor satu anak untuk periode tertentu.
     *
     * Skema baru (`grades`) diutamakan. Bila kosong, nilai lama
     * (`nilai_santri`) dipakai agar portal tidak kehilangan data historis.
     *
     * @return array<string, mixed>
     */
    public static function rapor(AnakPortal $anak, string $tahun, string $semester): array
    {
        $baru = collect();
        $lama = collect();

        if ($anak->studentId() && self::siapGrades()) {
            $baru = Grade::query()
                ->with('subject')
                ->where('student_id', $anak->studentId())
                ->where('status', 'published')
                ->where('tahun_ajaran', $tahun)
                ->where('semester', $semester)
                ->get();
        }

        if ($baru->isEmpty() && $anak->santriId() && Schema::hasTable('nilai_santri')) {
            $lama = NilaiSantri::query()
                ->with('mataPelajaran')
                ->where('santri_id', $anak->santriId())
                ->where('status', 'published')
                ->where('tahun_ajaran', $tahun)
                ->where('semester', $semester)
                ->get();
        }

        $sumber = $baru->isNotEmpty() ? 'baru' : ($lama->isNotEmpty() ? 'lama' : 'kosong');

        $mapel = $sumber === 'baru'
            ? self::kelompokkanBaru($baru)
            : self::kelompokkanLama($lama);

        $rata = $mapel->avg('akhir');
        $barisCatatan = $mapel->first(fn (array $row) => filled($row['catatan']));
        $catatan = $barisCatatan['catatan'] ?? null;
        $terakhir = $sumber === 'baru' ? $baru->max('published_at') : $lama->max('published_at');

        return [
            'mapel' => $mapel,
            'rata' => $rata,
            'predikat' => Akademik::predikat($rata),
            'jumlah' => $mapel->count(),
            'tuntas' => $mapel->where('tuntas', true)->count(),
            'catatan' => $catatan,
            'terakhir' => $terakhir,
            'baru' => $terakhir && Carbon::parse($terakhir)->gte(now()->subDays(14)),
            'sumber' => $sumber,
        ];
    }

    /**
     * Setoran hafalan (kategori `hafalan`) yang sudah terbit.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public static function tahfidz(AnakPortal $anak, string $tahun, string $semester): Collection
    {
        $baris = collect();

        if ($anak->studentId() && self::siapGrades()) {
            $baris = $baris->concat(
                Grade::query()
                    ->with('subject')
                    ->where('student_id', $anak->studentId())
                    ->where('status', 'published')
                    ->where('tahun_ajaran', $tahun)
                    ->where('semester', $semester)
                    ->where('kategori', 'hafalan')
                    ->get()
                    ->map(fn (Grade $grade) => [
                        'mapel' => $grade->subject?->nama ?? 'Tahfidz',
                        'nilai' => $grade->nilai,
                        'predikat' => Akademik::predikat($grade->nilai),
                        'catatan' => $grade->catatan,
                        'tanggal' => $grade->published_at,
                    ])
            );
        }

        if ($baris->isEmpty() && $anak->santriId() && Schema::hasTable('nilai_santri')) {
            $baris = NilaiSantri::query()
                ->with('mataPelajaran')
                ->where('santri_id', $anak->santriId())
                ->where('status', 'published')
                ->where('tahun_ajaran', $tahun)
                ->where('semester', $semester)
                ->where('kategori', 'hafalan')
                ->get()
                ->map(fn (NilaiSantri $nilai) => [
                    'mapel' => $nilai->mataPelajaran?->nama ?? 'Tahfidz',
                    'nilai' => $nilai->nilai,
                    'predikat' => Akademik::predikat($nilai->nilai),
                    'catatan' => $nilai->catatan,
                    'tanggal' => $nilai->published_at,
                ]);
        }

        return $baris->sortByDesc('tanggal')->values();
    }

    /**
     * Rekap absensi pada rentang semester.
     *
     * @return array{rows:Collection<int,Absensi>,rekap:Collection<string,int>,total:int}
     */
    public static function absensi(AnakPortal $anak, string $tahun, string $semester): array
    {
        $kosong = [
            'rows' => collect(),
            'rekap' => collect(),
            'total' => 0,
        ];

        if (! $anak->santriId() || ! Schema::hasTable('absensi')) {
            return $kosong;
        }

        [$mulai, $selesai] = self::rentangSemester($tahun, $semester);

        $rows = Absensi::query()
            ->where('santri_id', $anak->santriId())
            ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        return [
            'rows' => $rows,
            'rekap' => $rows->groupBy('status_kehadiran')->map->count(),
            'total' => $rows->count(),
        ];
    }

    /**
     * Tagihan dan pembayaran santri.
     *
     * @return array{rows:Collection<int,TagihanSantri>,total:float,dibayar:float,sisa:float}
     */
    public static function keuangan(AnakPortal $anak): array
    {
        $kosong = [
            'rows' => collect(),
            'total' => 0.0,
            'dibayar' => 0.0,
            'sisa' => 0.0,
        ];

        if (! Schema::hasTable('tagihan_santri')) {
            return $kosong;
        }

        $rows = TagihanSantri::query()
            ->where(function ($query) use ($anak) {
                if ($anak->studentId()) {
                    $query->where('student_id', $anak->studentId());
                }

                if ($anak->santriId()) {
                    $method = $anak->studentId() ? 'orWhere' : 'where';
                    $query->{$method}('santri_id', $anak->santriId());
                }
            })
            ->orderByDesc('jatuh_tempo')
            ->orderByDesc('id')
            ->get();

        return [
            'rows' => $rows,
            'total' => (float) $rows->sum('nominal'),
            'dibayar' => (float) $rows->sum('dibayar'),
            'sisa' => (float) $rows->sum(fn (TagihanSantri $row) => $row->sisa()),
        ];
    }

    public static function siapGrades(): bool
    {
        try {
            return Schema::hasTable('grades')
                && Schema::hasColumn('grades', 'status')
                && Schema::hasColumn('grades', 'published_at');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** @return array{0:Carbon,1:Carbon} */
    public static function rentangSemester(string $tahun, string $semester): array
    {
        $mulaiTahun = (int) strtok($tahun, '/');
        $mulaiTahun = $mulaiTahun > 0 ? $mulaiTahun : (int) now()->year;

        if ($semester === 'genap') {
            return [
                Carbon::create($mulaiTahun + 1, 1, 1)->startOfDay(),
                Carbon::create($mulaiTahun + 1, 6, 30)->endOfDay(),
            ];
        }

        return [
            Carbon::create($mulaiTahun, 7, 1)->startOfDay(),
            Carbon::create($mulaiTahun, 12, 31)->endOfDay(),
        ];
    }

    /**
     * @param  Collection<int, Grade>  $baris
     * @return Collection<int, array<string, mixed>>
     */
    private static function kelompokkanBaru(Collection $baris): Collection
    {
        return $baris
            ->groupBy('subject_id')
            ->map(function (Collection $grup) {
                $subject = $grup->first()->subject;
                $nilai = $grup->pluck('nilai')->map(fn ($n) => (float) $n);
                $akhir = round((float) $nilai->avg(), 2);
                $kkm = (float) ($subject?->kkm ?? 75);
                $ambil = fn (string $kategori) => $grup->firstWhere('kategori', $kategori)?->nilai;

                return [
                    'mapel' => $subject,
                    'nama' => $subject?->nama ?? 'Mata pelajaran terhapus',
                    'kkm' => $kkm,
                    'tugas' => $ambil('tugas'),
                    'uts' => $ambil('uts'),
                    'uas' => $ambil('uas'),
                    'hafalan' => $ambil('hafalan'),
                    'akhir' => $akhir,
                    'predikat' => Akademik::predikat($akhir),
                    'tuntas' => $akhir >= $kkm,
                    'catatan' => $grup->first(fn ($g) => filled($g->catatan))?->catatan,
                ];
            })
            ->sortBy('nama')
            ->values();
    }

    /**
     * @param  Collection<int, NilaiSantri>  $baris
     * @return Collection<int, array<string, mixed>>
     */
    private static function kelompokkanLama(Collection $baris): Collection
    {
        return $baris
            ->groupBy('mata_pelajaran_id')
            ->map(function (Collection $grup) {
                $mapel = $grup->first()->mataPelajaran;
                $nilai = $grup->pluck('nilai')->map(fn ($n) => (float) $n);
                $akhir = round((float) $nilai->avg(), 2);
                $kkm = (float) ($mapel?->kkm ?? 70);
                $ambil = fn (string $kategori) => $grup->firstWhere('kategori', $kategori)?->nilai;

                return [
                    'mapel' => $mapel,
                    'nama' => $mapel?->nama ?? 'Mata pelajaran terhapus',
                    'kkm' => $kkm,
                    'tugas' => $ambil('tugas'),
                    'uts' => $ambil('uts'),
                    'uas' => $ambil('uas'),
                    'hafalan' => $ambil('hafalan'),
                    'akhir' => $akhir,
                    'predikat' => Akademik::predikat($akhir),
                    'tuntas' => $akhir >= $kkm,
                    'catatan' => $grup->first(fn ($g) => filled($g->catatan))?->catatan,
                ];
            })
            ->sortBy('nama')
            ->values();
    }
}
