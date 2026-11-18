<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $token,
        public int $expiresInMinutes,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permintaan Reset Kata Sandi - Portal Babul Hasanah',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
            with: [
                'nama' => $this->user->name,
                'email' => $this->user->email,
                'url' => $this->resetUrl(),
                'expiresInMinutes' => $this->expiresInMinutes,
                'alamatPesantren' => 'Jl. Lintas Pinarik-Papaso Km. 14, Desa Manggis, Kec. Batang Lubu Sutam, Kabupaten Padang Lawas, Sumatera Utara 22763',
                'emailPesantren' => 'info@babulhasanah.sch.id',
            ],
        );
    }

    /**
     * Tautan menuju formulir kata sandi baru (token dikirim lewat email).
     */
    protected function resetUrl(): string
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->user->email,
        ], false));
    }
}
