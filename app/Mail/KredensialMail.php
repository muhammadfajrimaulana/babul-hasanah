<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email kredensial akun baru (wali santri / alumni).
 *
 * Password tidak pernah disimpan dalam bentuk teks biasa; email ini hanya
 * dikirim sekali saat akun dibuat atau direset oleh admin.
 */
class KredensialMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nama,
        public string $email,
        public string $passwordSementara,
        public string $peran,
        public string $judul,
        public ?string $catatan = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->judul.' - Pondok Pesantren Babul Hasanah');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.kredensial',
            with: [
                'nama' => $this->nama,
                'email' => $this->email,
                'passwordSementara' => $this->passwordSementara,
                'peran' => $this->peran,
                'judul' => $this->judul,
                'catatan' => $this->catatan,
                'loginUrl' => route('login'),
            ],
        );
    }
}
