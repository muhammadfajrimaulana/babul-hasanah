<?php

namespace App\Mail;

use App\Models\PendaftaranOnline;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendaftaranBerhasilMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PendaftaranOnline $pendaftaran,
        public ?string $kataSandi,
        public bool $akunBaru,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->akunBaru
                ? 'Akun Portal Wali Santri - Pendaftaran PPDB Berhasil'
                : 'Pendaftaran PPDB Berhasil - Akun Portal Anda',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pendaftaran-berhasil',
            with: [
                'namaWali' => $this->pendaftaran->nama_ortu_wali ?: $this->pendaftaran->nama_lengkap,
                'namaSantri' => $this->pendaftaran->nama_lengkap,
                'noPendaftaran' => $this->pendaftaran->no_pendaftaran,
                'jenjang' => $this->pendaftaran->jenjang_tujuan,
                'email' => $this->pendaftaran->email,
                'kataSandi' => $this->kataSandi,
                'akunBaru' => $this->akunBaru,
                'loginUrl' => route('login'),
                'lupaSandiUrl' => route('password.request'),
                'alamatPesantren' => 'Jl. Lintas Pinarik-Papaso Km. 14, Desa Manggis, Kec. Batang Lubu Sutam, Kabupaten Padang Lawas, Sumatera Utara 22763',
                'emailPesantren' => 'info@babulhasanah.sch.id',
            ],
        );
    }
}
