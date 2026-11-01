<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\InfoHarian;
use Illuminate\Support\Facades\Schema;

class InfoHarianController extends Controller
{
    public function index()
    {
        $info = collect();
        $jadwal = collect();
        $infoHistori = collect();

        // Guard hasTable: halaman tetap tampil walau migrasi info_harian belum jalan.
        if (Schema::hasTable('info_harian')) {
            $info = InfoHarian::info()->aktif()->berlaku()->sudahTerbit()
                ->orderByDesc('penting')
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->get();

            $jadwal = InfoHarian::jadwal()->aktif()->berlaku()
                ->orderBy('jam')
                ->orderBy('urutan')
                ->orderBy('id')
                ->get();

            // Arsip: info yang sudah kedaluwarsa, hanya sampai 3 hari setelahnya.
            $infoHistori = InfoHarian::info()->aktif()->sudahTerbit()
                ->histori(InfoHarian::HISTORI_HARI)
                ->orderByDesc('berlaku_sampai')
                ->orderByDesc('id')
                ->get();
        }

        // Jam sekarang "HH.MM" agar bisa dibandingkan langsung dengan kolom `jam`.
        $jamSekarang = now()->format('H.i');

        $jadwalBerikutnya = $jadwal->first(
            fn ($j) => str_replace(':', '.', (string) $j->jam) >= $jamSekarang
        );

        return view('pages.info-harian', [
            'info'             => $info,
            'infoHistori'      => $infoHistori,
            'infoPenting'      => $info->where('penting', true),
            'jadwal'           => $jadwal,
            'jadwalBerikutnya' => $jadwalBerikutnya,
            'kategori'         => $info->pluck('kategori')->filter()->unique()->values(),
            'jamSekarang'      => $jamSekarang,
            'tanggalHariIni'   => now()->locale('id')->translatedFormat('l, d F Y'),
            'historiHari'      => InfoHarian::HISTORI_HARI,
        ]);
    }
}
