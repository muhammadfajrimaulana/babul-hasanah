<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konten extends Model
{
    protected $table = 'konten';

    protected $fillable = ['key', 'value', 'type'];

    /**
     * Nilai bawaan halaman Sejarah & Visi-Misi.
     * Dipakai sebagai prefill form admin dan fallback halaman publik,
     * sehingga tampilan tetap sama sebelum konten diedit.
     */
    public const DEFAULTS = [
        // Halaman Sejarah
        'sejarah_hero_eyebrow' => 'Pondok Pesantren Babul Hasanah',
        'sejarah_hero_title' => 'Dari majelis kecil,',
        'sejarah_hero_title_accent' => 'tumbuh bersama umat.',
        'sejarah_hero_desc' => 'Berakar pada adab, bertumbuh dengan ilmu. Kenali sejarah, budaya, dan kajian yang menjadi denyut kehidupan santri Babul Hasanah.',
        'sejarah_tahun' => '1998',
        'sejarah_tahun_caption' => 'Awal sebuah ikhtiar di Padang Lawas',
        'sejarah_heading' => 'Awal berdirinya Pondok Pesantren Babul Hasanah',
        'sejarah_page_p1' => 'Babul Hasanah bermula dari majelis pengajian sederhana di Padang Lawas pada 1998. Para perintisnya didorong kepedulian terhadap pendidikan agama anak-anak dan masyarakat sekitar.',
        'sejarah_page_p2' => 'Dari majelis kecil itu, pesantren tumbuh menjadi lembaga pendidikan Islam yang memadukan tradisi keilmuan salaf dengan pendidikan yang relevan bagi masa depan. Setiap langkahnya terpusat pada tumbuh kembang santri, agar kelak menjadi insan berilmu, berakhlak mulia, dan berdaya guna.',
        'sejarah_pilar' => "Kitab kuning|Mendalami ilmu agama lewat kajian turats secara bertahap.\nAkhlak mulia|Adab dan keteladanan menjadi dasar setiap proses belajar.\nPengabdian|Ilmu diamalkan untuk keluarga, masyarakat, dan bangsa.",
        'sejarah_makna_nama' => 'berarti “pintu kebaikan”: harapan agar setiap santri yang masuk lewat pintu ini keluar sebagai pembawa kebaikan bagi keluarga, masyarakat, dan bangsa.',
        'sejarah_hinggakini_text' => 'Babul Hasanah melayani santri dari jenjang dasar hingga pendidikan tinggi pesantren, dikelola bersama dewan pengasuh dan tim pendidikan.',
        'sejarah_jenjang' => 'MDTA, PPS Wustha, MTs, MA, PDF Wustha, PDF Ulya, Ma’had ‘Aly',
        'sejarah_budaya_heading' => 'Satu pesantren, banyak budaya',
        'sejarah_budaya_desc' => 'Di sini santri belajar bahwa perbedaan asal daerah bukan jarak. Tradisi pesantren dan seni Islam hidup berdampingan dalam keseharian.',
        'sejarah_budaya' => "Santri dari berbagai daerah|Santri datang dari banyak kota dan suku. Bahasa daerah, makanan, dan kebiasaan yang berbeda justru mempererat persaudaraan.\nHadrah dan rebana|Dilatih rutin dan tampil di Maulid Nabi, haflah, serta undangan masyarakat sekitar.\nMuhadharah|Latihan pidato mingguan dalam bahasa Indonesia, Arab, dan Inggris agar santri berani tampil.\nGotong royong dan piket|Membersihkan asrama, masjid, dan dapur dikerjakan bersama. Kemandirian dilatih dari hal kecil.\nPencak silat|Seni bela diri tradisional untuk melatih disiplin, keberanian, dan sopan santun.",
        'sejarah_kajian_heading' => 'Kajian dan kitab yang dipelajari',
        'sejarah_kajian_desc' => 'Santri belajar bertahap, dari dasar bahasa Arab sampai tafsir dan tasawuf.',
        'sejarah_kajian_harian_judul' => 'Setiap hari',
        'sejarah_kajian_harian_catatan' => 'Dasar-dasar yang dipelajari santri setelah shalat Subuh, Ashar, dan Maghrib.',
        'sejarah_kajian_harian_items' => "Al-Qur’an|Tahsin, tajwid, dan setoran hafalan\nNahwu & Shorof|Jurumiyah, Imrithi, hingga Alfiyah Ibnu Malik\nFiqih|Safinatun Najah, Fathul Qarib\nAkhlak|Ta’lim Muta’allim, Washaya al-Aba’ lil Abna’",
        'sejarah_kajian_mingguan_judul' => 'Setiap pekan',
        'sejarah_kajian_mingguan_catatan' => 'Kajian yang lebih luas, sebagian terbuka untuk masyarakat umum.',
        'sejarah_kajian_mingguan_items' => "Tafsir|Tafsir Jalalain\nHadits|Riyadhus Shalihin, Bulughul Maram\nTasawuf|Kajian kitab Ihya’ Ulumuddin\nPengajian umum|Malam Jumat bersama pengasuh dan tamu undangan",
        'sejarah_kajian_tahunan_judul' => 'Sepanjang tahun',
        'sejarah_kajian_tahunan_catatan' => 'Kegiatan khusus pada waktu-waktu tertentu dalam kalender hijriah.',
        'sejarah_kajian_tahunan_items' => "Pesantren Kilat Ramadhan|Kajian intensif dan khataman kitab\nMaulid Nabi|Pembacaan maulid, hadrah, dan ceramah\nHaflah Akhirussanah|Wisuda santri dan pentas seni\nIsra’ Mi’raj|Pengajian akbar bersama masyarakat",
        'sejarah_motivasi_heading' => 'Semangat menuntut ilmu',
        'sejarah_motivasi' => "“Barang siapa menempuh jalan untuk menuntut ilmu, Allah mudahkan baginya jalan menuju surga.”|HR. Muslim\n“Menuntut ilmu itu wajib bagi setiap muslim.”|HR. Ibnu Majah",

        // Halaman Visi & Misi
        'visi_page_hero_eyebrow' => 'Pondok Pesantren Babul Hasanah',
        'visi_page_hero_title' => 'Cita-cita yang jelas,',
        'visi_page_hero_title_accent' => 'langkah yang nyata.',
        'visi_page_hero_desc' => 'Landasan kami dalam membangun generasi unggul, berilmu, dan berakhlak mulia: dari visi, misi, hingga nilai yang kami tanamkan setiap hari.',
        'visi_page_visi' => 'Membangun Generasi Unggul dalam Penguasaan Kitab Kuning, Berakhlak Mulia, dan Berwawasan Luas.',
        'visi_page_misi_heading' => 'Misi pondok pesantren',
        'visi_page_misi_desc' => 'Untuk mewujudkan visi tersebut, kami menjalankan lima misi berikut dengan penuh komitmen.',
        'visi_page_misi' => "Pendalaman Ilmu Agama|Menyelenggarakan pendidikan kitab kuning secara berjenjang dan sistematis dengan sanad keilmuan yang jelas.\nPembinaan Akhlak & Karakter|Menanamkan adab, kedisiplinan, kemandirian, dan keteladanan dalam kehidupan santri sehari-hari.\nPenguatan Wawasan & Keterampilan|Membekali santri dengan wawasan keilmuan, kemampuan berbahasa, dan keterampilan hidup yang relevan dengan zaman.\nLingkungan Pesantren yang Kondusif|Menciptakan suasana belajar yang aman, nyaman, dan islami untuk mendukung tumbuh kembang santri.\nPengabdian kepada Masyarakat|Mengembangkan dakwah dan kemaslahatan umat melalui kegiatan sosial, keagamaan, dan pendidikan.",
        'visi_page_nilai_heading' => 'Nilai-nilai utama',
        'visi_page_nilai_desc' => 'Empat pegangan yang kami tanamkan dalam keseharian santri.',
        'visi_page_nilai' => "Religius|Berpegang teguh pada Al-Qur’an, Sunnah, dan tradisi ulama salaf.\nIkhlas|Belajar dan mengabdi semata-mata mengharap ridha Allah.\nMandiri|Melatih santri hidup sederhana, disiplin, dan bertanggung jawab.\nUkhuwah|Menjalin persaudaraan yang erat antarsantri dan masyarakat.",
        'visi_page_motto_label' => 'Motto Pesantren',
        'visi_page_motto' => '“Berilmu, Beramal,',
        'visi_page_motto_accent' => 'Berakhlak Mulia”',

        // Halaman Pembelajaran
        'pembelajaran_hero_eyebrow' => 'Pondok Pesantren Babul Hasanah',
        'pembelajaran_hero_title' => 'Belajar bertahap,',
        'pembelajaran_hero_title_accent' => 'dari kitab hingga akhlak.',
        'pembelajaran_hero_desc' => 'Kurikulum terpadu yang memadukan tradisi keilmuan salaf, pembinaan akhlak, dan kegiatan penunjang untuk membentuk santri berilmu dan beradab.',
        'pembelajaran_jalur_eyebrow' => 'Sistem Pembelajaran',
        'pembelajaran_jalur_heading' => 'Pembelajaran dan kajian kitab kuning',
        'pembelajaran_jalur_desc' => 'Seluruh pelajaran di Babul Hasanah disampaikan melalui kitab-kitab klasik yang diajarkan bertahap sesuai kemampuan santri. Pengajian pesantren berjalan bersama pendidikan formal, sehingga santri tumbuh kuat dalam ilmu agama sekaligus siap menghadapi zamannya.',
        'pembelajaran_jalur' => "Kajian kitab kuning|Santri mempelajari kitab-kitab klasik secara bertahap sesuai kemampuan.\nPendidikan formal|Jenjang madrasah dan diniyah formal berjalan beriringan dengan pengajian pesantren.\nPembinaan karakter|Adab, bahasa, dan keterampilan dilatih lewat kegiatan harian dan ekstrakurikuler.",
        'pembelajaran_kajian_eyebrow' => 'Kajian & Kitab',
        'pembelajaran_kajian_heading' => 'Ilmu yang dipelajari santri',
        'pembelajaran_kajian_desc' => 'Pilih bidang ilmu untuk melihat penjelasan dan kitab yang dikaji.',
        'pembelajaran_kajian' => "Nahwu & Shorof|Fondasi membaca kitab gundul: tata bahasa dan perubahan kata dalam bahasa Arab.|Jurumiyah, Imrithi, Alfiyah, Amtsilati Tasrifiyah\nFiqih|Hukum ibadah dan muamalah menurut mazhab Syafi’i secara mendalam dan praktis.|Safinatun Najah, Fathul Qorib, Fathul Mu’in\nTauhid / Aqidah|Memperkokoh keyakinan sesuai ajaran Ahlussunnah wal Jama’ah.|Aqidatul Awam, Kifayatul Awam, Tijan Ad-Darari\nAkhlak & Tasawuf|Menata adab, hati, dan perilaku sebagai penuntut ilmu.|Ta’lim Muta’allim, Akhlaq lil Banin, Bidayatul Hidayah\nHadits & Tafsir|Memahami sabda Nabi dan kandungan Al-Qur’an melalui kajian para ulama.|Arba’in Nawawi, Riyadhus Shalihin, Tafsir Jalalain\nAl-Qur’an|Tahsin, tajwid, dan program tahfidz bagi santri yang berminat.|Tahsin, Tajwid, Tahfidz",
        'pembelajaran_jenjang_eyebrow' => 'Jenjang Pendidikan',
        'pembelajaran_jenjang_heading' => 'Jenjang dan program pendidikan',
        'pembelajaran_jenjang_desc' => 'Tujuh jenjang di bawah naungan Yayasan Pondok Pesantren Babul Hasanah, terbagi dalam dua jalur.',
        'pembelajaran_diniyah_title' => 'Jalur diniyah',
        'pembelajaran_diniyah_note' => 'Pendidikan berbasis kitab, dari tingkat dasar hingga Ma’had ‘Aly.',
        'pembelajaran_diniyah' => "MDTA|Madrasah Diniyah Takmiliyah|Pengenalan dasar membaca Al-Qur’an, ibadah, akhlak, dan pengantar bahasa Arab.\nPPS Wustha|Program pesantren jenjang Wustha|Pendalaman nahwu-shorof, fiqih, dan tauhid dengan kitab tingkat menengah.\nPDF Wustha|Pendidikan Diniyah Formal Wustha|Pendidikan diniyah formal jenjang Wustha dengan kurikulum kitab terstruktur.\nPDF Ulya|Pendidikan Diniyah Formal Ulya|Kajian lanjutan fiqih, tafsir, hadits, serta pelatihan bahtsul masail.\nMa’had ‘Aly|Pendidikan tinggi pesantren|Pendalaman keilmuan tingkat lanjut bagi santri yang ingin menjadi ahli di bidang kitab.",
        'pembelajaran_madrasah_title' => 'Jalur madrasah',
        'pembelajaran_madrasah_note' => 'Pendidikan formal yang berjalan bersama kegiatan pesantren.',
        'pembelajaran_madrasah' => "MTs|Madrasah Tsanawiyah|Pendidikan menengah pertama yang memadukan kurikulum madrasah dengan kegiatan pesantren.\nMA|Madrasah Aliyah|Pendidikan menengah atas yang menyiapkan santri melanjutkan studi dan berkhidmat di masyarakat.",
        'pembelajaran_metode_eyebrow' => 'Metode Pembelajaran',
        'pembelajaran_metode_heading' => 'Cara santri belajar',
        'pembelajaran_metode_desc' => 'Empat metode khas pesantren yang dipakai bergantian sesuai jenis kitab dan tingkat santri.',
        'pembelajaran_metode' => "Sorogan|Santri membaca kitab satu per satu di hadapan ustadz untuk dikoreksi langsung.\nBandongan|Ustadz membacakan dan menjelaskan kitab, santri menyimak dan memberi makna.\nMuhafazhah|Menghafal nazham dan matan kitab untuk memperkuat penguasaan ilmu.\nBahtsul masail|Diskusi kajian hukum untuk melatih nalar dan kemampuan merujuk kitab.",
        'pembelajaran_cta_title' => 'Siap belajar bersama kami?',
        'pembelajaran_cta_desc' => 'Mulai langkah pertama menuju ilmu yang bermanfaat dan akhlak yang mulia.',
        'pembelajaran_cta_button' => 'Daftar online',
        'pembelajaran_cta_url' => '/pendaftaran',
    ];

    /** Susunan section bawaan tiap halaman (untuk fitur ubah tata letak). */
    public const LAYOUTS = [
        'sejarah' => [
            ['id' => 'hero', 'label' => 'Hero / Header'],
            ['id' => 'sejarah', 'label' => 'Sejarah Pesantren'],
            ['id' => 'budaya', 'label' => 'Budaya Pesantren'],
            ['id' => 'kajian', 'label' => 'Kajian & Kitab'],
            ['id' => 'motivasi', 'label' => 'Kalimat Motivasi'],
        ],
        'visi' => [
            ['id' => 'hero', 'label' => 'Hero / Header'],
            ['id' => 'visi', 'label' => 'Visi'],
            ['id' => 'misi', 'label' => 'Misi'],
            ['id' => 'nilai', 'label' => 'Nilai Utama'],
            ['id' => 'motto', 'label' => 'Motto'],
        ],
        'pembelajaran' => [
            ['id' => 'hero', 'label' => 'Hero / Header'],
            ['id' => 'jalur', 'label' => 'Jalur Belajar'],
            ['id' => 'kajian', 'label' => 'Kajian & Kitab'],
            ['id' => 'jenjang', 'label' => 'Jenjang Pendidikan'],
            ['id' => 'metode', 'label' => 'Metode Pembelajaran'],
            ['id' => 'cta', 'label' => 'Ajakan / CTA'],
        ],
    ];

    /**
     * Label & kelompok untuk tampilan tabel admin.
     * Urutan di sini menentukan urutan baris pada tabel.
     */
    public const FIELDS = [
        // Halaman Sejarah
        'sejarah_hero_eyebrow' => ['label' => 'Hero — Label kecil', 'group' => 'Sejarah'],
        'sejarah_hero_title' => ['label' => 'Hero — Judul (putih)', 'group' => 'Sejarah'],
        'sejarah_hero_title_accent' => ['label' => 'Hero — Judul (emas)', 'group' => 'Sejarah'],
        'sejarah_hero_desc' => ['label' => 'Hero — Deskripsi', 'group' => 'Sejarah'],
        'sejarah_tahun' => ['label' => 'Tahun berdiri', 'group' => 'Sejarah'],
        'sejarah_tahun_caption' => ['label' => 'Keterangan tahun', 'group' => 'Sejarah'],
        'sejarah_heading' => ['label' => 'Judul bagian sejarah', 'group' => 'Sejarah'],
        'sejarah_page_p1' => ['label' => 'Paragraf 1', 'group' => 'Sejarah'],
        'sejarah_page_p2' => ['label' => 'Paragraf 2', 'group' => 'Sejarah'],
        'sejarah_pilar' => ['label' => 'Pilar pesantren (Judul | Keterangan)', 'group' => 'Sejarah'],
        'sejarah_makna_nama' => ['label' => 'Makna nama', 'group' => 'Sejarah'],
        'sejarah_hinggakini_text' => ['label' => 'Teks "hingga kini"', 'group' => 'Sejarah'],
        'sejarah_jenjang' => ['label' => 'Daftar jenjang (pisahkan koma)', 'group' => 'Sejarah'],
        'sejarah_budaya_heading' => ['label' => 'Budaya — Judul bagian', 'group' => 'Sejarah'],
        'sejarah_budaya_desc' => ['label' => 'Budaya — Deskripsi', 'group' => 'Sejarah'],
        'sejarah_budaya' => ['label' => 'Budaya — Kartu (Judul | Keterangan)', 'group' => 'Sejarah'],
        'sejarah_kajian_heading' => ['label' => 'Kajian — Judul bagian', 'group' => 'Sejarah'],
        'sejarah_kajian_desc' => ['label' => 'Kajian — Deskripsi', 'group' => 'Sejarah'],
        'sejarah_kajian_harian_judul' => ['label' => 'Kajian harian — Label tab', 'group' => 'Sejarah'],
        'sejarah_kajian_harian_catatan' => ['label' => 'Kajian harian — Catatan', 'group' => 'Sejarah'],
        'sejarah_kajian_harian_items' => ['label' => 'Kajian harian — Daftar (Judul | Keterangan)', 'group' => 'Sejarah'],
        'sejarah_kajian_mingguan_judul' => ['label' => 'Kajian mingguan — Label tab', 'group' => 'Sejarah'],
        'sejarah_kajian_mingguan_catatan' => ['label' => 'Kajian mingguan — Catatan', 'group' => 'Sejarah'],
        'sejarah_kajian_mingguan_items' => ['label' => 'Kajian mingguan — Daftar (Judul | Keterangan)', 'group' => 'Sejarah'],
        'sejarah_kajian_tahunan_judul' => ['label' => 'Kajian tahunan — Label tab', 'group' => 'Sejarah'],
        'sejarah_kajian_tahunan_catatan' => ['label' => 'Kajian tahunan — Catatan', 'group' => 'Sejarah'],
        'sejarah_kajian_tahunan_items' => ['label' => 'Kajian tahunan — Daftar (Judul | Keterangan)', 'group' => 'Sejarah'],
        'sejarah_motivasi_heading' => ['label' => 'Motivasi — Judul bagian', 'group' => 'Sejarah'],
        'sejarah_motivasi' => ['label' => 'Motivasi — Daftar (Kutipan | Sumber)', 'group' => 'Sejarah'],

        // Halaman Visi & Misi
        'visi_page_hero_eyebrow' => ['label' => 'Hero — Label kecil', 'group' => 'Visi & Misi'],
        'visi_page_hero_title' => ['label' => 'Hero — Judul (putih)', 'group' => 'Visi & Misi'],
        'visi_page_hero_title_accent' => ['label' => 'Hero — Judul (emas)', 'group' => 'Visi & Misi'],
        'visi_page_hero_desc' => ['label' => 'Hero — Deskripsi', 'group' => 'Visi & Misi'],
        'visi_page_visi' => ['label' => 'Teks visi', 'group' => 'Visi & Misi'],
        'visi_page_misi_heading' => ['label' => 'Misi — Judul bagian', 'group' => 'Visi & Misi'],
        'visi_page_misi_desc' => ['label' => 'Misi — Deskripsi', 'group' => 'Visi & Misi'],
        'visi_page_misi' => ['label' => 'Misi — Daftar (Judul | Keterangan)', 'group' => 'Visi & Misi'],
        'visi_page_nilai_heading' => ['label' => 'Nilai — Judul bagian', 'group' => 'Visi & Misi'],
        'visi_page_nilai_desc' => ['label' => 'Nilai — Deskripsi', 'group' => 'Visi & Misi'],
        'visi_page_nilai' => ['label' => 'Nilai — Daftar (Judul | Keterangan)', 'group' => 'Visi & Misi'],
        'visi_page_motto_label' => ['label' => 'Motto — Label', 'group' => 'Visi & Misi'],
        'visi_page_motto' => ['label' => 'Motto (putih)', 'group' => 'Visi & Misi'],
        'visi_page_motto_accent' => ['label' => 'Motto (emas)', 'group' => 'Visi & Misi'],

        // Halaman Pembelajaran
        'pembelajaran_hero_eyebrow' => ['label' => 'Hero — Label kecil', 'group' => 'Pembelajaran'],
        'pembelajaran_hero_title' => ['label' => 'Hero — Judul (putih)', 'group' => 'Pembelajaran'],
        'pembelajaran_hero_title_accent' => ['label' => 'Hero — Judul (emas)', 'group' => 'Pembelajaran'],
        'pembelajaran_hero_desc' => ['label' => 'Hero — Deskripsi', 'group' => 'Pembelajaran'],
        'pembelajaran_jalur_eyebrow' => ['label' => 'Jalur — Label kecil', 'group' => 'Pembelajaran'],
        'pembelajaran_jalur_heading' => ['label' => 'Jalur — Judul bagian', 'group' => 'Pembelajaran'],
        'pembelajaran_jalur_desc' => ['label' => 'Jalur — Paragraf pengantar', 'group' => 'Pembelajaran'],
        'pembelajaran_jalur' => ['label' => 'Jalur — Kartu (Judul | Keterangan)', 'group' => 'Pembelajaran'],
        'pembelajaran_kajian_eyebrow' => ['label' => 'Kajian — Label kecil', 'group' => 'Pembelajaran'],
        'pembelajaran_kajian_heading' => ['label' => 'Kajian — Judul bagian', 'group' => 'Pembelajaran'],
        'pembelajaran_kajian_desc' => ['label' => 'Kajian — Deskripsi', 'group' => 'Pembelajaran'],
        'pembelajaran_kajian' => ['label' => 'Kajian — Bidang (Judul | Keterangan | Kitab, Kitab)', 'group' => 'Pembelajaran'],
        'pembelajaran_jenjang_eyebrow' => ['label' => 'Jenjang — Label kecil', 'group' => 'Pembelajaran'],
        'pembelajaran_jenjang_heading' => ['label' => 'Jenjang — Judul bagian', 'group' => 'Pembelajaran'],
        'pembelajaran_jenjang_desc' => ['label' => 'Jenjang — Deskripsi', 'group' => 'Pembelajaran'],
        'pembelajaran_diniyah_title' => ['label' => 'Diniyah — Judul jalur', 'group' => 'Pembelajaran'],
        'pembelajaran_diniyah_note' => ['label' => 'Diniyah — Catatan', 'group' => 'Pembelajaran'],
        'pembelajaran_diniyah' => ['label' => 'Diniyah — Jenjang (Kode | Nama | Deskripsi)', 'group' => 'Pembelajaran'],
        'pembelajaran_madrasah_title' => ['label' => 'Madrasah — Judul jalur', 'group' => 'Pembelajaran'],
        'pembelajaran_madrasah_note' => ['label' => 'Madrasah — Catatan', 'group' => 'Pembelajaran'],
        'pembelajaran_madrasah' => ['label' => 'Madrasah — Jenjang (Kode | Nama | Deskripsi)', 'group' => 'Pembelajaran'],
        'pembelajaran_metode_eyebrow' => ['label' => 'Metode — Label kecil', 'group' => 'Pembelajaran'],
        'pembelajaran_metode_heading' => ['label' => 'Metode — Judul bagian', 'group' => 'Pembelajaran'],
        'pembelajaran_metode_desc' => ['label' => 'Metode — Deskripsi', 'group' => 'Pembelajaran'],
        'pembelajaran_metode' => ['label' => 'Metode — Kartu (Judul | Keterangan)', 'group' => 'Pembelajaran'],
        'pembelajaran_cta_title' => ['label' => 'CTA — Judul', 'group' => 'Pembelajaran'],
        'pembelajaran_cta_desc' => ['label' => 'CTA — Deskripsi', 'group' => 'Pembelajaran'],
        'pembelajaran_cta_button' => ['label' => 'CTA — Label tombol', 'group' => 'Pembelajaran'],
        'pembelajaran_cta_url' => ['label' => 'CTA — Tautan tombol', 'group' => 'Pembelajaran'],
    ];

    // Helper cepat untuk ambil nilai konten berdasarkan key
    public static function getVal($key, $default = null)
    {
        $item = self::where('key', $key)->first();

        return $item ? $item->value : $default;
    }

    /** Semua konten (1 query) yang sudah digabung dengan nilai bawaan. */
    public static function allWithDefaults(): array
    {
        return array_merge(self::DEFAULTS, self::query()->pluck('value', 'key')->all());
    }

    /** Sub-bagian tampilan admin berdasarkan prefix key. */
    public static function sectionFor(string $key): string
    {
        if (str_starts_with($key, 'visi_page_visi')) {
            return 'Visi';
        }

        $prefixes = [
            'sejarah_hero_' => 'Hero',
            'sejarah_budaya_' => 'Budaya',
            'sejarah_kajian_' => 'Kajian',
            'sejarah_motivasi' => 'Motivasi',
            'visi_page_hero_' => 'Hero',
            'visi_page_misi_' => 'Misi',
            'visi_page_nilai_' => 'Nilai',
            'visi_page_motto_' => 'Motto',
            'pembelajaran_hero_' => 'Hero',
            'pembelajaran_jalur' => 'Jalur Belajar',
            'pembelajaran_kajian_' => 'Kajian',
            'pembelajaran_jenjang_' => 'Jenjang',
            'pembelajaran_diniyah_' => 'Jenjang',
            'pembelajaran_madrasah_' => 'Jenjang',
            'pembelajaran_metode_' => 'Metode',
            'pembelajaran_cta_' => 'CTA',
        ];

        foreach ($prefixes as $prefix => $section) {
            if (str_starts_with($key, $prefix)) {
                return $section;
            }
        }

        if (str_starts_with($key, 'sejarah_')) {
            return 'Sejarah';
        }

        return 'Konten Tambahan';
    }

    /**
     * Susunan section yang tersimpan + bawaan.
     * Mengembalikan array berisi id, label, dan visible.
     */
    public static function layoutFor(string $page): array
    {
        $default = self::LAYOUTS[$page] ?? [];
        $raw = self::query()->where('key', 'layout_'.$page)->value('value');
        $saved = $raw ? json_decode($raw, true) : null;

        if (! is_array($saved)) {
            return array_map(fn (array $row) => $row + ['visible' => true], $default);
        }

        $labels = collect($default)->keyBy('id');
        $result = [];

        foreach ($saved as $row) {
            $id = $row['id'] ?? null;

            if (! $id || ! $labels->has($id) || isset($result[$id])) {
                continue;
            }

            $result[$id] = [
                'id' => $id,
                'label' => $labels[$id]['label'],
                'visible' => (bool) ($row['visible'] ?? true),
            ];
        }

        // Pastikan section baru tetap muncul walau belum tersimpan.
        foreach ($default as $row) {
            if (! isset($result[$row['id']])) {
                $result[$row['id']] = $row + ['visible' => true];
            }
        }

        return array_values($result);
    }
}
