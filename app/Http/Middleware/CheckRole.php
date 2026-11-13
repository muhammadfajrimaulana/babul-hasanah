<?php

namespace App\Http\Middleware;

use App\Support\Akses;
use App\Support\RoleSistem;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware otorisasi berbasis peran.
 *
 * Mendukung dua penulisan sekaligus:
 * - nama peran Spatie  : `super-admin`, `admin-jenjang`, `pengasuh`, `pembina`, `walisantri`, `alumni`
 * - nama lama (route)  : `super_admin`, `admin`, `pengasuh`, `pembina`, `wali_santri`, `alumni`
 *
 * Contoh `role:super_admin,admin` tetap meloloskan `admin-jenjang` karena
 * peran tersebut diturunkan menjadi `admin`. Dengan begitu route lama tidak
 * perlu ditulis ulang.
 */
class CheckRole
{
    /**
     * @param  string  ...$roles  Satu atau banyak peran yang diizinkan.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $dimiliki = $this->peranDimiliki(Auth::user());

        $diminta = [];
        foreach ($roles as $role) {
            $diminta = array_merge($diminta, $this->variasi($role));
        }

        if (array_intersect($dimiliki, array_unique($diminta))) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki hak akses untuk membuka halaman ini.');
    }

    /**
     * Kumpulan token peran yang dimiliki pengguna (Spatie + kolom lama).
     *
     * @return array<int, string>
     */
    private function peranDimiliki($user): array
    {
        $hasil = [];

        if (method_exists($user, 'getRoleNames')) {
            foreach ($user->getRoleNames() as $peran) {
                $hasil = array_merge($hasil, $this->variasi($peran));
            }
        }

        if (! empty($user->role)) {
            $hasil = array_merge($hasil, $this->variasi($user->role));
        }

        // Akun lama yang belum punya peran Spatie: tebak dari role + jenjang.
        $dariLama = RoleSistem::dariLegacy($user->role ?? null, $user->jenjang ?? null);

        if ($dariLama) {
            $hasil = array_merge($hasil, $this->variasi($dariLama));
        }

        return array_values(array_unique(array_filter($hasil)));
    }

    /**
     * Semua variasi penulisan dari satu nama peran.
     *
     * @return array<int, string>
     * @catatan: Sekarang hanya menggunakan Akses::normalisasi() sebagai sumber
     * satunya, sehingga peran lama dan Spatie secara konsisten dipetakan ke
     * kolom `users.role` yang menjadi kunci akses di MODUL/Akses::roles().
     */
    private function variasi(string $role): array
    {
        $hasil = [];

        // HANYA Akses::normalisasi() — menjadi satu sumber kebenaran
        // sehingga peran lama (pengurus, super, wali) dan Spatie (super-admin,
        // admin-jenjang) secara konsisten dipetakan ke users.role.
        $normalisasi = Akses::normalisasi($role);
        if ($normalisasi) {
            $hasil[] = $normalisasi;
            $hasil[] = str_replace('-', '_', $normalisasi);

            // admin-jenjang juga dianggap "admin" (kunci lama).
            if ($normalisasi === 'admin') {
                $hasil[] = 'admin';
            }
        }

        $mentah = strtolower(str_replace([' ', '-'], '_', trim($role)));
        if ($mentah !== '') {
            $hasil[] = $mentah;
            $hasil[] = str_replace('_', '-', $mentah);
        }

        return $hasil;
    }
}
