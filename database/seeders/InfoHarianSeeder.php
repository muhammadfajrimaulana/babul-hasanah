<?php

namespace Database\Seeders;

use App\Models\InfoHarian;
use Illuminate\Database\Seeder;

class InfoHarianSeeder extends Seeder
{
    public function run(): void
    {
        if (InfoHarian::exists()) {
            return;
        }

        $info = [
            ['Pengumuman', 'Pendaftaran santri baru gelombang 2 dibuka', 'Calon santri dapat mendaftar online hingga akhir bulan ini.', true, 0],
            ['Jadwal', "Kajian kitab ba'da Maghrib dipindah ke aula utama", 'Berlaku untuk seluruh santri putra hari ini.', true, 0],
            ['Agenda', 'Gotong royong kebersihan lingkungan pesantren', 'Santri berkumpul pukul 06.30 di lapangan, membawa perlengkapan masing-masing.', false, 1],
            ['Libur', 'Kegiatan Ahad pagi diliburkan', 'Kegiatan belajar kembali berjalan normal pada Senin pagi.', false, 2],
        ];

        foreach ($info as [$kategori, $judul, $isi, $penting, $hariLalu]) {
            InfoHarian::create([
                'jenis' => 'info', 'kategori' => $kategori, 'judul' => $judul, 'isi' => $isi,
                'penting' => $penting, 'aktif' => true, 'tanggal' => now()->subDays($hariLalu)->toDateString(),
            ]);
        }

        $jadwal = [
            ['04.30', "Subuh berjamaah & halaqah Al-Qur'an"],
            ['07.00', 'Pembelajaran madrasah'],
            ['12.15', 'Dzuhur berjamaah & makan siang'],
            ['15.30', 'Ashar & ekstrakurikuler'],
            ['18.15', 'Maghrib & kajian kitab'],
            ['20.00', "Muraja'ah & belajar mandiri"],
        ];

        foreach ($jadwal as $i => [$jam, $kegiatan]) {
            InfoHarian::create([
                'jenis' => 'jadwal', 'judul' => $kegiatan, 'jam' => $jam,
                'aktif' => true, 'urutan' => $i, 'tanggal' => now()->toDateString(),
            ]);
        }
    }
}
