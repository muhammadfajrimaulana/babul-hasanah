<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PendaftaranOnline;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalSantri' => 485,
            'totalPendaftar' => PendaftaranOnline::count(), // Dibuat dinamis
            'totalBerita' => 38,
            'totalAsatidz' => 32,
            // Ambil 5 data pendaftar terbaru berdasarkan yang paling baru dibuat
            'pendaftarTerbaru' => PendaftaranOnline::latest()->take(5)->get(),
        ];

        return view('dashboard.index', $data);
    }
}