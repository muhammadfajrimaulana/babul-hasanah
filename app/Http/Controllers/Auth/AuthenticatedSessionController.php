<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // 4. Cek status akun (opsional: jika tabel users memiliki kolom status)
        if (isset($user->is_active) && !$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Sekretariat Pesantren.',
            ]);
        }

        // 5. Redirection cerdas berdasarkan Role Pengguna
        return match ($user->role) {
            'super_admin', 'admin' => redirect()->intended(route('admin.dashboard')),
            'pengasuh'             => redirect()->intended(route('admin.dashboard')), // atau route('pengasuh.dashboard')
            'pembina'              => redirect()->intended(route('admin.dashboard')),
            'wali_santri'          => redirect()->intended(route('portal.dashboard')),
            default                => $this->logoutInvalidUser($request),
        };
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