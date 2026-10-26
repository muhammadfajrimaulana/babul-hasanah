<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\PendaftaranOnline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();

        $totalSantri = $this->hitung(\App\Models\Santri::class);

        $data = [
            // Santri (sesuaikan nama model & kolom jika berbeda)
            'totalSantri' => $totalSantri,
            'santriPutra' => $this->hitung(\App\Models\Santri::class, fn ($q) => $q->whereIn('jenis_kelamin', ['L', 'Laki-laki', 'Laki-Laki'])),
            'santriPutri' => $this->hitung(\App\Models\Santri::class, fn ($q) => $q->whereIn('jenis_kelamin', ['P', 'Perempuan'])),

            // Pendaftar
            'totalPendaftar'    => PendaftaranOnline::count(),
            'pendaftarMenunggu' => PendaftaranOnline::where(function ($q) {
                $q->whereNull('status_pendaftaran')
                  ->orWhereNotIn('status_pendaftaran', ['diterima', 'ditolak', 'Diterima', 'Ditolak']);
            })->count(),
            'pendaftarTerbaru'  => PendaftaranOnline::latest()->take(5)->get(),

            // Berita
            'totalBerita'    => Berita::published()->count(),
            'beritaBulanIni' => Berita::published()
                ->whereMonth('published_at', $now->month)
                ->whereYear('published_at', $now->year)
                ->count(),

            // Asatidz / struktur organisasi
            'totalAsatidz' => $this->hitung(\App\Models\Kepengurusan::class),

            // Fitur website
            'jmlGaleri'   => $this->hitung(\App\Models\Album::class),
            'totalAlumni'   => $this->hitung(\App\Models\Alumni::class),
            'totalPengguna' => $this->hitung(\App\Models\User::class),
            'isSuperAdmin'  => in_array(strtolower((string) (auth()->user()->role ?? '')), ['super_admin', 'superadmin', 'super admin', 'super'], true),
            'jmlPrestasi' => $this->hitung(\App\Models\PrestasiSantri::class),

            // Tahun ajaran otomatis (Juli = awal tahun ajaran baru)
            'tahunAjaran' => $now->month >= 7
                ? $now->year . '/' . ($now->year + 1)
                : ($now->year - 1) . '/' . $now->year,

            // Grafik, komposisi, agenda
            'tren'      => $this->trenPendaftar(),
            'komposisi' => $this->komposisiSantri($totalSantri),
            'agenda'    => $this->agendaTerdekat(),
        ];

        return view('dashboard.index', $data);
    }

    /** Cek model aman: false kalau file model tidak ada atau kosong. */
    private function modelAda(string $model): bool
    {
        try {
            return class_exists($model);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Hitung data dengan aman: kalau model/tabel/kolom belum ada, hasilnya 0. */
    private function hitung(string $model, ?callable $filter = null): int
    {
        if (! $this->modelAda($model)) {
            return 0;
        }

        /** @var Model $instance */
        $instance = new $model;

        if (! Schema::hasTable($instance->getTable())) {
            return 0;
        }

        try {
            $query = $model::query();
            if ($filter) {
                $filter($query);
            }
            return $query->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /** Jumlah pendaftar per bulan, 6 bulan terakhir. */
    private function trenPendaftar(): array
    {
        $tren = [];
        for ($i = 5; $i >= 0; $i--) {
            $awal  = now()->subMonths($i)->startOfMonth();
            $label = $awal->copy()->locale('id')->translatedFormat('M');
            $tren[$label] = PendaftaranOnline::whereBetween('created_at', [$awal, $awal->copy()->endOfMonth()])->count();
        }
        return $tren;
    }

    /** Komposisi santri per jenjang: [nama, persen, jumlah, warna, ikon] */
    private function komposisiSantri(int $total): array
    {
        $model = \App\Models\Santri::class;
        if ($total === 0 || ! $this->modelAda($model)) {
            return [];
        }

        try {
            $rows = $model::query()->selectRaw('jenjang, COUNT(*) as jml')->groupBy('jenjang')->get();
        } catch (\Throwable $e) {
            return []; // kolom "jenjang" belum ada
        }

        $warna = ['#17402f', '#b08d3e', '#9db8a8', '#3b82c4', '#8b5cc6', '#d6525a', '#0e9f8e'];

        return $rows->values()->map(fn ($r, $i) => [
            $r->jenjang ?: 'Belum diatur',
            round($r->jml / $total * 100),
            $r->jml,
            $warna[$i % count($warna)],
            'fa-graduation-cap',
        ])->all();
    }

    /** Agenda terdekat dari model Kegiatan: [tanggal, judul, jam, tempat, bulan] */
    private function agendaTerdekat(): array
    {
        $model = \App\Models\Kegiatan::class;
        if (! $this->modelAda($model) || ! Schema::hasTable((new $model)->getTable())) {
            return [];
        }

        try {
            return $model::query()
                ->whereDate('tanggal', '>=', today())
                ->orderBy('tanggal')
                ->take(3)
                ->get()
                ->map(function ($k) {
                    $tgl = \Illuminate\Support\Carbon::parse($k->tanggal)->locale('id');
                    return [
                        $tgl->format('d'),
                        $k->judul ?? $k->nama ?? 'Kegiatan',
                        $k->waktu ?? $k->jam ?? '-',
                        $k->lokasi ?? $k->tempat ?? '-',
                        strtoupper($tgl->translatedFormat('M')),
                    ];
                })->all();
        } catch (\Throwable $e) {
            return []; // kolom tanggal/judul belum sesuai
        }
    }
}
