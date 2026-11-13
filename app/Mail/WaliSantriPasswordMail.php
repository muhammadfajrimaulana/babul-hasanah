<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email kredensial portal wali santri.
 *
 * Dikirim otomatis setelah Admin Jenjang memverifikasi pendaftaran, atau
 * setelah Super Admin mereset password akun wali.
 */
class WaliSantriPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $wali,
        public string $password,
        public bool $akunBaru = true,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Aktivasi Akun Portal Wali Santri — Pondok Pesantren Babul Hasanah',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.wali_santri_password',
            with: [
                'wali' => $this->wali,
                'password' => $this->password,
                'akunBaru' => $this->akunBaru,
                'urlLogin' => route('login'),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
