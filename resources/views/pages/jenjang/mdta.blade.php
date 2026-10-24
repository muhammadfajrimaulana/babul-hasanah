@extends('layouts.app')

@section('title', 'Madrasah Diniyah Takmiliyah (MDTA) - Pondok Pesantren Babul Hasanah')

@section('content')

@php
    /*
     * CATATAN: isi di bawah ini adalah CONTOH awal. Sesuaikan dengan data resmi
     * (lama belajar, kitab, kegiatan, nama ketua, dst.) sebelum dipublikasikan.
     * Foto banner  : public/images/program/mdta.jpg
     * Foto ketua   : public/images/pengurus/ketua-mdta.jpg
     */
    $p = [
        'kode'    => 'MDTA',
        'nama'    => 'Madrasah Diniyah Takmiliyah',
        'ikon'    => 'fa-book-quran',
        'banner'  => 'mdta.jpg',
        'tagline' => 'Pendidikan diniyah dasar yang menanamkan bacaan Al-Qur’an, ibadah, dan akhlak sejak dini.',
        'deskripsi' => [
                'MDTA adalah pintu pertama santri mengenal ilmu agama secara terarah. Di sini santri belajar membaca Al-Qur’an dengan benar, memahami tata cara ibadah, dan membiasakan adab dalam kehidupan sehari-hari.',
                'Pembelajaran disampaikan dengan cara yang ramah bagi santri tingkat dasar: bertahap, banyak praktik, dan diulang secara rutin sampai menjadi kebiasaan.',
            ],
        'fakta' => [
            ['fa-user-graduate', 'Sasaran', 'Santri tingkat dasar'],
            ['fa-hourglass-half', 'Lama belajar', '4 tahun'],
            ['fa-clock', 'Waktu belajar', 'Sore hingga malam'],
            ['fa-certificate', 'Capaian', 'Syahadah MDTA'],
        ],
        'kurikulum_intro' => 'Fondasi ibadah, bacaan Al-Qur’an, dan akhlak yang dipelajari bertahap dari kelas awal.',
        'kurikulum' => [
            ['ikon' => 'fa-book-quran', 'judul' => 'Al-Qur’an', 'items' => ['Iqra’ dan tilawah', 'Tajwid dasar', 'Tahsin', 'Hafalan juz ‘amma', 'Doa harian']],
            ['ikon' => 'fa-hands-praying', 'judul' => 'Ibadah dan fiqih dasar', 'items' => ['Safinatun Najah', 'Thaharah', 'Tata cara shalat', 'Puasa dan zakat']],
            ['ikon' => 'fa-heart', 'judul' => 'Akidah dan akhlak', 'items' => ['Aqidatul Awam', 'Akhlaq lil Banin', 'Adab sehari-hari']],
            ['ikon' => 'fa-language', 'judul' => 'Bahasa Arab dan sejarah', 'items' => ['Mufradat dasar', 'Tulisan Arab', 'Sirah Nabawiyah']],
        ],
        'capaian' => [
                'Lancar membaca Al-Qur’an dengan tajwid dasar.',
                'Hafal surat-surat pendek dan doa sehari-hari.',
                'Menjalankan ibadah wajib dengan tata cara yang benar.',
                'Beradab kepada orang tua, guru, dan teman.',
            ],
        'kegiatan' => [
                'Setoran bacaan dan hafalan harian',
                'Praktik wudhu dan shalat',
                'Lomba tilawah dan hafalan',
                'Peringatan hari besar Islam',
            ],
        'ketua' => [
            'nama'     => 'Ust. Nama Ketua MDTA',
            'jabatan'  => 'Ketua MDTA',
            'foto'     => 'ketua-mdta.jpg',
            'sambutan' => 'Kami mendampingi santri melangkah dari huruf hijaiyah sampai terbiasa beribadah dan berakhlak baik. Setiap kemajuan kecil kami rayakan bersama.',
        ],
    ];
@endphp

@include('pages.jenjang._detail', ['p' => $p])

@endsection
