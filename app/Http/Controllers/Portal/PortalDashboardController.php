<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Nanti di sini bisa diload relasi data santri yang terhubung ke wali santri yang sedang login
        // Contoh: $santri = $user->santri;

        return view('portal.dashboard', compact('user'));
    }
}