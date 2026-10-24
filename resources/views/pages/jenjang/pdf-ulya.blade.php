@extends('layouts.app')

@section('title', 'Pendidikan Diniyah Formal Ulya (PDF Ulya) - Pondok Pesantren Babul Hasanah')

@section('content')

@php
    /*
     * CATATAN: isi di bawah ini adalah CONTOH awal. Sesuaikan dengan data resmi
     * (lama belajar, kitab, kegiatan, nama ketua, dst.) sebelum dipublikasikan.
     * Foto banner  : public/images/program/pdf-ulya.jpg
     * Foto ketua   : public/images/pengurus/ketua-pdf-ulya.jpg
     */
    $p = [
        'kode'    => 'PDF Ulya',
        'nama'    => 'Pendidikan Diniyah Formal Ulya',
        'ikon'    => 'fa-feather-pointed',
        'banner'  => 'pdf-ulya.jpg',
        'tagline' => 'Kajian kitab tingkat lanjut yang melatih santri berpikir dan merujuk sumber secara mandiri.',
        'deskripsi' => [
                'PDF Ulya adalah puncak jenjang diniyah formal. Santri mengkaji kitab-kitab besar dalam fiqih, ushul fiqih, tafsir, dan hadits, serta dilatih berdiskusi dan menyusun jawaban hukum.',
                'Santri senior juga dibiasakan berkhidmat, misalnya mendampingi adik-adik di jenjang dasar, sebagai latihan mengajar dan menjadi teladan.',
            ],
        'fakta' => [
            ['fa-user-graduate', 'Sasaran', 'Lulusan PDF Wustha / MTs'],
            ['fa-hourglass-half', 'Lama belajar', '3 tahun'],
            ['fa-list-check', 'Sistem', 'Diniyah formal berjenjang'],
            ['fa-certificate', 'Capaian', 'Ijazah PDF Ulya'],
        ],
        'kurikulum_intro' => 'Kitab lanjutan dengan penekanan pada penalaran dan pengambilan hukum.',
        'kurikulum' => [
            ['ikon' => 'fa-spell-check', 'judul' => 'Bahasa Arab lanjutan', 'items' => ['Alfiyah Ibnu Malik', 'Balaghah', 'Kitabah']],
            ['ikon' => 'fa-scale-balanced', 'judul' => 'Fiqih dan ushul', 'items' => ['Fathul Mu’in', 'Fathul Wahhab', 'Lubbul Ushul']],
            ['ikon' => 'fa-scroll', 'judul' => 'Tafsir dan hadits', 'items' => ['Tafsir Jalalain', 'Riyadhus Shalihin', 'Bulughul Maram']],
            ['ikon' => 'fa-heart-pulse', 'judul' => 'Tasawuf dan akhlak', 'items' => ['Ihya’ Ulumuddin (pilihan)', 'Bidayatul Hidayah']],
        ],
        'capaian' => [
                'Mengkaji kitab lanjutan secara mandiri.',
                'Mampu menelusuri dalil dan pendapat ulama dalam kitab.',
                'Terlatih menjawab persoalan hukum lewat bahtsul masail.',
                'Siap melanjutkan ke Ma’had ‘Aly atau berkhidmat di masyarakat.',
            ],
        'kegiatan' => [
                'Bahtsul masail terjadwal',
                'Khidmah mengajar di jenjang dasar',
                'Penulisan makalah ringan',
                'Kajian bersama pengasuh',
            ],
        'ketua' => [
            'nama'     => 'Ust. Nama Ketua PDF Ulya',
            'jabatan'  => 'Ketua PDF Ulya',
            'foto'     => 'ketua-pdf-ulya.jpg',
            'sambutan' => 'Di Ulya, santri belajar bahwa ilmu adalah amanah. Kami melatih mereka berpikir jernih, merujuk kitab, dan rendah hati.',
        ],
    ];
@endphp

@include('pages.jenjang._detail', ['p' => $p])

@endsection
