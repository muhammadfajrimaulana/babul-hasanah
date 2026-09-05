<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Konten;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndUserSeeder::class,
        ]);

        \App\Models\Konten::insert([
            ['key' => 'profil_hero_title', 'value' => 'Profil & Visi Misi Pondok Pesantren', 'type' => 'text'],
            ['key' => 'profil_hero_desc', 'value' => 'Mengenal lebih dekat sejarah, landasan hukum, struktur kepengurusan, serta cita-cita luhur Pondok Pesantren Babul Hasanah.', 'type' => 'textarea'],
            ['key' => 'sejarah_judul', 'value' => 'Menyalakan Lentera Ilmu di Tanah Padang Lawas', 'type' => 'text'],
            ['key' => 'sejarah_p1', 'value' => 'Pondok Pesantren Babul Hasanah didirikan dengan niat tulus...', 'type' => 'textarea'],
            ['key' => 'sejarah_p2', 'value' => 'Terletak di Jalan Lintas Pinarik Papaso Km. 14...', 'type' => 'textarea'],
            ['key' => 'sejarah_foto', 'value' => null, 'type' => 'image'],
            ['key' => 'visi', 'value' => 'Terwujudnya lembaga pendidikan Islam yang unggul...', 'type' => 'textarea'],
            ['key' => 'misi', 'value' => '1. Menyelenggarakan pendidikan Islam terpadu...\n2. Menanamkan kecintaan terhadap Al-Qur\'an...', 'type' => 'textarea'],
        ]);
    }
}