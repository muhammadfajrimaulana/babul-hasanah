@extends('layouts.app')

@section('title', 'PPS Wustha - Pondok Pesantren Babul Hasanah')

@section('content')

@php
    /*
     * CATATAN: isi di bawah ini adalah CONTOH awal. Sesuaikan dengan data resmi
     * (lama belajar, kitab, kegiatan, nama ketua, dst.) sebelum dipublikasikan.
     * Foto banner  : public/images/program/pps-wustha.jpg
     * Foto ketua   : public/images/pengurus/ketua-pps-wustha.jpg
     */
    $p = [
        'kode'    => 'PPS Wustha',
        'nama'    => 'Pendidikan Pesantren Salafiyah Wustha',
        'ikon'    => 'fa-award',
        'banner'  => 'pps-wustha.jpg',
        'tagline' => 'Pendidikan salafiyah tingkat menengah pertama yang memadukan kajian kitab dan pembentukan akhlak.',
        'deskripsi' => [
            'PPS Wustha adalah jenjang pendidikan salafiyah tingkat menengah pertama bagi santri yang ingin memperdalam ilmu agama melalui kajian kitab kuning secara terarah.',
            'Santri dibimbing bertahap mulai dari dasar nahwu dan sharf, fiqih, hingga akidah dan akhlak, dengan bimbingan langsung dari para ustadz.',
        ],
        'fakta' => [
            ['fa-user-graduate', 'Sasaran', 'Lulusan MDTA / SD sederajat'],
            ['fa-hourglass-half', 'Lama belajar', '3 tahun'],
            ['fa-clock', 'Waktu belajar', 'Pagi dan malam'],
            ['fa-certificate', 'Capaian', 'Ijazah PPS Wustha'],
        ],
        'kurikulum_intro' => 'Kajian kitab dasar dan penguatan ilmu alat yang dipelajari bertahap setiap tahun.',
        'kurikulum' => [
            ['ikon' => 'fa-book-quran', 'judul' => 'Al-Qur’an', 'items' => ['Tahsin dan tajwid', 'Hafalan juz ‘amma', 'Tafsir dasar']],
            ['ikon' => 'fa-hands-praying', 'judul' => 'Fiqih', 'items' => ['Fathul Qarib', 'Safinatun Najah', 'Praktik ibadah']],
            ['ikon' => 'fa-language', 'judul' => 'Ilmu alat', 'items' => ['Nahwu dasar', 'Sharf', 'Mufradat Arab']],
            ['ikon' => 'fa-heart', 'judul' => 'Akidah dan akhlak', 'items' => ['Aqidatul Awam', 'Ta’lim Muta’allim', 'Adab santri']],
        ],
        'capaian' => [
            'Mampu membaca kitab kuning tingkat dasar.',
            'Memahami dasar-dasar fiqih ibadah dan akidah.',
            'Hafal surat-surat pilihan dan doa harian.',
            'Beradab kepada guru, orang tua, dan sesama santri.',
        ],
        'kegiatan' => [
            'Kajian kitab harian',
            'Muhadharah dan latihan pidato',
            'Musyawarah dan bahtsul masail dasar',
            'Peringatan hari besar Islam',
        ],
        'ketua' => [
            'nama'     => 'Ust. Nama Ketua PPS Wustha',
            'jabatan'  => 'Ketua PPS Wustha',
            'foto'     => 'ketua-pps-wustha.jpg',
            'sambutan' => 'Kami mendampingi santri memahami kitab dan mengamalkannya dalam kehidupan sehari-hari dengan sabar dan penuh keteladanan.',
        ],
    ];
@endphp

@include('pages.jenjang._detail', ['p' => $p])

@endsection