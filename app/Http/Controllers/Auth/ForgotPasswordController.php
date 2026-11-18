<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * Masa berlaku tautan reset kata sandi (menit).
     */
    public const TOKEN_EXPIRY_MINUTES = 30;

    /**
     * Jeda minimal sebelum email yang sama boleh meminta tautan lagi (detik).
     * Mencegah spam dan pembuatan token beruntun.
     */
    public const RESEND_COOLDOWN_SECONDS = 60;

    /**
     * Menampilkan formulir permintaan tautan reset kata sandi.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Membuat token reset, menyimpan hash-nya, lalu mengirim email.
     *
     * Email Enumeration Protection: apa pun hasilnya (email ada atau tidak),
     * pengguna selalu menerima pesan sukses yang sama sehingga penyerang
     * tidak bisa menebak email mana yang terdaftar.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        $email = trim((string) $request->input('email'));

        $user = User::query()->where('email', $email)->first();

        if ($user) {
            $this->sendResetLink($user);
        }

        return back()
            ->withInput($request->only('email'))
            ->with(
                'status',
                'Jika email tersebut terdaftar pada akun portal, kami telah mengirim tautan reset kata sandi. Silakan periksa kotak masuk atau folder spam.'
            );
    }

    /**
     * Generate token acak, simpan versi hash-nya, dan kirim email reset.
     */
    protected function sendResetLink(User $user): void
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->first();

        // Masih dalam masa jeda: jangan kirim email berulang.
        if ($record && $record->created_at
            && Carbon::parse($record->created_at)->addSeconds(self::RESEND_COOLDOWN_SECONDS)->isFuture()) {
            return;
        }

        // Token acak kriptografis (64 karakter). Yang disimpan hanya hash-nya.
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        try {
            Mail::to($user->email, $user->name)->send(
                new ResetPasswordMail($user, $token, self::TOKEN_EXPIRY_MINUTES)
            );
        } catch (\Throwable $e) {
            // Dicatat untuk admin, tetapi pengguna tetap melihat pesan generik
            // agar status email tidak bocor.
            report($e);
        }
    }
}
