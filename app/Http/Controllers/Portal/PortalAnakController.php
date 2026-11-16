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

        // Hanya izinkan redirect ke host aplikasi ini. Perbandingan host (bukan
        // sekadar awalan string) mencegah open redirect seperti
        // `https://domain-aplikasi.com.evil.test`.
        if ($tujuan !== '') {
            $bagian = parse_url($tujuan);
            $hostTujuan = $bagian['host'] ?? null;
            $hostAplikasi = parse_url(url('/'), PHP_URL_HOST);

            $skemaSah = ! isset($bagian['scheme'])
                || in_array(strtolower((string) $bagian['scheme']), ['http', 'https'], true);

            $hostSah = $hostTujuan === null
                || ($hostAplikasi && strcasecmp($hostTujuan, $hostAplikasi) === 0);

            if (! $skemaSah || ! $hostSah) {
                $tujuan = '';
            }
        }

        if ($tujuan === '') {
            $tujuan = route('portal.dashboard');
        }

        return redirect()->to($tujuan);
    }
}
