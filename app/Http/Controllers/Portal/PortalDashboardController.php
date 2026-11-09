<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PortalDashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $daftarAnak = $user->santri()->with(['pendaftaranOnline', 'absensi'])->get(); 

        return view('portal.dashboard', compact('user', 'daftarAnak'));
    }
}