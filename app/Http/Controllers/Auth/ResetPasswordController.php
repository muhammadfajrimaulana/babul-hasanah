<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    /**
     * Menampilkan formulir pembuatan kata sandi baru berdasarkan token.
     */
    public function create(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    /**
     * Memvalidasi token lalu menyimpan kata sandi baru.
     *
     * Token bersifat single-use: langsung dihapus setelah kata sandi
     * berhasil diperbarui.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', PasswordRule::min(6)],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi baru minimal :min karakter.',
        ]);

        $email = trim((string) $request->input('email'));

        $user = User::query()->where('email', $email)->first();

        // Gunakan email tersimpan agar pencocokan token konsisten
        // meskipun pengguna menuliskan huruf besar/kecil berbeda.
        $emailToken = $user?->email ?? $email;

        $record = DB::table('password_reset_tokens')
            ->where('email', $emailToken)
            ->first();

        if ($this->tokenTidakValid($record, (string) $request->input('token'))) {
            throw ValidationException::withMessages([
                'email' => 'Tautan reset kata sandi tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.',
            ]);
        }

        if (! $user) {
            // Hapus token yatim agar tidak bisa dipakai lagi.
            DB::table('password_reset_tokens')->where('email', $emailToken)->delete();

            throw ValidationException::withMessages([
                'email' => 'Tautan reset kata sandi tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.',
            ]);
        }

        // Simpan kata sandi baru (di-hash) dan putuskan sesi "ingat saya" lama.
        $user->forceFill([
            'password' => Hash::make((string) $request->input('password')),
        ])->setRememberToken(Str::random(60));

        $user->save();

        // Single-use: token langsung dihapus dari database.
        DB::table('password_reset_tokens')->where('email', $emailToken)->delete();

        return redirect()
            ->route('login')
            ->with('status', 'Kata sandi berhasil diperbarui. Silakan masuk menggunakan kata sandi baru Anda.');
    }

    /**
     * Cek keberadaan, kecocokan hash, dan masa kedaluwarsa token.
     */
    protected function tokenTidakValid(?object $record, string $token): bool
    {
        if (! $record || ! $record->token || ! $record->created_at) {
            return true;
        }

        if (! Hash::check($token, $record->token)) {
            return true;
        }

        // Batas waktu token: 30 menit sejak dibuat.
        return Carbon::parse($record->created_at)
            ->addMinutes(ForgotPasswordController::TOKEN_EXPIRY_MINUTES)
            ->isPast();
    }
}
