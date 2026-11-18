<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Support\Akses;
use App\Support\Jenjang;
use App\Support\Kelulusan;
use App\Support\RekapJenjang;

class JenjangController extends Controller
{
    /** Daftar 7 jenjang beserta ringkasan datanya. */
    public function index()
    {
        $user = auth()->user();

        abort_if(
            Akses::adminTanpaJenjang($user),
            403,
            'Akun admin ini belum memiliki jenjang. Hubungi Super Admin.'
        );

        // Admin Jenjang hanya menangani satu jenjang, jadi daftar 7 jenjang
        // tidak relevan. Langsung arahkan ke halaman jenjangnya sendiri.
        if ($info = Akses::jenjangInfo($user)) {
            return redirect()->route('admin.jenjang.show', $info['slug']);
        }

        $ringkasan = RekapJenjang::ringkasan();
        $totalSantri = array_sum(array_column($ringkasan, 'santri'));

        return view('dashboard.jenjang.index', [
            'ringkasan' => $ringkasan,
            'totalSantri' => $totalSantri,
        ]);
    }

    /** Tampilan data satu jenjang. */
    public function show(string $jenjang)
    {
        $user = auth()->user();

        abort_if(
            Akses::adminTanpaJenjang($user),
            403,
            'Akun admin ini belum memiliki jenjang. Hubungi Super Admin.'
        );

        // Admin Jenjang tidak boleh membuka jenjang lain lewat URL langsung.
        abort_unless(
            Akses::cocokJenjang($user, $jenjang),
            403,
            'Jenjang ini di luar kewenangan Anda.'
        );

        $data = Jenjang::cari($jenjang);
        abort_unless($data, 404);

        $ringkasan = collect(RekapJenjang::ringkasan())
            ->firstWhere('slug', $jenjang) ?? [];

        $nilaiSantri = RekapJenjang::nilaiDb(Santri::class, 'jenjang_pendidikan', $jenjang);
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
                ->when(Kelulusan::siapTautan(), fn ($q) => $q->with('santri'))
                ->orderByDesc('tahun_lulus')
                ->latest()
                ->paginate(8, ['*'], 'alumni')
                ->withQueryString()
            : null;

        $totalAlumni = (int) ($ringkasan['alumni'] ?? 0);
        $nilaiSiap = Alumni::siapNilai();
        $alumniPunyaNilai = $nilaiSiap && $nilaiAlumni
            ? Alumni::query()
                ->whereIn('jenjang', $nilaiAlumni)
                ->whereNotNull('nilai_path')
                ->where('nilai_path', '!=', '')
                ->count()
            : 0;

        $input = app(RoleDashboardController::class)->dataInputJenjang($jenjang);

        return view('dashboard.jenjang.show', array_merge([
            'jenjang' => $data,
            'ringkasan' => $ringkasan,
            'santri' => $santri,
            'pendaftar' => $pendaftar,
            'alumni' => $alumni,
            'kelasChart' => RekapJenjang::santriPerKelas($nilaiSantri),
            'statusChart' => RekapJenjang::statusPendaftar($nilaiPendaftar),
            'jenjangPilihan' => $jenjang,
            'modeAgregat' => false,
            'bolehNilai' => $user->bisa('akademik.ubah'),
            'bolehPrestasi' => $user->bisa('prestasi.ubah'),
            'bolehAlumni' => $user->bisa('alumni.ubah'),
            'tautanSiap' => Kelulusan::siapTautan(),
            'nilaiSiap' => $nilaiSiap,
            'totalAlumni' => $totalAlumni,
            'alumniPunyaNilai' => $alumniPunyaNilai,
            'alumniBelumNilai' => max(0, $totalAlumni - $alumniPunyaNilai),
            'alumniTerbaru' => $alumni ? $alumni->getCollection() : collect(),
        ], $input));
    }
}
