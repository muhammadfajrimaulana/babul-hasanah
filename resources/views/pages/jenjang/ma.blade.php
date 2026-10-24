@extends('layouts.app')

@section('title', 'Madrasah Aliyah (MA) - Pondok Pesantren Babul Hasanah')

@section('content')

@php
    /*
     * CATATAN: isi di bawah ini adalah CONTOH awal. Sesuaikan dengan data resmi
     * (lama belajar, kitab, kegiatan, nama ketua, dst.) sebelum dipublikasikan.
     * Foto banner  : public/images/program/ma.jpg
     * Foto ketua   : public/images/pengurus/ketua-ma.jpg
     */
    $p = [
        'kode'    => 'MA',
        'nama'    => 'Madrasah Aliyah',
        'ikon'    => 'fa-graduation-cap',
        'banner'  => 'ma.jpg',
        'tagline' => 'Pendidikan menengah atas yang menyiapkan santri berilmu, berakhlak, dan siap melanjutkan studi.',
        'deskripsi' => [
                'MA Babul Hasanah membekali santri dengan ilmu umum dan agama untuk menghadapi jenjang berikutnya, baik perguruan tinggi, Ma’had ‘Aly, maupun pengabdian di masyarakat.',
                'Santri dilatih memimpin, berorganisasi, dan berbicara di depan umum, sambil tetap mengaji kitab bersama para ustadz.',
            ],
        'fakta' => [
            ['fa-user-graduate', 'Sasaran', 'Lulusan MTs/SMP'],
            ['fa-hourglass-half', 'Lama belajar', '3 tahun (kelas X–XII)'],
            ['fa-book', 'Kurikulum', 'Madrasah dan pesantren'],
            ['fa-certificate', 'Capaian', 'Ijazah MA'],
        ],
        'kurikulum_intro' => 'Pelajaran madrasah, kajian kitab lanjutan, dan bimbingan studi lanjut.',
        'kurikulum' => [
            ['ikon' => 'fa-mosque', 'judul' => 'Mata pelajaran agama', 'items' => ['Al-Qur’an Hadis', 'Akidah Akhlak', 'Fikih', 'SKI', 'Bahasa Arab']],
            ['ikon' => 'fa-flask', 'judul' => 'Mata pelajaran umum', 'items' => ['Matematika', 'Sains', 'Ilmu sosial', 'Bahasa Indonesia', 'Bahasa Inggris']],
            ['ikon' => 'fa-book-open', 'judul' => 'Muatan pesantren', 'items' => ['Fathul Mu’in', 'Tafsir Jalalain', 'Bahtsul masail', 'Tahfidz']],
            ['ikon' => 'fa-compass', 'judul' => 'Persiapan lanjut', 'items' => ['Bimbingan studi lanjut', 'Kepemimpinan dan organisasi', 'Keterampilan digital']],
        ],
        'capaian' => [
                'Menguasai kompetensi akademik jenjang menengah atas.',
                'Mampu membaca dan mengkaji kitab kuning secara mandiri.',
                'Percaya diri berbicara dan memimpin kegiatan.',
                'Siap melanjutkan ke perguruan tinggi atau Ma’had ‘Aly.',
            ],
        'kegiatan' => [
                'Organisasi santri dan kepanitiaan acara',
                'Muhadharah dan debat',
                'Bimbingan masuk perguruan tinggi',
                'Bahtsul masail antarkelas',
            ],
        'ketua' => [
            'nama'     => 'Ust. Nama Ketua MA',
            'jabatan'  => 'Ketua MA',
            'foto'     => 'ketua-ma.jpg',
            'sambutan' => 'Kami menyiapkan santri menjadi pribadi yang matang: kuat ilmunya, jernih pikirannya, dan siap berkhidmat di mana pun ditempatkan.',
        ],
    ];
@endphp

@include('pages.jenjang._detail', ['p' => $p])

@endsection
