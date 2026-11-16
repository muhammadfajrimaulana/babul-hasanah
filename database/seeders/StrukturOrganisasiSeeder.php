<?php

namespace Database\Seeders;

use App\Models\StrukturOrganisasi;
use Illuminate\Database\Seeder;

class StrukturOrganisasiSeeder extends Seeder
{
    /** Data contoh = isi halaman lama. Ganti lewat dashboard admin. Hanya jalan jika tabel masih kosong. */
    public function run(): void
    {
        if (StrukturOrganisasi::count() > 0) {
            return;
        }

        $pengasuh = StrukturOrganisasi::create([
            'kategori' => 'bagan', 'nama' => 'KH. Nama Pengasuh', 'jabatan' => 'Pengasuh / Pimpinan',
            'ikon' => 'fa-user-tie', 'urutan' => 1,
        ]);

        $mudir = StrukturOrganisasi::create([
            'kategori' => 'bagan', 'parent_id' => $pengasuh->id, 'nama' => 'Ust. Nama Mudir',
            'jabatan' => 'Mudir / Kepala Pondok', 'ikon' => 'fa-user', 'urutan' => 1,
        ]);

        foreach ([
            ['Ust. Nama Sekretaris', 'Sekretaris',        'fa-pen-nib'],
            ['Ust. Nama Bendahara',  'Bendahara',         'fa-wallet'],
            ['Ust. Nama Pendidikan', 'Kepala Pendidikan', 'fa-user-graduate'],
        ] as $i => [$nama, $jabatan, $ikon]) {
            StrukturOrganisasi::create([
                'kategori' => 'bagan', 'parent_id' => $mudir->id, 'nama' => $nama,
                'jabatan' => $jabatan, 'ikon' => $ikon, 'urutan' => $i + 1,
            ]);
        }

        foreach ([
            ['MDTA',        'Madrasah Diniyah Takmiliyah'],
            ['PPS Wustha',  'Pondok Pesantren Salafiyah Wustha'],
            ['MTs',         'Madrasah Tsanawiyah'],
            ['MA',          'Madrasah Aliyah'],
            ['PDF Wustha',  'Pendidikan Diniyah Formal Wustha'],
            ['PDF Ulya',    'Pendidikan Diniyah Formal Ulya'],
            ['Ma’had ‘Aly', 'Ma’had ‘Aly'],
        ] as $i => [$kode, $lengkap]) {
            StrukturOrganisasi::create([
                'kategori' => 'jenjang', 'nama' => 'Ust. Nama Ketua ' . $kode,
                'kode' => $kode, 'keterangan' => $lengkap, 'urutan' => $i + 1,
            ]);
        }

        foreach ([
            ['fa-book-open',     'Pendidikan',          'Kurikulum, jadwal, dan evaluasi pembelajaran.'],
            ['fa-shield-heart',  'Keamanan & Disiplin', 'Ketertiban dan pembinaan kedisiplinan santri.'],
            ['fa-language',      'Bahasa',              'Pengembangan bahasa Arab dan Inggris.'],
            ['fa-house-chimney', 'Sarana & Kebersihan', 'Perawatan fasilitas dan lingkungan pondok.'],
            ['fa-kit-medical',   'Kesehatan',           'Pelayanan kesehatan dan pertolongan pertama.'],
            ['fa-bullhorn',      'Humas & Media',       'Informasi, publikasi, dan hubungan masyarakat.'],
        ] as $i => [$ikon, $nama, $deskripsi]) {
            StrukturOrganisasi::create([
                'kategori' => 'bidang', 'nama' => $nama, 'keterangan' => $deskripsi,
                'ikon' => $ikon, 'urutan' => $i + 1,
            ]);
        }
    }
}
