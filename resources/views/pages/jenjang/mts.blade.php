@extends('layouts.app')

@section('title', 'Madrasah Tsanawiyah (MTs) - Pondok Pesantren Babul Hasanah')

@section('content')

@php
    /*
     * CATATAN: isi di bawah ini adalah CONTOH awal. Sesuaikan dengan data resmi
     * (lama belajar, kitab, kegiatan, nama ketua, dst.) sebelum dipublikasikan.
     * Foto banner  : public/images/program/mts.jpg
     * Foto ketua   : public/images/pengurus/ketua-mts.jpg
     */
    $p = [
        'kode'    => 'MTs',
        'nama'    => 'Madrasah Tsanawiyah',
        'ikon'    => 'fa-school',
        'banner'  => 'mts.jpg',
        'tagline' => 'Pendidikan menengah pertama yang memadukan kurikulum madrasah dengan kehidupan pesantren.',
        'deskripsi' => [
                'MTs Babul Hasanah menyiapkan santri menguasai pelajaran umum dan agama sekaligus. Santri belajar di kelas pada pagi hari, lalu melanjutkan pengajian dan pembinaan di lingkungan pondok.',
                'Perpaduan ini membuat santri terbiasa disiplin, mandiri, dan menjaga ibadah di tengah padatnya kegiatan belajar.',
            ],
        'fakta' => [
            ['fa-user-graduate', 'Sasaran', 'Lulusan SD/MI'],
            ['fa-hourglass-half', 'Lama belajar', '3 tahun (kelas VII–IX)'],
            ['fa-book', 'Kurikulum', 'Madrasah dan pesantren'],
            ['fa-certificate', 'Capaian', 'Ijazah MTs'],
        ],
        'kurikulum_intro' => 'Mata pelajaran madrasah berjalan bersama muatan khas pesantren.',
        'kurikulum' => [
            ['ikon' => 'fa-mosque', 'judul' => 'Mata pelajaran agama', 'items' => ['Al-Qur’an Hadis', 'Akidah Akhlak', 'Fikih', 'SKI', 'Bahasa Arab']],
            ['ikon' => 'fa-flask', 'judul' => 'Mata pelajaran umum', 'items' => ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'PPKn']],
            ['ikon' => 'fa-book-open', 'judul' => 'Muatan pesantren', 'items' => ['Kajian kitab kuning', 'Tahsin dan tahfidz', 'Muhadharah']],
            ['ikon' => 'fa-futbol', 'judul' => 'Pengembangan diri', 'items' => ['Pramuka', 'Olahraga', 'Hadrah dan rebana']],
        ],
        'capaian' => [
                'Menguasai kompetensi akademik jenjang menengah pertama.',
                'Membaca Al-Qur’an dengan baik dan memiliki hafalan.',
                'Terbiasa disiplin, mandiri, dan berakhlak.',
                'Siap melanjutkan ke MA atau jenjang diniyah.',
            ],
        'kegiatan' => [
                'Pengajian kitab sore dan malam',
                'Muhadharah mingguan',
                'Pramuka dan olahraga',
                'Hadrah dan seni Islami',
            ],
        'ketua' => [
            'nama'     => 'Ust. Nama Ketua MTs',
            'jabatan'  => 'Ketua MTs',
            'foto'     => 'ketua-mts.jpg',
            'sambutan' => 'Kami mendidik santri agar cerdas di kelas dan kuat di akhlak. Keduanya harus berjalan bersama.',
        ],
    ];
@endphp

@include('pages.jenjang._detail', ['p' => $p])

@endsection
