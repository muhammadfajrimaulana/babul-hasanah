<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Support\PortalWali;
use Illuminate\Http\Request;

/**
 * Ganti anak aktif dari Multi-Santri Switcher di header portal.
 */
class PortalAnakController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'anak' => ['required', 'string', 'max:30'],
            'redirect' => ['nullable', 'string', 'max:2048'],
        ]);

        $daftar = PortalWali::anak($request->user());
        $anak = $daftar->firstWhere('key', (string) $request->input('anak'));

        abort_unless($anak, 403, 'Data anak ini bukan milik akun Anda.');

        $request->session()->put('portal.anak', $anak->key);

        $tujuan = (string) $request->input('redirect');

        if ($tujuan === '' || ! str_starts_with($tujuan, url('/'))) {
            $tujuan = route('portal.dashboard');
        }

        return redirect()->to($tujuan);
    }
}
