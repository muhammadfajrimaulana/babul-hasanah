<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Support\Jenjang;
use App\Support\RekapJenjang;

class JenjangController extends Controller
{
    /** Daftar 7 jenjang beserta ringkasan datanya. */
    public function index()
    {
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
            'kelasChart' => RekapJenjang::santriPerKelas($nilaiSantri),
            'statusChart' => RekapJenjang::statusPendaftar($nilaiPendaftar),
        ]);
    }
}
