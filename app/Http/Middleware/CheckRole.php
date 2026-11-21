<?php

namespace App\Http\Middleware;

use App\Support\Akses;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  string  ...$roles (Menerima 1 atau banyak role sekaligus)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Akun nonaktif tidak boleh memakai sistem sama sekali.
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Sekretariat Pesantren.',
            ]);
        }

        // Satu sumber normalisasi: App\Support\Akses.
        $roleUser = Akses::normalisasi($user->role);
        $daftarRole = array_values(array_filter(array_map(
            fn ($role) => Akses::normalisasi((string) $role),
            $roles
        )));

        if ($roleUser && in_array($roleUser, $daftarRole, true)) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki hak akses untuk membuka halaman ini.');
    }
}
