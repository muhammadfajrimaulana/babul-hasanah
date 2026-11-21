<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\Akses;
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
     * Memproses data login dan mengarahkan sesuai role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'agree' => ['accepted'],
        ], [
            'agree.accepted' => 'Centang persetujuan Syarat & Ketentuan terlebih dahulu sebelum masuk ke Dashboard.',
        ]);

        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Akun nonaktif tidak boleh masuk.
        if (! $user->is_active) {
            return $this->logoutWithError($request, 'Akun Anda sedang dinonaktifkan. Silakan hubungi Sekretariat Pesantren.');
        }

        // Password sementara yang sudah lewat 24 jam harus direset admin.
        if ($user->passwordSementaraKedaluwarsa()) {
            return $this->logoutWithError($request, 'Password sementara sudah kedaluwarsa. Silakan hubungi Admin Jenjang untuk reset password.');
        }

        $tujuan = Akses::ruteSetelahLogin($user);

        if (! $tujuan) {
            return $this->logoutWithError($request, 'Akun Anda tidak memiliki hak akses yang valid ke sistem.');
        }

        $user->forceFill(['last_login_at' => now()])->save();

        // Wajib ganti password sebelum masuk dashboard.
        if ($user->must_change_password) {
            return redirect()->route('password.change')
                ->with('warning', 'Demi keamanan, ganti password sementara Anda terlebih dahulu.');
        }

        return redirect()->route($tujuan);
    }

    protected function logoutWithError(Request $request, string $pesan)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors(['email' => $pesan]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
