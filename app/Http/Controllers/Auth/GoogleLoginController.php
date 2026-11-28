<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Akses;
use App\Support\CatatanAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

/**
 * Login Google memakai Socialite.
 *
 * Google hanya dipakai sebagai penyedia identitas. Pendaftaran akun baru
 * TIDAK otomatis: email Google harus sudah terdaftar di tabel users dan
 * berstatus aktif. Tombol login disembunyikan bila kredensial belum diisi.
 * Login Google melewati gerbang password sementara karena email sudah diverifikasi Google.
 */
class GoogleLoginController extends Controller
{
    public function redirect()
    {
        if (! self::aktif()) {
            return $this->gagal('Login Google belum tersedia. Silakan masuk dengan email dan kata sandi.');
        }

        $this->pasangRedirectUri();

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        if (! self::aktif()) {
            return $this->gagal('Login Google belum tersedia. Silakan masuk dengan email dan kata sandi.');
        }

        $this->pasangRedirectUri();

        try {
            $googleUser = Socialite::driver('google')->user();
            $email = strtolower(trim((string) $googleUser->getEmail()));

            if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                return $this->gagal('Google tidak memberikan alamat email yang valid.');
            }

            // Email akun disimpan apa adanya; perbandingan dibuat case-insensitive.
            $user = User::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();

            if (! $user) {
                return $this->gagal(
                    'Email '.$email.' belum terdaftar. Hubungi Admin Jenjang untuk dibuatkan akun terlebih dahulu.'
                );
            }

            if (! $user->is_active) {
                return $this->gagal('Akun Anda sedang dinonaktifkan. Silakan hubungi Sekretariat Pesantren.');
            }

            $tujuan = Akses::ruteSetelahLogin($user);

            if (! $tujuan) {
                return $this->gagal('Akun Anda tidak memiliki hak akses yang valid ke sistem.');
            }

            // Google sudah memverifikasi kepemilikan email, sehingga gerbang
            // password sementara tidak perlu dipakai pada jalur OAuth.
            $passwordSementaraDilewati = (bool) $user->must_change_password;

            $user->forceFill([
                'email_verified_at' => $user->email_verified_at ?? now(),
                'last_login_at' => now(),
                'must_change_password' => false,
                'temp_password_expires_at' => null,
            ])->save();

            Auth::login($user);
            $request->session()->regenerate();

            CatatanAktivitas::catat('login_google', $user, [
                'email' => $email,
                'password_sementara_dilewati' => $passwordSementaraDilewati,
            ]);

            return redirect()->route($tujuan)
                ->with('success', 'Berhasil masuk dengan akun Google.');
        } catch (\Throwable $e) {
            report($e);

            return $this->gagal('Login Google gagal. Silakan coba lagi atau masuk dengan email dan kata sandi.');
        }
    }

    /**
     * Socialite membaca redirect dari config. Nilai default sengaja dihitung
     * saat request karena helper url() tidak tersedia saat config dimuat di CLI.
     */
    private function pasangRedirectUri(): void
    {
        config(['services.google.redirect' => self::redirectUri()]);
    }

    public static function redirectUri(): string
    {
        $dienv = trim((string) config('services.google.redirect'));

        if ($dienv !== '') {
            return $dienv;
        }

        return url('/auth/google/callback');
    }

    /** Apakah tombol Google tersedia di halaman login. */
    public static function aktif(): bool
    {
        // Jangan tampilkan tombol / jalankan Socialite bila paketnya belum
        // terpasang di vendor (composer install belum dijalankan).
        return class_exists(\Laravel\Socialite\Facades\Socialite::class)
            && filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'));
    }

    private function gagal(string $pesan)
    {
        Auth::logout();

        return redirect()->route('login')->withErrors(['email' => $pesan]);
    }
}
