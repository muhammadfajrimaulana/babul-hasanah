<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WaliSantriController extends Controller
{
    /** Pastikan hanya akun wali santri yang boleh membuka halaman ini. */
    private function pastikanWali(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->peranSistem() === 'walisantri',
            403,
            'Halaman ini hanya untuk akun Wali Santri.'
        );
    }

    /** Riwayat PPDB milik wali yang sedang login (email wali, bukan email santri). */
    private function riwayatPendaftaran(string $email): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\PendaftaranOnline::query()
            ->where(function ($q) use ($email) {
                $q->where('parent_email', $email)
                    ->orWhere(function ($lama) use ($email) {
                        $lama->whereNull('parent_email')->where('email', $email);
                    });
            })
            ->latest()
            ->take(5)
            ->get();
    }

    /**
     * Show the wali santri dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->pastikanWali();

        // Get the wali santri's children data
        $wali = Auth::user();
        $anakSaya = null;

        // Try to get associated santri data
        if ($wali->santri()->exists()) {
            $anakSaya = $wali->santri->first();
        }

        // Get pendaftaran history
        $riwayatPendaftaran = $this->riwayatPendaftaran($wali->email);

        return view('wali_santri.dashboard', [
            'anakSaya' => $anakSaya,
            'riwayatPendaftaran' => $riwayatPendaftaran,
        ]);
    }

    /**
     * Show the wali santri profile.
     *
     * @return \Illuminate\View\View
     */
    public function profil()
    {
        $this->pastikanWali();

        return view('wali_santri.profil');
    }

    /**
     * Show the wali santri nilai (grades) page.
     *
     * @return \Illuminate\View\View
     */
    public function nilai()
    {
        $this->pastikanWali();

        return view('wali_santri.nilai');
    }

    /**
     * Show the wali santri anak data page.
     *
     * @return \Illuminate\View\View
     */
    public function anak()
    {
        $this->pastikanWali();

        $wali = Auth::user();
        $anakSaya = null;

        if ($wali->santri()->exists()) {
            $anakSaya = $wali->santri->first();
        }

        return view('wali_santri.anak', [
            'anakSaya' => $anakSaya,
        ]);
    }

    /**
     * Show the wali santri pendaftaran history page.
     *
     * @return \Illuminate\View\View
     */
    public function pendaftaran()
    {
        $this->pastikanWali();

        $wali = Auth::user();
        $riwayatPendaftaran = $this->riwayatPendaftaran($wali->email);

        return view('wali_santri.pendaftaran', [
            'riwayatPendaftaran' => $riwayatPendaftaran,
        ]);
    }
}