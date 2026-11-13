<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\Akses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Memproses data login dan melakukan redirection sesuai role
     */
    public function store(Request $request)
    {
        // 1. Validasi input form
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Cek percobaan autentikasi
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        // 3. Regenerasi session ID demi keamanan (CBR/Session Fixation Prevention)
        $request->session()->regenerate();

        $user = Auth::user();

        // 4. Cek status akun. Akun wali santri yang mendaftar mandiri berstatus
        //    `pending` sampai Admin Jenjang memverifikasi pendaftarannya.
        if (Schema::hasColumn('users', 'status') && ! $user->sudahAktif()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda masih berstatus PENDING. Tunggu verifikasi Admin Jenjang; '
                    .'kata sandi akan dikirim otomatis ke email Anda setelah pendaftaran disetujui.',
            ]);
        }

        // 5. Redirection cerdas berdasarkan Role Pengguna.
        //    Pemetaan role -> dashboard ada di satu tempat (App\Support\Akses)
        //    supaya jalur login tidak pernah tertukar.
        $tujuan = Akses::ruteSetelahLogin($user);

        if (! $tujuan) {
            return $this->logoutInvalidUser($request);
        }

        return redirect()->intended(route($tujuan));
    }

    /**
     * Logout pengguna yang rolenya tidak dikenali/tidak punya akses
     */
    protected function logoutInvalidUser(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => 'Akun Anda tidak memiliki hak akses yang valid ke sistem.',
        ]);
    }

    /**
     * Memproses Logout Pengguna
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}