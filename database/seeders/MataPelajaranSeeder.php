<?php

namespace Database\Seeders;

use App\Models\MataPelajaran;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Master mata pelajaran dasar per jenjang (data referensi kurikulum).
 *
 * Seeder ini idempoten: aman dijalankan berulang karena memakai kunci
 * (jenjang + kode). Jalankan hanya setelah migrasi akademik tersedia.
 */
class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            'mdta' => [
                ['kode' => 'QUR', 'nama' => "Baca Tulis Al-Qur'an", 'kelompok' => 'Diniyah', 'kkm' => 70],
                ['kode' => 'FQH', 'nama' => 'Fiqih Ibadah', 'kelompok' => 'Diniyah', 'kkm' => 70],
                ['kode' => 'AKH', 'nama' => 'Akhlak', 'kelompok' => 'Diniyah', 'kkm' => 70],
                ['kode' => 'ARB', 'nama' => 'Bahasa Arab Dasar', 'kelompok' => 'Bahasa', 'kkm' => 65],
                ['kode' => 'HAF', 'nama' => 'Hafalan Surat Pendek', 'kelompok' => 'Tahfidz', 'kkm' => 75],
            ],
            'mts' => [
                ['kode' => 'QUR', 'nama' => "Al-Qur'an Hadits", 'kelompok' => 'Diniyah', 'kkm' => 75],
                ['kode' => 'FQH', 'nama' => 'Fiqih', 'kelompok' => 'Diniyah', 'kkm' => 75],
                ['kode' => 'AKD', 'nama' => 'Akidah Akhlak', 'kelompok' => 'Diniyah', 'kkm' => 75],
                ['kode' => 'SKI', 'nama' => 'Sejarah Kebudayaan Islam', 'kelompok' => 'Diniyah', 'kkm' => 70],
                ['kode' => 'BIN', 'nama' => 'Bahasa Indonesia', 'kelompok' => 'Umum', 'kkm' => 70],
                ['kode' => 'MTK', 'nama' => 'Matematika', 'kelompok' => 'Umum', 'kkm' => 65],
                ['kode' => 'IPA', 'nama' => 'Ilmu Pengetahuan Alam', 'kelompok' => 'Umum', 'kkm' => 65],
                ['kode' => 'BIG', 'nama' => 'Bahasa Inggris', 'kelompok' => 'Umum', 'kkm' => 65],
                ['kode' => 'NHW', 'nama' => 'Nahwu', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'SRF', 'nama' => 'Sharaf', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'HAF', 'nama' => 'Tahfidz', 'kelompok' => 'Tahfidz', 'kkm' => 75],
            ],
            'ma' => [
                ['kode' => 'QUR', 'nama' => "Al-Qur'an Hadits", 'kelompok' => 'Diniyah', 'kkm' => 75],
                ['kode' => 'FQH', 'nama' => 'Fiqih', 'kelompok' => 'Diniyah', 'kkm' => 75],
                ['kode' => 'AKD', 'nama' => 'Akidah Akhlak', 'kelompok' => 'Diniyah', 'kkm' => 75],
                ['kode' => 'SKI', 'nama' => 'Sejarah Kebudayaan Islam', 'kelompok' => 'Diniyah', 'kkm' => 70],
                ['kode' => 'BIN', 'nama' => 'Bahasa Indonesia', 'kelompok' => 'Umum', 'kkm' => 75],
                ['kode' => 'MTK', 'nama' => 'Matematika', 'kelompok' => 'Umum', 'kkm' => 70],
                ['kode' => 'BIG', 'nama' => 'Bahasa Inggris', 'kelompok' => 'Umum', 'kkm' => 70],
                ['kode' => 'NHW', 'nama' => 'Nahwu', 'kelompok' => 'Kitab', 'kkm' => 75],
                ['kode' => 'BLG', 'nama' => 'Balaghah', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'UML', 'nama' => 'Ushul Fiqih', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'TFS', 'nama' => 'Tafsir', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'HDS', 'nama' => 'Hadits', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'HAF', 'nama' => 'Tahfidz', 'kelompok' => 'Tahfidz', 'kkm' => 75],
            ],
            'pps-wustha' => [
                ['kode' => 'FQH', 'nama' => 'Fiqih', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'NHW', 'nama' => 'Nahwu', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'SRF', 'nama' => 'Sharaf', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'BLG', 'nama' => 'Balaghah', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'TFS', 'nama' => 'Tafsir', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'HAF', 'nama' => 'Tahfidz', 'kelompok' => 'Tahfidz', 'kkm' => 75],
            ],
            'pdf-wustha' => [
                ['kode' => 'FQH', 'nama' => 'Fiqih', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'NHW', 'nama' => 'Nahwu', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'SRF', 'nama' => 'Sharaf', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'HDS', 'nama' => 'Hadits', 'kelompok' => 'Kitab', 'kkm' => 70],
                ['kode' => 'TFS', 'nama' => 'Tafsir', 'kelompok' => 'Kitab', 'kkm' => 70],
            ],
            'pdf-ulya' => [
                ['kode' => 'FQH', 'nama' => 'Fiqih', 'kelompok' => 'Kitab', 'kkm' => 75],
                ['kode' => 'UML', 'nama' => 'Ushul Fiqih', 'kelompok' => 'Kitab', 'kkm' => 75],
                ['kode' => 'TFS', 'nama' => 'Tafsir', 'kelompok' => 'Kitab', 'kkm' => 75],
                ['kode' => 'HDS', 'nama' => 'Hadits', 'kelompok' => 'Kitab', 'kkm' => 75],
                ['kode' => 'BLG', 'nama' => 'Balaghah', 'kelompok' => 'Kitab', 'kkm' => 75],
                ['kode' => 'BHS', 'nama' => 'Bahtsul Masail', 'kelompok' => 'Kitab', 'kkm' => 75],
            ],
            'mahad-aly' => [
                ['kode' => 'UML', 'nama' => 'Ushul Fiqih', 'kelompok' => 'Kitab', 'kkm' => 75],
                ['kode' => 'QWA', 'nama' => "Qawa'id Fiqhiyah", 'kelompok' => 'Kitab', 'kkm' => 75],
                ['kode' => 'MQS', 'nama' => 'Maqashid Syariah', 'kelompok' => 'Kitab', 'kkm' => 75],
                ['kode' => 'TFS', 'nama' => 'Tafsir', 'kelompok' => 'Kitab', 'kkm' => 75],
                ['kode' => 'HDS', 'nama' => 'Hadits', 'kelompok' => 'Kitab', 'kkm' => 75],
            ],
        ];

        foreach ($daftar as $jenjang => $mapel) {
            foreach ($mapel as $item) {
                MataPelajaran::updateOrCreate(
                    ['jenjang' => $jenjang, 'kode' => $item['kode']],
                    [
                        'nama' => $item['nama'],
                        'kelompok' => $item['kelompok'],
                        'kkm' => $item['kkm'],
                        'aktif' => true,
                    ]
                );

                if (Schema::hasTable('subjects')) {
                    $subject = Subject::withTrashed()->firstOrNew([
                        'jenjang' => $jenjang,
                        'kode' => $item['kode'],
                    ]);

                    $subject->fill([
                        'nama' => $item['nama'],
                        'kelompok' => $item['kelompok'],
                        'kkm' => $item['kkm'],
                    ]);

                    if ($subject->trashed()) {
                        $subject->restore();
                    }

                    $subject->deleted_at = null;
                    $subject->save();
                }
            }
        }
    }
}
