<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Models\Student;
use App\Models\User;
use App\Support\Akses;
use App\Support\Jenjang;
use App\Support\RekapJenjang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JenjangController extends Controller
{
    /** Daftar jenjang beserta ringkasan datanya (sesuai wewenang pengguna). */
    public function index()
    {
        $user = auth()->user();
        $ringkasan = RekapJenjang::ringkasan();

        // Admin Jenjang hanya melihat jenjangnya; admin tanpa jenjang tidak
        // melihat data apa pun (fail-closed).
        if (Akses::adminTanpaJenjang($user)) {
            $ringkasan = [];
        } elseif (Akses::dibatasiJenjang($user)) {
            $slug = Akses::jenjangSlug($user);
            $ringkasan = array_values(array_filter(
                $ringkasan,
                fn (array $baris) => $baris['slug'] === $slug
            ));
        }

        $totalSantri = array_sum(array_column($ringkasan, 'santri'));

        return view('dashboard.jenjang.index', [
            'ringkasan' => $ringkasan,
            'totalSantri' => $totalSantri,
        ]);
    }

    /** Tampilan data satu jenjang. */
    public function show(string $jenjang)
    {
        $data = Jenjang::cari($jenjang);
        abort_unless($data, 404);

        // Admin Jenjang tidak boleh membuka jenjang lain.
        abort_unless(
            Akses::cocokJenjang(auth()->user(), $jenjang),
            403,
            'Jenjang ini berada di luar wewenang Anda.'
        );

        $ringkasan = collect(RekapJenjang::ringkasan())
            ->firstWhere('slug', $jenjang) ?? [];

        $nilaiSantri = RekapJenjang::nilaiDb(Santri::class, 'jenjang_pendidikan', $jenjang);
        $nilaiStudent = RekapJenjang::nilaiDb(Student::class, 'jenjang', $jenjang);
        $nilaiPendaftar = RekapJenjang::nilaiDb(PendaftaranOnline::class, 'jenjang_tujuan', $jenjang);
        $nilaiAlumni = RekapJenjang::nilaiDb(Alumni::class, 'jenjang', $jenjang);

        $santri = RekapJenjang::siap(Santri::class)
            ? Santri::query()
                ->whereIn('jenjang_pendidikan', $nilaiSantri)
                ->latest()
                ->paginate(8, ['*'], 'santri')
                ->withQueryString()
            : null;

        $pendaftar = RekapJenjang::siap(PendaftaranOnline::class)
            ? PendaftaranOnline::query()
                ->whereIn('jenjang_tujuan', $nilaiPendaftar)
                ->latest()
                ->paginate(8, ['*'], 'pendaftar')
                ->withQueryString()
            : null;

        $alumni = RekapJenjang::siap(Alumni::class)
            ? Alumni::query()
                ->whereIn('jenjang', $nilaiAlumni)
                ->orderByDesc('tahun_lulus')
                ->latest()
                ->paginate(8, ['*'], 'alumni')
                ->withQueryString()
            : null;

        return view('dashboard.jenjang.show', [
            'jenjang' => $data,
            'ringkasan' => $ringkasan,
            'santri' => $santri,
            'pendaftar' => $pendaftar,
            'alumni' => $alumni,
            'wali' => $this->waliJenjang($nilaiStudent, $nilaiSantri, $nilaiPendaftar),
            'kelasChart' => RekapJenjang::santriPerKelas($nilaiSantri),
            'statusChart' => RekapJenjang::statusPendaftar($nilaiPendaftar),
        ]);
    }

    /**
     * Akun wali yang memiliki anak/pendaftaran pada jenjang ini.
     *
     * @param  array<int, string>  $nilaiStudent
     * @param  array<int, string>  $nilaiSantri
     * @param  array<int, string>  $nilaiPendaftar
     */
    private function waliJenjang(array $nilaiStudent, array $nilaiSantri, array $nilaiPendaftar)
    {
        if (! RekapJenjang::siap(User::class)) {
            return null;
        }

        $email = collect();
        $id = collect();

        if (RekapJenjang::siap(Student::class) && $nilaiStudent !== []) {
            if (Schema::hasColumn('students', 'parent_email')) {
                $email = $email->merge(
                    Student::query()
                        ->whereIn('jenjang', $nilaiStudent)
                        ->whereNotNull('parent_email')
                        ->pluck('parent_email')
                );
            }

            // Anak yang ditautkan langsung ke akun wali lewat students.user_id.
            $id = $id->merge(
                Student::query()
                    ->whereIn('jenjang', $nilaiStudent)
                    ->whereNotNull('user_id')
                    ->pluck('user_id')
            );
        }

        if (RekapJenjang::siap(Santri::class)) {
            $id = $id->merge(
                Santri::query()
                    ->whereIn('jenjang_pendidikan', $nilaiSantri)
                    ->whereNotNull('user_id')
                    ->pluck('user_id')
            );
        }

        if (RekapJenjang::siap(PendaftaranOnline::class)) {
            $email = $email->merge(
                PendaftaranOnline::query()
                    ->whereIn('jenjang_tujuan', $nilaiPendaftar)
                    ->whereNotNull('parent_email')
                    ->pluck('parent_email')
            );
        }

        $email = $email->filter()
            ->map(fn ($e) => strtolower(trim((string) $e)))
            ->unique()
            ->values()
            ->all();

        $id = $id->filter()->unique()->values()->all();

        if ($email === [] && $id === []) {
            return null;
        }

        return User::query()
            ->whereIn('role', ['wali_santri', 'walisantri', 'santri'])
            ->where(function ($query) use ($email, $id) {
                if ($email !== []) {
                    $query->whereIn(DB::raw('LOWER(email)'), $email);
                }

                if ($id !== []) {
                    $query->orWhereIn('id', $id);
                }
            })
            ->withCount(['students', 'santri'])
            ->orderBy('name')
            ->paginate(8, ['*'], 'wali')
            ->withQueryString();
    }
}
