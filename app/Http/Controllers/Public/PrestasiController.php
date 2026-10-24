<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class PrestasiController extends Controller
{
    public function index()
    {
        // DATA CONTOH. Nanti ganti dengan data dari database, misalnya:
        // $prestasi = \App\Models\Prestasi::latest('tanggal')->get();
        // Pastikan tiap item punya field yang sama dengan yang dipakai di view.
        $prestasi = collect([
            ['judul' => "Musabaqah Tilawatil Qur'an (MTQ)", 'peringkat' => 1, 'label' => 'Juara I',
                'kategori' => 'Tahfidz', 'tingkat' => 'Kabupaten', 'wilayah' => 'Padang Lawas', 'tahun' => 2026,
                'nama' => 'Nama Santri', 'kelas' => 'MA', 'deskripsi' => 'Meraih juara pertama cabang tilawah putra pada MTQ tingkat Kabupaten Padang Lawas.'],
            ['judul' => 'Musabaqah Hifzhil Qur\'an 30 Juz', 'peringkat' => 1, 'label' => 'Juara I',
                'kategori' => 'Tahfidz', 'tingkat' => 'Provinsi', 'wilayah' => 'Sumatera Utara', 'tahun' => 2026,
                'nama' => 'Nama Santri', 'kelas' => 'MA', 'deskripsi' => 'Menyelesaikan hafalan 30 juz dengan predikat mumtaz dan meraih peringkat terbaik tingkat provinsi.'],
            ['judul' => "Musabaqah Qira'atil Kutub", 'peringkat' => 2, 'label' => 'Juara II',
                'kategori' => 'Kitab Kuning', 'tingkat' => 'Provinsi', 'wilayah' => 'Sumatera Utara', 'tahun' => 2026,
                'nama' => 'Nama Santri', 'kelas' => 'PDF Ulya', 'deskripsi' => 'Mampu membaca dan menjelaskan kitab turats dengan baik di hadapan dewan juri.'],
            ['judul' => 'Olimpiade Bahasa Arab', 'peringkat' => 1, 'label' => 'Medali Emas',
                'kategori' => 'Bahasa', 'tingkat' => 'Provinsi', 'wilayah' => 'Sumatera Utara', 'tahun' => 2025,
                'nama' => 'Nama Santri', 'kelas' => 'MTS', 'deskripsi' => 'Meraih medali emas pada olimpiade bahasa Arab tingkat provinsi.'],
            ['judul' => 'Lomba Pidato Tiga Bahasa', 'peringkat' => 3, 'label' => 'Juara III',
                'kategori' => 'Bahasa', 'tingkat' => 'Kabupaten', 'wilayah' => 'Padang Lawas', 'tahun' => 2025,
                'nama' => 'Nama Santri', 'kelas' => 'MA', 'deskripsi' => 'Tampil percaya diri berpidato dalam bahasa Arab, Inggris, dan Indonesia.'],
            ['judul' => 'Kompetisi Sains Madrasah (KSM)', 'peringkat' => 2, 'label' => 'Juara II',
                'kategori' => 'Sains', 'tingkat' => 'Kabupaten', 'wilayah' => 'Padang Lawas', 'tahun' => 2025,
                'nama' => 'Nama Santri', 'kelas' => 'MTS', 'deskripsi' => 'Lolos ke babak final dan meraih juara kedua bidang matematika.'],
            ['judul' => 'Lomba Kaligrafi', 'peringkat' => 1, 'label' => 'Juara I',
                'kategori' => 'Seni & Olahraga', 'tingkat' => 'Kabupaten', 'wilayah' => 'Padang Lawas', 'tahun' => 2024,
                'nama' => 'Nama Santri', 'kelas' => 'MA', 'deskripsi' => 'Karya kaligrafi khat naskhi terpilih sebagai karya terbaik oleh dewan juri.'],
            ['judul' => 'Kejuaraan Pencak Silat Pelajar', 'peringkat' => 3, 'label' => 'Juara III',
                'kategori' => 'Seni & Olahraga', 'tingkat' => 'Provinsi', 'wilayah' => 'Sumatera Utara', 'tahun' => 2024,
                'nama' => 'Nama Santri', 'kelas' => 'MTS', 'deskripsi' => 'Meraih medali perunggu kelas remaja putra pada kejuaraan tingkat provinsi.'],
            ['judul' => 'Festival Kreasi Santri Nasional', 'peringkat' => 0, 'label' => 'Finalis',
                'kategori' => 'Sains', 'tingkat' => 'Nasional', 'wilayah' => 'Indonesia', 'tahun' => 2024,
                'nama' => 'Nama Santri', 'kelas' => 'MA', 'deskripsi' => 'Masuk jajaran finalis dengan karya inovasi teknologi tepat guna.'],
        ]);

        return view('pages.prestasi', compact('prestasi'));
    }
}
