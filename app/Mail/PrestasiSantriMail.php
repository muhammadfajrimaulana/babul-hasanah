<?php

namespace App\Mail;

use App\Models\PrestasiSantri;
use App\Models\Santri;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PrestasiSantriMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PrestasiSantri $prestasi,
        public Santri $santri,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Prestasi Baru Ananda '.$this->santri->nama_lengkap,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.prestasi-santri',
            with: [
                'namaWali' => $this->santri->user?->name ?? ($this->santri->nama_wali ?: 'Bapak/Ibu Wali Santri'),
                'namaSantri' => $this->santri->nama_lengkap,
                'kelas' => $this->santri->kelas ?: '-',
                'jenjang' => $this->santri->jenjang_pendidikan ?: '-',
                'judul' => $this->prestasi->judul,
                'kategori' => $this->prestasi->kategori,
                'tingkat' => $this->prestasi->tingkat,
                'wilayah' => $this->prestasi->wilayah,
                'tahun' => $this->prestasi->tahun,
                'label' => $this->prestasi->label,
                'deskripsi' => $this->prestasi->deskripsi,
                'portalUrl' => route('portal.prestasi'),
                'publicUrl' => route('prestasi.index'),
                'alamatPesantren' => 'Jl. Lintas Pinarik-Papaso Km. 14, Desa Manggis, Kec. Batang Lubu Sutam, Kabupaten Padang Lawas, Sumatera Utara 22763',
                'emailPesantren' => 'info@babulhasanah.sch.id',
            ],
        );
    }
}
