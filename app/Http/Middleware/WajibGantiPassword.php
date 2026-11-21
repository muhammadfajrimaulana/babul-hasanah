<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memaksa akun dengan password sementara untuk mengganti password
 * sebelum bisa membuka halaman lain.
 */
class WajibGantiPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda sedang dinonaktifkan.',
            ]);
        }

        if (! $user->must_change_password) {
            return $next($request);
        }

        if ($user->passwordSementaraKedaluwarsa()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Password sementara sudah kedaluwarsa. Silakan hubungi Admin Jenjang untuk reset password.',
            ]);
        }

        if ($request->routeIs('password.change', 'password.change.update', 'logout')) {
            return $next($request);
        }

        return redirect()->route('password.change')
            ->with('warning', 'Demi keamanan, ganti password sementara Anda terlebih dahulu.');
    }
}
