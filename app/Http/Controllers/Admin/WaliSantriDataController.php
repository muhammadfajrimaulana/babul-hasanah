<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Models\Student;
use App\Models\User;
use App\Support\Akses;
use App\Support\PortalWali;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * Data Wali Santri di panel admin.
 *
 * Menampilkan akun wali beserta anak yang tertaut (melalui `students`,
 * `santri`, atau `pendaftaran_online`). Admin Jenjang hanya melihat wali
 * yang memiliki anak/pendaftaran pada jenjangnya; admin tanpa jenjang tidak
 * melihat data apa pun (fail-closed).
 */
class WaliSantriDataController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $this->queryWali($user);
        $punyaStatus = Schema::hasColumn('users', 'status');

        // Kolom `status` baru ditambahkan lewat migrasi verifikasi akun. Bila
        // belum ada, jangan menambah klausa yang membuat query gagal.
        $jumlahAktif = $punyaStatus
            ? (clone $query)->where('status', 'active')->count()
            : 0;

        if ($request->filled('q')) {
            $cari = '%'.$request->input('q').'%';

            $query->where(function (Builder $q) use ($cari) {
                $q->where('name', 'like', $cari)
                    ->orWhere('email', 'like', $cari)
                    ->orWhere('phone', 'like', $cari);
            });
        }

        $status = strtolower(trim((string) $request->input('status')));
        if ($punyaStatus && in_array($status, ['active', 'pending'], true)) {
            $query->where('status', $status);
        }

        $wali = $query
            ->withCount(['students', 'santri'])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.wali-santri.index', [
            'wali' => $wali,
            'filter' => $request->only(['q', 'status']),
            'jumlahAktif' => $jumlahAktif,
        ]);
    }

    public function show(User $pengguna)
    {
        abort_unless(
            $pengguna->peranSistem() === 'walisantri',
            404,
            'Akun ini bukan wali santri.'
        );

        $pengguna->load(['students.kelas', 'students.santri', 'santri.pendaftaranOnline']);

        $anak = PortalWali::anak($pengguna);
        $pendaftaran = PortalWali::pendaftaran($pengguna);

        $this->pastikanJenjangWali($anak, $pendaftaran);

        return view('dashboard.wali-santri.show', [
            'wali' => $pengguna,
            'anak' => $anak,
            'pendaftaran' => $pendaftaran,
        ]);
    }

    /* ============================================================= Helper */

    /** Query akun wali yang terlihat oleh pengguna. */
    private function queryWali(User $user): Builder
    {
        $query = User::query()->where(function (Builder $q) {
            $q->whereIn('role', ['wali_santri', 'walisantri', 'santri'])
                ->orWhereHas('roles', fn (Builder $r) => $r->where('name', 'walisantri'));
        });

        if (Akses::adminTanpaJenjang($user)) {
            return $query->whereRaw('1 = 0');
        }

        if (! Akses::dibatasiJenjang($user)) {
            return $query;
        }

        $nilaiStudent = Akses::nilaiJenjangDb($user, Student::class, 'jenjang') ?: ['__tidak_ada__'];
        $nilaiSantri = Akses::nilaiJenjangDb($user, Santri::class, 'jenjang_pendidikan') ?: ['__tidak_ada__'];
        $nilaiPpdb = Akses::nilaiJenjangDb($user, PendaftaranOnline::class, 'jenjang_tujuan') ?: ['__tidak_ada__'];

        return $query->where(function (Builder $q) use ($nilaiStudent, $nilaiSantri, $nilaiPpdb) {
            $q->whereHas('students', fn (Builder $s) => $s->whereIn('jenjang', $nilaiStudent))
                ->orWhereHas('santri', fn (Builder $s) => $s->whereIn('jenjang_pendidikan', $nilaiSantri))
                ->orWhereIn('email', Student::query()
                    ->select('parent_email')
                    ->whereIn('jenjang', $nilaiStudent)
                    ->whereNotNull('parent_email'))
                ->orWhereIn('email', PendaftaranOnline::query()
                    ->select('parent_email')
                    ->whereIn('jenjang_tujuan', $nilaiPpdb)
                    ->whereNotNull('parent_email'));
        });
    }

    /**
     * Pastikan admin jenjang hanya membuka wali yang memang punya anak atau
     * pendaftaran pada jenjangnya.
     */
    private function pastikanJenjangWali($anak, $pendaftaran): void
    {
        $user = auth()->user();

        if (Akses::adminTanpaJenjang($user)) {
            abort(403, 'Akun admin Anda belum memiliki jenjang. Hubungi Super Admin.');
        }

        if (! Akses::dibatasiJenjang($user)) {
            return;
        }

        $punyaAnak = $anak->contains(
            fn ($a) => Akses::cocokJenjang($user, $a->jenjang)
        );

        $punyaPpdb = $pendaftaran->contains(
            fn ($p) => Akses::cocokJenjang($user, $p->jenjang_tujuan)
        );

        abort_unless(
            $punyaAnak || $punyaPpdb,
            403,
            'Data wali ini berada di luar jenjang yang Anda tangani.'
        );
    }
}
