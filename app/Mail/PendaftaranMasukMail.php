<?php

namespace App\Mail;

use App\Models\PendaftaranOnline;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendaftaranMasukMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PendaftaranOnline $pendaftaran)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pendaftaran PPDB Diterima - Menunggu Verifikasi',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pendaftaran-masuk',
            with: [
                'namaWali' => $this->pendaftaran->nama_ortu_wali ?: $this->pendaftaran->nama_lengkap,
                'namaSantri' => $this->pendaftaran->nama_lengkap,
                'noPendaftaran' => $this->pendaftaran->no_pendaftaran,
                'jenjang' => $this->pendaftaran->jenjang_tujuan,
                'emailSantri' => $this->pendaftaran->email_santri,
                'loginUrl' => route('login'),
            ],
        );
    }
}
