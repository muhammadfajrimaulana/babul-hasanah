<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\DokumenNilai;
use App\Models\NilaiSantri;
use App\Models\PrestasiSantri;
use App\Models\User;
use App\Support\Akademik;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class PortalDashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $daftarAnak = $user->anakQuery()->with(['pendaftaranOnline', 'absensi'])->get();

        // Ringkasan nilai terbit per anak untuk indikator notifikasi di beranda.
        $infoNilai = collect();
        $adaNilaiBaru = false;

        if (Akademik::siap() && $daftarAnak->isNotEmpty()) {
            $infoNilai = NilaiSantri::query()
                ->whereIn('santri_id', $daftarAnak->pluck('id'))
                ->where('status', 'published')
                ->selectRaw('santri_id, COUNT(*) as jumlah, MAX(published_at) as terakhir')
                ->groupBy('santri_id')
                ->get()
                ->keyBy('santri_id');

            $adaNilaiBaru = $infoNilai->contains(function ($baris) {
                return $baris->terakhir
                    && Carbon::parse($baris->terakhir)->gte(now()->subDays(14));
            });
        }

        // Nilai terbaru per anak untuk ringkasan di beranda portal.
        $nilaiTerbaru = collect();

        if (Akademik::siap() && $daftarAnak->isNotEmpty()) {
            $nilaiTerbaru = NilaiSantri::query()
                ->with('mataPelajaran')
                ->whereIn('santri_id', $daftarAnak->pluck('id'))
                ->where('status', 'published')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->get()
                ->groupBy('santri_id')
                ->map(fn ($baris) => $baris->take(4)->values());
        }

        // Rapor PDF baru juga dianggap sebagai nilai baru di beranda portal.
        if ($daftarAnak->isNotEmpty() && DokumenNilai::siap()) {
            $adaDokumenBaru = DokumenNilai::query()
                ->whereIn('santri_id', $daftarAnak->pluck('id'))
                ->where('status', 'published')
                ->where('published_at', '>=', now()->subDays(14))
                ->exists();

            $adaNilaiBaru = $adaNilaiBaru || $adaDokumenBaru;
        }

        // Prestasi terbaru ananda untuk kartu notifikasi di beranda portal.
        $prestasiTerbaru = collect();
        $adaPrestasiBaru = false;

        if ($daftarAnak->isNotEmpty() && $this->prestasiSiap()) {
            $prestasiTerbaru = PrestasiSantri::query()
                ->whereIn('santri_id', $daftarAnak->pluck('id'))
                ->latest()
                ->take(5)
                ->get();

            $adaPrestasiBaru = $prestasiTerbaru->contains(function (PrestasiSantri $prestasi) {
                return $prestasi->created_at && $prestasi->created_at->gte(now()->subDays(14));
            });
        }

        return view('portal.dashboard', compact(
            'user',
            'daftarAnak',
            'infoNilai',
            'nilaiTerbaru',
            'adaNilaiBaru',
            'prestasiTerbaru',
            'adaPrestasiBaru'
        ));
    }

    /** Kolom penghubung prestasi mungkin belum ada sebelum migrasi. */
    private function prestasiSiap(): bool
    {
        try {
            return Schema::hasTable('prestasi_santri')
                && Schema::hasColumn('prestasi_santri', 'santri_id');
        } catch (\Throwable $e) {
            return false;
        }
    }
}
