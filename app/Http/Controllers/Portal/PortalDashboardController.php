<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\NilaiSantri;
use App\Models\User;
use App\Support\Akademik;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PortalDashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $daftarAnak = $user->santri()->with(['pendaftaranOnline', 'absensi'])->get();

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

        return view('portal.dashboard', compact(
            'user',
            'daftarAnak',
            'infoNilai',
            'adaNilaiBaru'
        ));
    }
}
