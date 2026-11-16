<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles (Menerima 1 atau banyak role sekaligus)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. Cek apakah role user ada di dalam daftar $roles yang diizinkan.
        //    Normalisasi agar "Super Admin", "superadmin", atau "SUPER_ADMIN"
        //    tetap dianggap sama dan tidak salah jalur.
        $roleUser = strtolower(str_replace([' ', '-'], '_', trim((string) $user->role)));
        $roleUser = $roleUser === 'superadmin' ? 'super_admin' : $roleUser;

        $daftarRole = array_map(
            fn ($role) => strtolower(str_replace([' ', '-'], '_', trim((string) $role))),
            $roles
        );

        if (in_array($roleUser, $daftarRole, true)) {
            return $next($request);
        }

        // 3. Jika tidak punya akses, berikan respons Error 403 / Redirect
        abort(403, 'Anda tidak memiliki hak akses untuk membuka halaman ini.');
    }
}