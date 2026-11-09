<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Album;
use App\Models\AlbumMedia;
use App\Models\Alumni;
use App\Models\Berita;
use App\Models\CatatanKalender;
use App\Models\InfoHarian;
use App\Models\Konten;
use App\Models\PendaftaranOnline;
use App\Models\PrestasiSantri;
use App\Models\Santri;
use App\Models\StrukturOrganisasi;
use App\Models\User;
use App\Support\Jenjang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Data dummy untuk pengujian semua fitur admin.
 *
 * Cara pakai (jalankan sendiri, bukan oleh agen):
 *   php artisan db:seed --class=DummyDataSeeder
 *
 * Aman dijalankan berulang:
 * - Memakai firstOrCreate / updateOrCreate, jadi tidak menimpa data
 *   yang sudah ada (kecuali akun dummy: password direset ke babul2026).
 * - Hanya mengisi kolom yang benar-benar ada di database.
 */
class DummyDataSeeder extends Seeder
{
    /** @var array<int, string> */
    private array $namaSantri = [
        'Ahmad Fauzan Nasution',
        'Muhammad Rizky Harahap',
        'Abdul Aziz Siregar',
        'Hafizh Al-Farizi',
        'Zulkarnain Lubis',
        'Fatimah Az-Zahra',
        'Aisyah Putri Siregar',
        'Nabila Husna',
        'Salsabila Rahmadani',
        'Zahra Aulia',
        'Muhammad Ilham',
        'Rahmat Hidayat',
        'Fikri Haikal',
        'Iqbal Maulana',
        'Arif Budiman',
        'Nurhaliza',
        'Siti Khadijah',
        'Putri Ramadhani',
        'Dewi Anggraini',
        'Raihan Alfarizi',
    ];

    /** @var array<int, string> */
    private array $namaWali = [
        'Sofyan Nasution',
        'Ridwan Harahap',
        'Hasan Siregar',
        'Fauzi Lubis',
        'Mansur Dalimunthe',
        'Syamsul Bahri',
        'Abdul Rahman',
        'Iskandar Siregar',
        'Zainal Abidin',
        'Muhammad Yusuf',
        'Anwar Sadat',
        'Bahrul Ulum',
        'Dedi Kurniawan',
        'Eko Prasetyo',
        'Fajar Ramadhan',
        'Gunawan Saputra',
        'Hendri Lubis',
        'Irfan Maulana',
        'Joko Susilo',
        'Khoirul Anam',
    ];

    /** @var array<int, string> */
    private array $namaPendaftar = [
        'Bagas Pratama',
        'Cahaya Ramadhan',
        'Dinda Ayu Lestari',
        'Farhan Maulana',
        'Gita Nurhaliza',
        'Hendra Saputra',
        'Intan Permata',
        'Jihan Aulia',
        'Kurniawan Adi',
        'Lestari Wulandari',
        'Miftahul Huda',
        'Naufal Hakim',
        'Oktavia Rahma',
        'Pandu Wicaksono',
        'Qonita Zahira',
        'Rizal Firmansyah',
        'Siti Aisyah',
        'Taufik Hidayat',
        'Ummi Kalsum',
        'Wahyu Saputra',
    ];

    /** @var array<int, string> */
    private array $namaAlumni = [
        'Ahmad Syafii',
        'Baihaqi Rambe',
        'Cut Nyak Dien',
        'Dahlan Siregar',
        'Erna Wati',
        'Fahmi Idris',
        'Ghozali Lubis',
        'Halimah Saadiyah',
        'Ibrahim Nasution',
        'Jamaluddin',
        'Kartini Harahap',
        'Lukman Hakim',
        'Maimunah',
        'Nurdin Siregar',
        'Omar Hidayat',
        'Purnama Sari',
        'Qomaruddin',
        'Rahmawati',
        'Sulaiman Lubis',
        'Tuti Alawiyah',
    ];

    /** @var array<int, string> */
    private array $judulBerita = [
        'Penerimaan Santri Baru 2026 Resmi Dibuka',
        "Khataman Al-Qur'an Santri MDTA Berjalan Khidmat",
        'Santri MTs Raih Juara 1 Olimpiade Matematika',
        'Kajian Kitab Kuning Kembali Digelar di Aula Utama',
        'Peringatan Maulid Nabi di Babul Hasanah',
        'Pelatihan Kaligrafi untuk Santri PDF Wustha',
        'Gotong Royong Bersihkan Lingkungan Pesantren',
        'Wisuda Tahfidz Angkatan ke-12',
        'Kunjungan Studi Banding dari Pesantren Mitra',
        "Pembukaan Program Ma'had 'Aly Tahun Akademik Baru",
        'Santri MA Juara Pidato Bahasa Arab',
        'Pemeriksaan Kesehatan Gratis untuk Santri',
        'Festival Seni Islami Antar Jenjang',
        'Peningkatan Fasilitas Asrama Putri',
        'Pelatihan Kepemimpinan Organisasi Santri',
        'Peringatan Hari Santri Nasional',
        'Lomba Futsal Antar Pondok Pesantren',
        'Pengajian Rutin Ahad Pagi Bersama Masyarakat',
        'Prestasi Santri di MTQ Kabupaten Padang Lawas',
        'Pembagian Rapor dan Pertemuan Wali Santri',
    ];

    public function run(): void
    {
        $users = $this->seedUsers();
        $pendaftaran = $this->seedPendaftaran();
        $this->seedSantri($users, $pendaftaran);
        $this->seedAlumni();
        $this->seedKonten();
        $this->seedBerita();
        $this->seedInfoHarian();
        $this->seedStruktur();
        $this->seedGaleri();
        $this->seedPrestasi();
        $this->seedAbsensi();
        $this->seedCatatanKalender($users);

        $this->command?->info('Data dummy selesai: 20 baris per fitur (users, pendaftaran, santri, alumni, konten, berita, info harian, struktur, galeri, prestasi, absensi, catatan kalender).');
    }

    /**
     * Hanya ambil kolom yang benar-benar ada di tabel.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function hanyaKolom(string $tabel, array $data): array
    {
        if (! Schema::hasTable($tabel)) {
            return [];
        }

        $kolom = Schema::getColumnListing($tabel);

        return array_intersect_key($data, array_flip($kolom));
    }

    /**
     * @return array<int, User>
     */
    private function seedUsers(): array
    {
        if (! Schema::hasTable('users')) {
            return [];
        }

        $hasil = [];
        $jenjang = Jenjang::semua();

        // 7 admin jenjang
        for ($i = 0; $i < 7; $i++) {
            $j = $jenjang[$i];
            $email = 'dummy.admin.'.$j['slug'].'@babulhasanah.test';

            $data = $this->hanyaKolom('users', [
                'name' => 'Admin '.$j['kode'].' (Dummy)',
                'email' => $email,
                'password' => Hash::make('babul2026'),
                'role' => 'admin',
                'jenjang' => $j['nama'],
                'phone' => '08123456'.str_pad((string) (1000 + $i), 4, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),
            ]);

            $hasil[] = User::updateOrCreate(['email' => $email], $data);
        }

        // 3 pembina
        for ($i = 1; $i <= 3; $i++) {
            $email = 'dummy.pembina'.$i.'@babulhasanah.test';

            $data = $this->hanyaKolom('users', [
                'name' => 'Pembina Asrama '.$i.' (Dummy)',
                'email' => $email,
                'password' => Hash::make('babul2026'),
                'role' => 'pembina',
                'phone' => '08129876'.str_pad((string) (2000 + $i), 4, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),
            ]);

            $hasil[] = User::updateOrCreate(['email' => $email], $data);
        }

        // 10 wali santri
        for ($i = 1; $i <= 10; $i++) {
            $email = 'dummy.wali'.str_pad((string) $i, 2, '0', STR_PAD_LEFT).'@babulhasanah.test';

            $data = $this->hanyaKolom('users', [
                'name' => $this->namaWali[$i - 1],
                'email' => $email,
                'password' => Hash::make('babul2026'),
                'role' => 'wali_santri',
                'phone' => '08137777'.str_pad((string) (3000 + $i), 4, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),
            ]);

            $hasil[] = User::updateOrCreate(['email' => $email], $data);
        }

        return $hasil;
    }

    /**
     * @return array<int, PendaftaranOnline>
     */
    private function seedPendaftaran(): array
    {
        if (! Schema::hasTable('pendaftaran_online')) {
            return [];
        }

        $hasil = [];
        $jenjang = Jenjang::semua();
        $pekerjaan = ['Petani', 'Pedagang', 'Guru', 'Buruh', 'Wiraswasta', 'PNS', 'Nelayan'];
        $status = array_merge(
            array_fill(0, 10, 'diterima'),
            array_fill(0, 6, 'pending'),
            array_fill(0, 4, 'ditolak')
        );

        foreach ($this->namaPendaftar as $i => $nama) {
            $no = $i + 1;
            $j = $jenjang[$i % count($jenjang)];
            $email = 'dummy.ppdb'.str_pad((string) $no, 2, '0', STR_PAD_LEFT).'@example.test';
            $nik = '12700101'.str_pad((string) $no, 8, '0', STR_PAD_LEFT);
            $nikOrtu = '12700202'.str_pad((string) $no, 8, '0', STR_PAD_LEFT);

            $data = $this->hanyaKolom('pendaftaran_online', [
                'no_pendaftaran' => 'PPDB-2026-'.str_pad((string) $no, 4, '0', STR_PAD_LEFT),
                'nama_lengkap' => $nama,
                'nama_panggilan' => explode(' ', $nama)[0],
                'nik_santri' => $nik,
                'nisn' => '00'.str_pad((string) (12345678 + $no), 8, '0', STR_PAD_LEFT),
                // Kolom jenis_kelamin sengaja tidak diisi: di sebagian database
                // kolom ini masih ENUM lama, sedangkan form PPDB tidak memakainya.
                'tempat_tanggal_lahir' => 'Padang Lawas, '.(1 + ($i % 28)).' '.
                    ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$i % 12].
                    ' '.(2010 + ($i % 4)),
                'jenjang_tujuan' => $j['nama'],
                'asal_sekolah' => ['SD Negeri 01 Padang Lawas', 'MI Al-Hidayah', 'SD Negeri 05 Pinarik', 'MTs Babul Hasanah'][$i % 4],
                'alamat_sekolah' => 'Jl. Pendidikan No. '.(1 + $i).', Padang Lawas',
                'tahun_lulus' => (string) (2025 + ($i % 2)),
                'no_hp_aktif' => '0812'.str_pad((string) (34000000 + $no), 8, '0', STR_PAD_LEFT),
                'alamat_domisili' => 'Jl. Lintas Pinarik Papaso Km. '.(10 + $i).', Padang Lawas',
                'nama_ortu_wali' => $this->namaWali[$i],
                'nik_ortu' => $nikOrtu,
                'pekerjaan_ortu' => $pekerjaan[$i % count($pekerjaan)],
                'alamat_jalan' => 'Jl. Lintas Pinarik Papaso Km. '.(10 + $i),
                'alamat_rt_rw' => 'RT 0'.(1 + ($i % 5)).' / RW 0'.(1 + ($i % 3)),
                'alamat_desa_kelurahan' => 'Desa Pinarik '.(1 + ($i % 4)),
                'alamat_kecamatan' => 'Kecamatan Padang Lawas',
                'alamat_kabupaten_kota' => 'Kabupaten Padang Lawas',
                'alamat_provinsi' => 'Sumatera Utara',
                'nama_ayah' => $this->namaWali[$i],
                'nama_ibu' => 'Ibu '.explode(' ', $this->namaSantri[$i])[0],
                'email' => $email,
                'no_hp_ortu' => '0813'.str_pad((string) (55000000 + $no), 8, '0', STR_PAD_LEFT),
                'status_pendaftaran' => $status[$i],
                'created_at' => now()->subDays(30 - $i),
                'updated_at' => now()->subDays(30 - $i),
            ]);

            if ($data === []) {
                continue;
            }

            $hasil[] = PendaftaranOnline::firstOrCreate(
                ['no_pendaftaran' => 'PPDB-2026-'.str_pad((string) $no, 4, '0', STR_PAD_LEFT)],
                $data
            );
        }

        return $hasil;
    }

    /**
     * @param  array<int, User>  $users
     * @param  array<int, PendaftaranOnline>  $pendaftaran
     */
    private function seedSantri(array $users, array $pendaftaran): void
    {
        if (! Schema::hasTable('santri')) {
            return;
        }

        $jenjang = Jenjang::semua();
        $wali = array_values(array_filter($users, fn (User $u) => $u->role === 'wali_santri'));
        $diterima = array_values(array_filter($pendaftaran, fn (PendaftaranOnline $p) => $p->status_pendaftaran === 'diterima'));

        $kelas = [
            'mdta' => ['Kelas 1', 'Kelas 2', 'Kelas 3'],
            'pps-wustha' => ['Wustha 1', 'Wustha 2', 'Wustha 3'],
            'mts' => ['VII A', 'VIII B', 'IX A'],
            'ma' => ['X IPA', 'XI IPS', 'XII IPA'],
            'pdf-wustha' => ['DF Wustha 1', 'DF Wustha 2', 'DF Wustha 3'],
            'pdf-ulya' => ['DF Ulya 1', 'DF Ulya 2', 'DF Ulya 3'],
            'mahad-aly' => ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4'],
        ];

        foreach ($this->namaSantri as $i => $nama) {
            $no = $i + 1;
            $j = $jenjang[$i % count($jenjang)];

            // Sebar 20 santri ke akun wali & pendaftaran yang diterima
            // (aman jika salah satu daftar kosong).
            $userId = $wali === [] ? null : $wali[$i % count($wali)]->id;
            $pendaftaranId = $diterima === [] ? null : $diterima[$i % count($diterima)]->id;

            $data = $this->hanyaKolom('santri', [
                'user_id' => $userId,
                'pendaftaran_online_id' => $pendaftaranId,
                'nomor_induk' => 'DMS-2026-'.str_pad((string) $no, 3, '0', STR_PAD_LEFT),
                'nama_lengkap' => $nama,
                'jenjang_pendidikan' => $j['nama'],
                'kelas' => $kelas[$j['slug']][$i % count($kelas[$j['slug']])],
                'alamat' => 'Jl. Lintas Pinarik Papaso Km. '.(10 + $i).', Desa Pinarik, Kabupaten Padang Lawas',
                'nama_wali' => $this->namaWali[$i],
            ]);

            if ($data === []) {
                continue;
            }

            Santri::firstOrCreate(
                ['nomor_induk' => 'DMS-2026-'.str_pad((string) $no, 3, '0', STR_PAD_LEFT)],
                $data
            );
        }
    }

    private function seedAlumni(): void
    {
        if (! Schema::hasTable('alumni')) {
            return;
        }

        $kegiatan = Alumni::KEGIATAN;
        $jenjang = Alumni::JENJANG;
        $instansi = [
            'Universitas Islam Negeri Sumatera Utara',
            'Universitas Al-Azhar Kairo',
            'Pondok Pesantren Babul Hasanah',
            'Universitas Sumatera Utara',
            'UIN Sunan Kalijaga',
            'PT Perkebunan Nusantara IV',
            'CV Berkah Mandiri',
            'Sekolah Islam Terpadu Al-Falah',
        ];

        foreach ($this->namaAlumni as $i => $nama) {
            $no = $i + 1;

            $data = $this->hanyaKolom('alumni', [
                'nama' => $nama,
                'jenis_kelamin' => $i % 2 === 0 ? 'L' : 'P',
                'tahun_lulus' => 2019 + ($i % 6),
                'jenjang' => $jenjang[$i % count($jenjang)],
                'kegiatan' => $kegiatan[$i % count($kegiatan)],
                'instansi' => $instansi[$i % count($instansi)],
                'no_hp' => '0815'.str_pad((string) (66000000 + $no), 8, '0', STR_PAD_LEFT),
                'email' => 'dummy.alumni'.str_pad((string) $no, 2, '0', STR_PAD_LEFT).'@example.test',
                'alamat' => 'Jl. Lintas Pinarik Papaso Km. '.(5 + $i).', Padang Lawas',
                'testimoni' => 'Alhamdulillah, ilmu yang dipelajari di Babul Hasanah sangat bermanfaat untuk studi dan pengabdian saya.',
            ]);

            if ($data === []) {
                continue;
            }

            Alumni::firstOrCreate(
                ['nama' => $nama, 'tahun_lulus' => $data['tahun_lulus'] ?? null],
                $data
            );
        }
    }

    private function seedKonten(): void
    {
        if (! Schema::hasTable('konten')) {
            return;
        }

        $items = [
            'sejarah_hero_eyebrow' => ['Babul Hasanah (Dummy)', 'text'],
            'sejarah_hero_title' => ['Menyalakan lentera ilmu', 'text'],
            'sejarah_hero_title_accent' => ['di tanah Padang Lawas.', 'text'],
            'sejarah_hero_desc' => ['Pondok Pesantren Babul Hasanah tumbuh dari majelis kecil menjadi lembaga pendidikan Islam yang memadukan tradisi salaf dan kebutuhan zaman.', 'textarea'],
            'sejarah_tahun' => ['1998', 'text'],
            'sejarah_tahun_caption' => ['Awal ikhtiar pendidikan di Padang Lawas', 'text'],
            'sejarah_heading' => ['Awal berdirinya Pondok Pesantren Babul Hasanah', 'text'],
            'sejarah_page_p1' => ['Babul Hasanah bermula dari majelis pengajian sederhana pada tahun 1998. Para perintisnya berikhtiar menyediakan pendidikan agama bagi anak-anak di sekitar Padang Lawas.', 'textarea'],
            'sejarah_page_p2' => ['Seiring waktu, pesantren berkembang menjadi lembaga pendidikan Islam dengan tujuh jenjang, dari diniyah dasar hingga pendidikan tinggi pesantren.', 'textarea'],
            'sejarah_pilar' => ["Kitab kuning|Mendalami turats secara bertahap.\nAkhlak mulia|Adab menjadi dasar setiap proses belajar.\nPengabdian|Ilmu diamalkan untuk umat.", 'textarea'],
            'sejarah_makna_nama' => ['Babul Hasanah berarti pintu kebaikan, harapan agar setiap santri menjadi pembawa kebaikan.', 'textarea'],
            'sejarah_hinggakini_text' => ['Hingga kini Babul Hasanah melayani santri dari berbagai daerah dengan kurikulum kepesantrenan dan madrasah.', 'textarea'],
            'sejarah_budaya_heading' => ['Budaya pesantren yang hidup', 'text'],
            'sejarah_budaya_desc' => ['Kebersamaan, kesederhanaan, dan kedisiplinan menjadi napas kehidupan santri setiap hari.', 'textarea'],
            'sejarah_kajian_heading' => ['Kajian kitab dan halaqah', 'text'],
            'sejarah_kajian_desc' => ['Kajian kitab kuning dilaksanakan secara bertingkat sesuai kemampuan santri.', 'textarea'],
            'visi_page_visi' => ['Terwujudnya generasi muslim yang berilmu, berakhlak mulia, dan berdaya guna bagi masyarakat.', 'textarea'],
            'visi_page_misi' => ["Menyelenggarakan pendidikan Islam terpadu|Memadukan ilmu agama dan umum.\nMembina tahfidz Al-Qur'an|Membiasakan interaksi dengan Al-Qur'an.\nMenumbuhkan jiwa pengabdian|Mendorong santri berkontribusi bagi umat.", 'textarea'],
            'visi_page_motto' => ['Berilmu, Berakhlak, Bermanfaat', 'text'],
            'pembelajaran_hero_desc' => ['Pembelajaran di Babul Hasanah dirancang agar santri tumbuh seimbang dalam ilmu, adab, dan keterampilan.', 'textarea'],
        ];

        foreach ($items as $key => [$value, $type]) {
            Konten::firstOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => $type]
            );
        }
    }

    private function seedBerita(): void
    {
        if (! Schema::hasTable('beritas')) {
            return;
        }

        $kategori = Berita::KATEGORI;

        foreach ($this->judulBerita as $i => $judul) {
            $slug = Str::slug($judul).'-dummy';
            $status = $i < 16 ? 'published' : 'draft';

            $data = $this->hanyaKolom('beritas', [
                'judul' => $judul,
                'slug' => $slug,
                'kategori' => $kategori[$i % count($kategori)],
                'ringkasan' => Str::limit('Kegiatan dan informasi terbaru dari Pondok Pesantren Babul Hasanah. '.$judul.' menjadi bagian dari dinamika pendidikan santri.', 150),
                'isi' => '<p>'.$judul.' menjadi salah satu agenda penting di Pondok Pesantren Babul Hasanah.</p>'
                    .'<p>Kegiatan ini melibatkan santri, asatidz, dan pengurus pesantren. Diharapkan seluruh rangkaian acara berjalan lancar serta memberi manfaat bagi pengembangan ilmu dan adab santri.</p>'
                    .'<p>Pesantren terus berkomitmen menghadirkan pendidikan yang seimbang antara ilmu agama, pengetahuan umum, dan pembinaan karakter.</p>',
                'gambar' => null,
                'penulis' => 'Admin Dummy',
                'status' => $status,
                'published_at' => $status === 'published' ? now()->subDays(20 - $i) : null,
                'views' => 45 + ($i * 13),
            ]);

            if ($data === []) {
                continue;
            }

            Berita::firstOrCreate(['slug' => $slug], $data);
        }
    }

    private function seedInfoHarian(): void
    {
        if (! Schema::hasTable('info_harian')) {
            return;
        }

        $info = [
            ['Pengumuman', 'Pembayaran SPP bulan ini dibuka', 'Pembayaran dapat dilakukan melalui bendahara pesantren setiap hari kerja.'],
            ['Pengumuman', 'Pendaftaran santri baru gelombang 2 dibuka', 'Calon santri dapat mendaftar secara online hingga akhir bulan.'],
            ['Agenda', 'Rapat dewan asatidz', 'Rapat evaluasi pembelajaran dilaksanakan ba\'da Isya di ruang guru.'],
            ['Agenda', 'Gotong royong kebersihan asrama', 'Santri berkumpul pukul 06.30 di lapangan utama.'],
            ['Libur', 'Libur semester ganjil', 'Kegiatan belajar dimulai kembali sesuai kalender akademik.'],
            ['Libur', 'Kegiatan Ahad pagi diliburkan', 'Kegiatan kembali normal pada Senin pagi.'],
            ['Pengumuman', 'Pemeriksaan kesehatan santri', 'Pemeriksaan gratis bekerja sama dengan puskesmas setempat.'],
            ['Agenda', 'Festival seni islami antar jenjang', 'Setiap jenjang mengirimkan perwakilan terbaiknya.'],
            ['Pengumuman', 'Pembagian rapor semester', 'Wali santri diharapkan hadir tepat waktu.'],
            ['Agenda', 'Pelatihan kepemimpinan santri', 'Diikuti pengurus organisasi santri seluruh jenjang.'],
            ['Pengumuman', 'Pengambilan seragam santri baru', 'Seragam dapat diambil di kantor administrasi.'],
            ['Agenda', 'Muhadharah mingguan', 'Latihan pidato tiga bahasa untuk santri tingkat menengah.'],
        ];

        foreach ($info as $i => [$kategori, $judul, $isi]) {
            $data = $this->hanyaKolom('info_harian', [
                'jenis' => 'info',
                'kategori' => $kategori,
                'judul' => $judul,
                'isi' => $isi,
                'tanggal' => now()->subDays($i % 5)->toDateString(),
                'berlaku_sampai' => now()->addDays(30)->toDateString(),
                'penting' => $i < 3,
                'aktif' => true,
                'urutan' => $i,
            ]);

            if ($data !== []) {
                InfoHarian::firstOrCreate(['jenis' => 'info', 'judul' => $judul], $data);
            }
        }

        $jadwal = [
            ['04.30', "Subuh berjamaah & halaqah Al-Qur'an"],
            ['06.30', 'Kegiatan kebersihan & olahraga pagi'],
            ['07.00', 'Pembelajaran madrasah'],
            ['10.00', 'Istirahat & makan siang'],
            ['12.15', 'Dzuhur berjamaah'],
            ['15.30', 'Ashar & ekstrakurikuler'],
            ['18.15', 'Maghrib & kajian kitab'],
            ['20.00', "Muraja'ah & belajar mandiri"],
        ];

        foreach ($jadwal as $i => [$jam, $kegiatan]) {
            $data = $this->hanyaKolom('info_harian', [
                'jenis' => 'jadwal',
                'kategori' => 'Jadwal',
                'judul' => $kegiatan,
                'isi' => 'Kegiatan rutin harian santri Babul Hasanah.',
                'jam' => $jam,
                'tanggal' => now()->toDateString(),
                'berlaku_sampai' => now()->addDays(30)->toDateString(),
                'penting' => false,
                'aktif' => true,
                'urutan' => $i,
            ]);

            if ($data !== []) {
                InfoHarian::firstOrCreate(['jenis' => 'jadwal', 'judul' => $kegiatan], $data);
            }
        }
    }

    private function seedStruktur(): void
    {
        if (! Schema::hasTable('struktur_organisasi')) {
            return;
        }

        $bagan = [
            ['Pengasuh Pesantren', 'Pengasuh', 'fa-user-tie'],
            ['Ketua Yayasan', 'Ketua', 'fa-user-tie'],
            ["Mudir Ma'had", 'Mudir', 'fa-user-graduate'],
            ['Wakil Mudir', 'Wakil Mudir', 'fa-user'],
            ['Sekretaris Pesantren', 'Sekretaris', 'fa-pen-nib'],
            ['Bendahara Pesantren', 'Bendahara', 'fa-wallet'],
            ['Kabid Pendidikan', 'Kepala Bidang', 'fa-book-open'],
            ['Kabid Asrama', 'Kepala Bidang', 'fa-house-chimney'],
        ];

        $induk = null;

        foreach ($bagan as $i => [$nama, $jabatan, $ikon]) {
            $data = $this->hanyaKolom('struktur_organisasi', [
                'kategori' => 'bagan',
                'parent_id' => $i === 0 ? null : $induk,
                'nama' => $nama.' (Dummy)',
                'jabatan' => $jabatan,
                'ikon' => $ikon,
                'keterangan' => 'Data dummy untuk pengujian struktur organisasi.',
                'urutan' => $i,
                'aktif' => true,
            ]);

            if ($data === []) {
                continue;
            }

            $row = StrukturOrganisasi::firstOrCreate(
                ['kategori' => 'bagan', 'nama' => $nama.' (Dummy)'],
                $data
            );

            $induk ??= $row->id;
        }

        $jenjang = Jenjang::semua();

        foreach (array_slice($jenjang, 0, 5) as $i => $j) {
            $data = $this->hanyaKolom('struktur_organisasi', [
                'kategori' => 'jenjang',
                'parent_id' => null,
                'nama' => 'Ketua '.$j['kode'].' (Dummy)',
                'jabatan' => 'Ketua Jenjang',
                'kode' => $j['kode'],
                'ikon' => $j['ikon'],
                'keterangan' => 'Penanggung jawab jenjang '.$j['nama'].'.',
                'urutan' => $i,
                'aktif' => true,
            ]);

            if ($data !== []) {
                StrukturOrganisasi::firstOrCreate(
                    ['kategori' => 'jenjang', 'kode' => $j['kode']],
                    $data
                );
            }
        }

        $bidang = [
            ['Humas & Media', 'fa-bullhorn'],
            ['Kesehatan Santri', 'fa-kit-medical'],
            ['Keamanan & Disiplin', 'fa-shield-heart'],
            ['Pengembangan Bahasa', 'fa-language'],
            ['Sarana & Prasarana', 'fa-house-chimney'],
            ["Tahfidz Al-Qur'an", 'fa-book-quran'],
            ['Ekstrakurikuler', 'fa-user-graduate'],
        ];

        foreach ($bidang as $i => [$nama, $ikon]) {
            $data = $this->hanyaKolom('struktur_organisasi', [
                'kategori' => 'bidang',
                'parent_id' => null,
                'nama' => $nama.' (Dummy)',
                'jabatan' => 'Koordinator Bidang',
                'ikon' => $ikon,
                'keterangan' => 'Bidang pelayanan santri dan pesantren.',
                'urutan' => $i,
                'aktif' => true,
            ]);

            if ($data !== []) {
                StrukturOrganisasi::firstOrCreate(
                    ['kategori' => 'bidang', 'nama' => $nama.' (Dummy)'],
                    $data
                );
            }
        }
    }

    private function seedGaleri(): void
    {
        if (! Schema::hasTable('albums')) {
            return;
        }

        $kategori = ['Kegiatan', 'Prestasi', 'Fasilitas', 'Wisuda', 'Ramadhan'];
        $judulAlbum = [
            'Kegiatan Belajar Santri MDTA',
            'Wisuda Tahfidz Angkatan ke-12',
            'Festival Seni Islami 2026',
            'Gotong Royong Bersih Asrama',
            'Kunjungan Studi Banding',
            'Peringatan Maulid Nabi',
            'Lomba Futsal Antar Pesantren',
            'Praktik Kaligrafi Santri',
            'Pembukaan Tahun Akademik',
            'Peringatan Hari Santri',
            'Kegiatan Muhadharah',
            'Pemeriksaan Kesehatan Santri',
            'Pembagian Rapor Semester',
            'Pelatihan Kepemimpinan',
            'Kajian Kitab Kuning',
            'Peringatan Isra Mi\'raj',
            'Buka Bersama Ramadhan',
            'Kegiatan Ekstrakurikuler',
            'Renovasi Asrama Putri',
            'Pelepasan Santri Alumni',
        ];

        foreach ($judulAlbum as $i => $judul) {
            $data = $this->hanyaKolom('albums', [
                'judul' => $judul.' (Dummy)',
                'kategori' => $kategori[$i % count($kategori)],
                'tanggal' => now()->subDays(60 - ($i * 2))->toDateString(),
                'deskripsi' => 'Dokumentasi '.$judul.' di Pondok Pesantren Babul Hasanah.',
                'sampul' => null,
            ]);

            if ($data === []) {
                continue;
            }

            $album = Album::firstOrCreate(['judul' => $judul.' (Dummy)'], $data);

            if (! Schema::hasTable('album_media')) {
                continue;
            }

            $media = $this->hanyaKolom('album_media', [
                'album_id' => $album->id,
                'tipe' => 'link',
                'url' => 'https://www.youtube.com/watch?v=ScMzIvxBSi4',
                'keterangan' => 'Video dokumentasi '.$judul,
                'urutan' => 0,
            ]);

            if ($media !== []) {
                AlbumMedia::firstOrCreate(
                    ['album_id' => $album->id, 'url' => $media['url']],
                    $media
                );
            }
        }
    }

    private function seedPrestasi(): void
    {
        if (! Schema::hasTable('prestasi_santri')) {
            return;
        }

        $kategori = ['Tahfidz', 'Pidato', 'Kaligrafi', 'Olimpiade Matematika', 'MTQ', 'Futsal', 'Karya Tulis', 'Robotik'];
        $tingkat = ['Kecamatan', 'Kabupaten', 'Provinsi', 'Nasional', 'Internasional'];
        $wilayah = ['Padang Lawas', 'Medan', 'Sumatera Utara', 'Jakarta', 'Bandung'];

        for ($i = 0; $i < 20; $i++) {
            $nama = $this->namaSantri[$i];
            $peringkat = ($i % 4) + 1;
            $label = $peringkat <= 3 ? 'Juara '.$peringkat : 'Penghargaan';
            $judul = $kategori[$i % count($kategori)].' '.$tingkat[$i % count($tingkat)].' '.(2022 + ($i % 5));

            $data = $this->hanyaKolom('prestasi_santri', [
                'judul' => $judul,
                'nama' => $nama,
                'kelas' => ['VII A', 'VIII B', 'IX A', 'X IPA', 'XI IPS', 'XII IPA'][$i % 6],
                'kategori' => $kategori[$i % count($kategori)],
                'tingkat' => $tingkat[$i % count($tingkat)],
                'wilayah' => $wilayah[$i % count($wilayah)],
                'tahun' => 2022 + ($i % 5),
                'peringkat' => $peringkat,
                'label' => $label,
                'deskripsi' => 'Prestasi diraih oleh '.$nama.' dalam ajang '.$judul.'.',
            ]);

            if ($data !== []) {
                PrestasiSantri::firstOrCreate(['judul' => $judul, 'nama' => $nama], $data);
            }
        }
    }

    private function seedAbsensi(): void
    {
        if (! Schema::hasTable('absensi') || ! Schema::hasTable('santri')) {
            return;
        }

        $santri = Santri::where('nomor_induk', 'like', 'DMS-2026-%')->get();

        if ($santri->isEmpty()) {
            return;
        }

        $kegiatan = ['Sholat Berjamaah', 'Tahfidz', 'Sekolah Formal', 'Kajian Kitab', 'Ekstrakurikuler'];
        $status = ['hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpa'];

        for ($i = 0; $i < 20; $i++) {
            $s = $santri[$i % $santri->count()];
            $tanggal = now()->subDays($i % 14)->toDateString();
            $jenis = $kegiatan[$i % count($kegiatan)];

            $data = $this->hanyaKolom('absensi', [
                'santri_id' => $s->id,
                'jenis_kegiatan' => $jenis,
                'keterangan' => $status[$i % count($status)] === 'hadir'
                    ? 'Mengikuti kegiatan dengan tertib.'
                    : 'Keterangan dummy untuk pengujian absensi.',
                'status_kehadiran' => $status[$i % count($status)],
                'tanggal' => $tanggal,
            ]);

            if ($data !== []) {
                Absensi::firstOrCreate(
                    ['santri_id' => $s->id, 'tanggal' => $tanggal, 'jenis_kegiatan' => $jenis],
                    $data
                );
            }
        }
    }

    /**
     * @param  array<int, User>  $users
     */
    private function seedCatatanKalender(array $users): void
    {
        if (! Schema::hasTable('catatan_kalender')) {
            return;
        }

        $admin = null;

        foreach ($users as $u) {
            if (in_array($u->role, ['super_admin', 'admin'], true)) {
                $admin = $u;
                break;
            }
        }

        $catatan = [
            'Rapat koordinasi dewan asatidz',
            'Pembagian rapor semester ganjil',
            'Pemeriksaan kesehatan santri',
            'Gotong royong asrama putri',
            'Kunjungan wali santri',
            'Pelatihan kepemimpinan OSIS',
            'Festival seni islami',
            'Muhadharah tiga bahasa',
            'Pembayaran SPP bulanan',
            'Rapat evaluasi kurikulum',
            'Kegiatan tahfidz intensif',
            'Lomba kaligrafi antar jenjang',
            'Peringatan hari santri',
            'Buka bersama ramadhan',
            'Pelepasan santri alumni',
            'Pembukaan pendaftaran santri baru',
            'Kerja bakti lingkungan pesantren',
            'Pemeriksaan kesehatan berkala',
            'Rapat pengurus yayasan',
            'Penutupan kegiatan semester',
        ];

        $warna = ['#12a67a', '#2563eb', '#b45309', '#e11d48', '#8b5cc6'];

        foreach ($catatan as $i => $teks) {
            $tanggal = now()->addDays($i - 10)->toDateString();

            $data = $this->hanyaKolom('catatan_kalender', [
                'user_id' => $admin->id ?? null,
                'tanggal' => $tanggal,
                'catatan' => $teks.' (dummy)',
                'warna' => $warna[$i % count($warna)],
            ]);

            if ($data !== []) {
                CatatanKalender::firstOrCreate(
                    ['tanggal' => $tanggal, 'catatan' => $teks.' (dummy)'],
                    $data
                );
            }
        }
    }
}
