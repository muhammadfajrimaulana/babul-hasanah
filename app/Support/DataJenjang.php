<?php

namespace App\Support;

/**
 * Data default tujuh halaman jenjang.
 *
 * Sebelumnya data ini ditulis ulang di masing-masing file Blade. Dua file
 * (pdf-wustha dan mahad-aly) bahkan masih menyalin konten PPS Wustha.
 * Sekarang ada satu sumber data; Blade hanya merender variabel $p.
 *
 * CATATAN: nama ketua di sini masih contoh. Aset placeholder sudah tersedia
 * di public/images/program dan public/images/pengurus; ganti dengan foto resmi
 * pesantren sebelum dipublikasikan.
 */
class DataJenjang
{
    public const SLUGS = [
        'mdta', 'pps-wustha', 'mts', 'ma',
        'pdf-wustha', 'pdf-ulya', 'mahad-aly',
    ];

    public static function untuk(string $slug): ?array
    {
        return self::DATA[$slug] ?? null;
    }

    private const DATA = [
        'mdta' => [
            'judul_halaman' => 'Madrasah Diniyah Takmiliyah (MDTA)',
            'kode' => 'MDTA',
            'nama' => 'Madrasah Diniyah Takmiliyah',
            'ikon' => 'fa-book-quran',
            'banner' => 'mdta.jpg',
            'tagline' => 'Pendidikan diniyah dasar yang menanamkan bacaan Al-Qur’an, ibadah, dan akhlak sejak dini.',
            'deskripsi' => [
                'MDTA adalah pintu pertama santri mengenal ilmu agama secara terarah. Di sini santri belajar membaca Al-Qur’an dengan benar, memahami tata cara ibadah, dan membiasakan adab dalam kehidupan sehari-hari.',
                'Pembelajaran disampaikan dengan cara yang ramah bagi santri tingkat dasar: bertahap, banyak praktik, dan diulang secara rutin sampai menjadi kebiasaan.',
            ],
            'fakta' => [
                ['fa-user-graduate', 'Sasaran', 'Santri tingkat dasar'],
                ['fa-hourglass-half', 'Lama belajar', '4 tahun'],
                ['fa-clock', 'Waktu belajar', 'Sore hingga malam'],
                ['fa-certificate', 'Capaian', 'Syahadah MDTA'],
            ],
            'kurikulum_intro' => 'Fondasi ibadah, bacaan Al-Qur’an, dan akhlak yang dipelajari bertahap dari kelas awal.',
            'kurikulum' => [
                ['ikon' => 'fa-book-quran', 'judul' => 'Al-Qur’an', 'items' => ['Iqra’ dan tilawah', 'Tajwid dasar', 'Tahsin', 'Hafalan juz ‘amma', 'Doa harian']],
                ['ikon' => 'fa-hands-praying', 'judul' => 'Ibadah dan fiqih dasar', 'items' => ['Safinatun Najah', 'Thaharah', 'Tata cara shalat', 'Puasa dan zakat']],
                ['ikon' => 'fa-heart', 'judul' => 'Akidah dan akhlak', 'items' => ['Aqidatul Awam', 'Akhlaq lil Banin', 'Adab sehari-hari']],
                ['ikon' => 'fa-language', 'judul' => 'Bahasa Arab dan sejarah', 'items' => ['Mufradat dasar', 'Tulisan Arab', 'Sirah Nabawiyah']],
            ],
            'capaian' => [
                'Lancar membaca Al-Qur’an dengan tajwid dasar.',
                'Hafal surat-surat pendek dan doa sehari-hari.',
                'Menjalankan ibadah wajib dengan tata cara yang benar.',
                'Beradab kepada orang tua, guru, dan teman.',
            ],
            'kegiatan' => [
                'Setoran bacaan dan hafalan harian',
                'Praktik wudhu dan shalat',
                'Lomba tilawah dan hafalan',
                'Peringatan hari besar Islam',
            ],
            'ketua' => [
                'nama' => 'Ust. Nama Ketua MDTA',
                'jabatan' => 'Ketua MDTA',
                'foto' => 'ketua-mdta.jpg',
                'sambutan' => 'Kami mendampingi santri melangkah dari huruf hijaiyah sampai terbiasa beribadah dan berakhlak baik. Setiap kemajuan kecil kami rayakan bersama.',
            ],
        ],

        'pps-wustha' => [
            'judul_halaman' => 'Pendidikan Pesantren Salafiyah Wustha (PPS Wustha)',
            'kode' => 'PPS Wustha',
            'nama' => 'Pendidikan Pesantren Salafiyah Wustha',
            'ikon' => 'fa-award',
            'banner' => 'pps-wustha.jpg',
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
                'nama' => 'Ust. Nama Ketua PPS Wustha',
                'jabatan' => 'Ketua PPS Wustha',
                'foto' => 'ketua-pps-wustha.jpg',
                'sambutan' => 'Kami mendampingi santri memahami kitab dan mengamalkannya dalam kehidupan sehari-hari dengan sabar dan penuh keteladanan.',
            ],
        ],

        'mts' => [
            'judul_halaman' => 'Madrasah Tsanawiyah (MTs)',
            'kode' => 'MTs',
            'nama' => 'Madrasah Tsanawiyah',
            'ikon' => 'fa-school',
            'banner' => 'mts.jpg',
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
                'nama' => 'Ust. Nama Ketua MTs',
                'jabatan' => 'Ketua MTs',
                'foto' => 'ketua-mts.jpg',
                'sambutan' => 'Kami mendidik santri agar cerdas di kelas dan kuat di akhlak. Keduanya harus berjalan bersama.',
            ],
        ],

        'ma' => [
            'judul_halaman' => 'Madrasah Aliyah (MA)',
            'kode' => 'MA',
            'nama' => 'Madrasah Aliyah',
            'ikon' => 'fa-graduation-cap',
            'banner' => 'ma.jpg',
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
                'nama' => 'Ust. Nama Ketua MA',
                'jabatan' => 'Ketua MA',
                'foto' => 'ketua-ma.jpg',
                'sambutan' => 'Kami menyiapkan santri menjadi pribadi yang matang: kuat ilmunya, jernih pikirannya, dan siap berkhidmat di mana pun ditempatkan.',
            ],
        ],

        'pdf-wustha' => [
            'judul_halaman' => 'Pendidikan Diniyah Formal Wustha (PDF Wustha)',
            'kode' => 'PDF Wustha',
            'nama' => 'Pendidikan Diniyah Formal Wustha',
            'ikon' => 'fa-scroll',
            'banner' => 'pdf-wustha.jpg',
            'tagline' => 'Pendidikan diniyah formal jenjang Wustha dengan kurikulum kitab yang terstruktur.',
            'deskripsi' => [
                'PDF Wustha adalah jalur diniyah formal yang memadukan kajian kitab kuning dengan kurikulum terstruktur. Santri belajar ilmu alat, fiqih, dan akidah secara berjenjang.',
                'Pendidikan ini berjalan beriringan dengan pengajian pesantren, sehingga santri memiliki bekal ilmu agama yang kuat sekaligus mengikuti pendidikan formal.',
            ],
            'fakta' => [
                ['fa-user-graduate', 'Sasaran', 'Lulusan MDTA / sederajat'],
                ['fa-hourglass-half', 'Lama belajar', '3 tahun'],
                ['fa-list-check', 'Sistem', 'Diniyah formal berjenjang'],
                ['fa-certificate', 'Capaian', 'Ijazah PDF Wustha'],
            ],
            'kurikulum_intro' => 'Kurikulum kitab terstruktur dengan penekanan pada ilmu alat dan dasar fiqih.',
            'kurikulum' => [
                ['ikon' => 'fa-language', 'judul' => 'Ilmu alat', 'items' => ['Nahwu', 'Sharf', 'Mufradat', 'Balaghah dasar']],
                ['ikon' => 'fa-hands-praying', 'judul' => 'Fiqih', 'items' => ['Safinatun Najah', 'Fathul Qarib', 'Praktik ibadah']],
                ['ikon' => 'fa-book-quran', 'judul' => 'Al-Qur’an dan tafsir', 'items' => ['Tahsin dan tajwid', 'Tafsir dasar', 'Hafalan pilihan']],
                ['ikon' => 'fa-heart', 'judul' => 'Akidah dan akhlak', 'items' => ['Aqidatul Awam', 'Ta’lim Muta’allim', 'Adab santri']],
            ],
            'capaian' => [
                'Mampu membaca kitab dasar dengan bimbingan ustadz.',
                'Memahami dasar nahwu, sharf, dan fiqih ibadah.',
                'Memiliki hafalan Al-Qur’an dan doa harian.',
                'Siap melanjutkan ke PDF Ulya atau jenjang menengah.',
            ],
            'kegiatan' => [
                'Sorogan dan bandongan kitab',
                'Hafalan dan tahsin terjadwal',
                'Muhadharah mingguan',
                'Peringatan hari besar Islam',
            ],
            'ketua' => [
                'nama' => 'Ust. Nama Ketua PDF Wustha',
                'jabatan' => 'Ketua PDF Wustha',
                'foto' => 'ketua-pdf-wustha.jpg',
                'sambutan' => 'Di jenjang Wustha kami menata dasar ilmu santri dengan sabar: satu kitab demi satu kitab, sampai mereka mampu membaca dan mengamalkannya.',
            ],
        ],

        'pdf-ulya' => [
            'judul_halaman' => 'Pendidikan Diniyah Formal Ulya (PDF Ulya)',
            'kode' => 'PDF Ulya',
            'nama' => 'Pendidikan Diniyah Formal Ulya',
            'ikon' => 'fa-feather-pointed',
            'banner' => 'pdf-ulya.jpg',
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
                'nama' => 'Ust. Nama Ketua PDF Ulya',
                'jabatan' => 'Ketua PDF Ulya',
                'foto' => 'ketua-pdf-ulya.jpg',
                'sambutan' => 'Di Ulya, santri belajar bahwa ilmu adalah amanah. Kami melatih mereka berpikir jernih, merujuk kitab, dan rendah hati.',
            ],
        ],

        'mahad-aly' => [
            'judul_halaman' => 'Ma’had ‘Aly',
            'kode' => 'Ma’had ‘Aly',
            'nama' => 'Ma’had ‘Aly',
            'ikon' => 'fa-graduation-cap',
            'banner' => 'mahad-aly.jpg',
            'tagline' => 'Pendidikan tinggi pesantren untuk pendalaman keilmuan kitab tingkat lanjut.',
            'deskripsi' => [
                'Ma’had ‘Aly adalah jenjang pendidikan tinggi pesantren bagi santri yang ingin memperdalam keilmuan kitab dan menjadi ahli di bidangnya.',
                'Kajian difokuskan pada literatur turats, penalaran hukum, dan pembentukan santri yang mampu mengajar serta berkhidmat di masyarakat.',
            ],
            'fakta' => [
                ['fa-user-graduate', 'Sasaran', 'Lulusan PDF Ulya / MA'],
                ['fa-hourglass-half', 'Lama belajar', '2–3 tahun'],
                ['fa-landmark', 'Sistem', 'Pendidikan tinggi pesantren'],
                ['fa-certificate', 'Capaian', 'Syahadah Ma’had ‘Aly'],
            ],
            'kurikulum_intro' => 'Pendalaman keilmuan lanjut dengan fokus pada kitab induk dan bahtsul masail.',
            'kurikulum' => [
                ['ikon' => 'fa-scale-balanced', 'judul' => 'Fiqih dan ushul fiqih', 'items' => ['Fathul Wahhab', 'Lubbul Ushul', 'Bahtsul masail lanjutan']],
                ['ikon' => 'fa-scroll', 'judul' => 'Tafsir dan hadits', 'items' => ['Tafsir Jalalain', 'Bulughul Maram', 'Kajian sanad']],
                ['ikon' => 'fa-language', 'judul' => 'Bahasa dan literatur', 'items' => ['Alfiyah Ibnu Malik', 'Balaghah', 'Kitabah arabiyah']],
                ['ikon' => 'fa-hands-holding', 'judul' => 'Khidmah dan kepemimpinan', 'items' => ['Latihan mengajar', 'Bimbingan santri junior', 'Pengabdian masyarakat']],
            ],
            'capaian' => [
                'Menguasai kitab induk bidang keahliannya.',
                'Mampu menyusun jawaban hukum dengan merujuk dalil.',
                'Berpengalaman mengajar dan membimbing santri junior.',
                'Siap berkhidmat di pesantren dan masyarakat.',
            ],
            'kegiatan' => [
                'Bahtsul masail tingkat lanjut',
                'Khidmah mengajar di jenjang dasar dan menengah',
                'Diskusi kitab bersama pengasuh',
                'Pengabdian dan dakwah kemasyarakatan',
            ],
            'ketua' => [
                'nama' => 'Ust. Nama Ketua Ma’had ‘Aly',
                'jabatan' => 'Ketua Ma’had ‘Aly',
                'foto' => 'ketua-mahad-aly.jpg',
                'sambutan' => 'Ma’had ‘Aly menyiapkan santri menjadi ahli yang rendah hati: dalam ilmu mereka mendalam, dalam pengabdian mereka hadir untuk sesama.',
            ],
        ],
    ];
}
