<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PortalWali;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PortalDashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $anakList = PortalWali::anak($user);
        $infoNilai = PortalWali::ringkasanNilaiTerbit($anakList);

        $adaNilaiBaru = $infoNilai->contains(function ($baris) {
            return $baris->terakhir
                && Carbon::parse($baris->terakhir)->gte(now()->subDays(14));
        });

        return view('portal.dashboard', compact(
            'user',
            'anakList',
            'infoNilai',
            'adaNilaiBaru'
        ));
    }
}
