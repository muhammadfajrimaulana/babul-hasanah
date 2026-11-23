<?php

namespace Database\Seeders;

use App\Models\Konten;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Data intasional & master (tidak ada perubahan)
        $this->call([
            RoleAndUserSeeder::class,
            ProgramSeeder::class,
            StrukturOrganisasiSeeder::class,
            MataPelajaranSeeder::class,
        ]);

        // 2. PERBAIKAN KONTEN (Ini yang mengatasi error Duplicate entry)
        // Mengganti Konten::insert() dengan loop cek existence agar aman berulang kali
        $daftarKonten = [
            ['key' => 'profil_hero_title', 'value' => 'Profil & Visi Misi Pondok Pesantren', 'type' => 'text'],
            ['key' => 'profil_hero_desc', 'value' => 'Mengenal lebih dekat sejarah, landasan hukum, struktur kepengurusan, serta cita-cita luhur Pondok Pesantren Babul Hasanah.', 'type' => 'textarea'],
            ['key' => 'sejarah_judul', 'value' => 'Menyalakan Lentera Ilmu di Tanah Padang Lawas', 'type' => 'text'],
            ['key' => 'sejarah_p1', 'value' => 'Pondok Pesantren Babul Hasanah didirikan dengan niat tulus...', 'type' => 'textarea'],
            ['key' => 'sejarah_p2', 'value' => 'Terletak di Jalan Lintas Pinarik Papaso Km. 14...', 'type' => 'textarea'],
            ['key' => 'sejarah_foto', 'value' => null, 'type' => 'image'],
            ['key' => 'visi', 'value' => 'Terwujudnya lembaga pendidikan Islam yang unggul...', 'type' => 'textarea'],
            ['key' => 'misi', 'value' => '1. Menyelenggarakan pendidikan Islam terpadu...\n2. Menanamkan kecintaan terhadap Al-Qur\'an...', 'type' => 'textarea'],
        ];

        foreach ($daftarKonten as $konten) {
            // CEK DULU: apakah key sudah ada di database?
            $sudahAda = Konten::where('key', $konten['key'])->exists();

            if (! $sudahAda) {
                // BELUM ADA → baru buat record
                Konten::create($konten);
            }
            // Jika sudah ada: lewati, tidak terjadi error duplicate key
        }
    }
}
