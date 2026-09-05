<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    // Menampilkan halaman login
    public function create()
    {
        return view('auth.login');
    }

    // Memproses data login
    public function store(Request $request)
    {
        // Validasi input form
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek percobaan autentikasi
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            // Jika salah, kembalikan ke halaman login beserta pesan errornya
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Redirect berdasarkan role yang tersimpan di database
        if ($user->role === 'super_admin' || $user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->role === 'wali_santri') {
            return redirect()->intended(route('portal.dashboard'));
        }

        // Default jika role tidak dikenali
        Auth::logout();
        return redirect()->route('login')->withErrors([
            'email' => 'Akun Anda tidak memiliki hak akses yang valid.',
        ]);
    }

    // Proses logout
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}