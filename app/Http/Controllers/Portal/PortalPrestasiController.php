<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\PrestasiSantri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PortalPrestasiController extends Controller
{
    /** Daftar prestasi seluruh anak yang tertaut ke akun wali. */
    public function index(Request $request)
    {
        $user = $request->user();
        $anakList = $user->anakQuery()->orderBy('nama_lengkap')->get();

        $prestasi = collect();
        $siap = $anakList->isNotEmpty() && $this->siap();

        if ($siap) {
            $prestasi = PrestasiSantri::query()
                ->with('santri')
                ->whereIn('santri_id', $anakList->pluck('id'))
                ->orderByDesc('tahun')
                ->orderByRaw('peringkat = 0, peringkat')
                ->orderByDesc('id')
                ->get();
        }

        return view('portal.prestasi.index', compact('anakList', 'prestasi', 'siap'));
    }

    /**
     * Halaman tetap aman sebelum migrasi kolom penghubung dijalankan.
     */
    private function siap(): bool
    {
        try {
            return Schema::hasTable('prestasi_santri')
                && Schema::hasColumn('prestasi_santri', 'santri_id');
        } catch (\Throwable $e) {
            return false;
        }
    }
}
