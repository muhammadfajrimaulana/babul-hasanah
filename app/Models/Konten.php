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

        // Halaman Sejarah — dokumen riwayat (BAB I–III)
        'sejarah_doc_eyebrow' => 'Profil Pesantren',
        'sejarah_doc_title' => 'RIWAYAT PENDIRIAN DAN PERKEMBANGAN PONDOK PESANTREN BABUL HASANAH',
        'sejarah_doc_intro' => 'Menelusuri perjalanan Pondok Pesantren Babul Hasanah sejak masa perintisan hingga tumbuh menjadi lembaga pendidikan Islam yang bersama masyarakat.',
        'sejarah_doc_year' => '1997',
        'sejarah_doc_year_caption' => 'Awal perintisan di Padang Lawas',
        'sejarah_doc_note' => 'Sejak {tahun}, Babul Hasanah tumbuh bersama masyarakat sebagai rumah belajar Al-Qur’an, kitab kuning, dan pembinaan akhlak.',
        'sejarah_foto_caption' => 'Komplek Pondok Pesantren Babul Hasanah, tempat pendidikan dan pembinaan santri dari berbagai daerah.',
        'sejarah_bab1_a_title' => 'A. Latar Belakang Pendirian',
        'sejarah_bab1_a_body' => 'Pondok Pesantren Babul Hasanah lahir dari kepedulian terhadap pendidikan agama di tengah masyarakat Padang Lawas. Pada masa itu, akses pendidikan Islam yang terarah dan berkelanjutan masih terbatas. Anak-anak di sekitar lingkungan pesantren membutuhkan wadah untuk belajar membaca Al-Qur’an, memahami dasar-dasar ibadah, dan menanamkan akhlak mulia. Dari kebutuhan itulah para tokoh masyarakat dan keluarga besar pendiri berinisiatif menghadirkan majelis pendidikan yang sederhana namun berkelanjutan.',
        'sejarah_bab1_b_title' => 'B. Penggagas dan Pendiri Pesantren',
        'sejarah_bab1_b_body' => 'Pendirian Babul Hasanah tidak dapat dilepaskan dari peran para ulama, tokoh masyarakat, dan keluarga besar yang memiliki kepedulian terhadap pendidikan Islam. Mereka bersepakat menyiapkan tempat belajar, menghimpun dukungan, dan mengajak masyarakat untuk berpartisipasi. Kebersamaan tersebut menjadi kekuatan awal pesantren. Hingga kini, semangat kebersamaan itu dirawat sebagai bagian dari identitas lembaga.',
        'sejarah_bab1_c_title' => 'C. Alasan dan Tujuan Pendirian',
        'sejarah_bab1_c_body' => 'Pendirian pesantren dilatarbelakangi oleh keinginan menyediakan pendidikan Islam yang mudah dijangkau, berakar pada Al-Qur’an dan sunah, serta menghormati tradisi keilmuan ulama. Tujuannya adalah membentuk generasi yang berilmu, berakhlak mulia, mampu mengamalkan ajaran Islam dalam kehidupan sehari-hari, dan memberi manfaat bagi keluarga, masyarakat, serta bangsa.',
        'sejarah_bab2_title' => 'BAB II. PROSES PENDIRIAN PONDOK PESANTREN BABUL HASANAH',
        'sejarah_bab2_a_title' => 'A. Perintisan pada Tahun 1997',
        'sejarah_bab2_a_body' => 'Perintisan dimulai pada tahun 1997 dari kegiatan pengajian sederhana. Keterbatasan sarana tidak menghalangi para perintis untuk memulai. Dari rumah ke rumah, dari majelis ke majelis, kegiatan belajar terus dilanjutkan hingga semakin banyak santri yang bergabung. Tahun 1997 menjadi penanda awal semangat pendidikan yang kemudian menjadi landasan perkembangan pesantren.',
        'sejarah_bab2_b_title' => 'B. Semangat Kebersamaan dan Dukungan Masyarakat',
        'sejarah_bab2_b_body' => 'Perkembangan pesantren terjadi karena dukungan masyarakat. Warga bergotong royong menyiapkan tempat, tenaga, dan kebutuhan pendidikan. Dukungan itu tidak hanya berbentuk materi, tetapi juga kepercayaan untuk menitipkan anak-anak mereka belajar di pesantren. Semangat kebersamaan menjadi ciri khas yang terus dijaga.',
        'sejarah_bab2_c_title' => 'C. Makna Nama Babul Hasanah',
        'sejarah_bab2_c_body' => 'Nama Babul Hasanah berarti pintu kebaikan. Nama ini mengandung harapan agar setiap santri yang belajar di pesantren keluar sebagai pribadi yang membawa kebaikan bagi keluarga dan masyarakat. Makna tersebut menjadi pengingat bahwa pendidikan di pesantren bukan hanya soal kecakapan intelektual, tetapi juga pembentukan akhlak dan pengabdian.',
        'sejarah_bab3_title' => 'BAB III. KEPEMIMPINAN DAN PERKEMBANGAN PESANTREN',
        'sejarah_bab3_a_title' => 'A. Kepemimpinan KH. Mardin Hasibuan Asshiddiqy, M.M.Pd.',
        'sejarah_bab3_a_body' => 'Kepemimpinan KH. Mardin Hasibuan Asshiddiqy, M.M.Pd. menjadi bagian penting dalam perjalanan Babul Hasanah. Beliau memimpin dengan menekankan pentingnya ilmu, adab, dan pengabdian. Arahan beliau menjadi landasan dalam menata pendidikan, membina santri, dan menjaga hubungan baik dengan masyarakat. Kepemimpinan tersebut dijalankan bersama para asatidz, pengurus, dan keluarga besar pesantren.',
        'sejarah_bab3_b_title' => 'B. Pengembangan Pendidikan dan Tradisi Keilmuan',
        'sejarah_bab3_b_body' => 'Pesantren terus mengembangkan pendidikan dengan memadukan kajian kitab kuning dan pendidikan formal. Tradisi keilmuan dijaga melalui pengajian kitab, sorogan, bandongan, dan pembinaan hafalan. Pengembangan dilakukan secara bertahap agar tradisi salaf tetap hidup sekaligus santri memiliki bekal menghadapi perkembangan zaman.',
        'sejarah_bab3_c_title' => 'C. Sarana Pendidikan, Kehidupan Keagamaan, dan Kegiatan Santri',
        'sejarah_bab3_c_body' => 'Seiring waktu, sarana pendidikan dilengkapi untuk mendukung pembelajaran dan kehidupan santri. Kegiatan keagamaan seperti shalat berjamaah, pengajian, dan peringatan hari besar Islam menjadi bagian dari keseharian. Santri juga dibina melalui kegiatan ekstrakurikuler, latihan pidato, seni Islam, dan kegiatan sosial agar tumbuh mandiri serta peduli terhadap lingkungan.',

        // Halaman Sejarah — ajakan pendaftaran
        'sejarah_daftar_title' => 'Bergabung bersama Pondok Pesantren Babul Hasanah',
        'sejarah_daftar_desc' => 'Pondok Pesantren Babul Hasanah membuka pendaftaran santri baru. Sejak perintisan pada tahun 1997, pesantren mendidik santri melalui kajian kitab kuning, pendidikan formal, dan pembinaan akhlak di lingkungan asrama.',
        'sejarah_daftar_button' => 'Daftar Santri Baru',

        // Halaman Visi & Misi
        'visi_page_hero_eyebrow' => 'Pondok Pesantren Babul Hasanah',
        'visi_page_hero_title' => 'Cita-cita yang jelas,',
        'visi_page_hero_title_accent' => 'langkah yang nyata.',
        'visi_page_hero_desc' => 'Landasan kami dalam membangun generasi unggul, berilmu, dan berakhlak mulia: dari visi, misi, hingga nilai yang kami tanamkan setiap hari.',
        'visi_page_visi_heading' => 'Arah yang kami tuju bersama',
        'visi_page_visi_desc' => 'Visi menjadi kompas seluruh kegiatan pendidikan, pembinaan, dan pengabdian di Babul Hasanah.',
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
        'visi_page_bab5_title' => 'BAB V. VISI, HARAPAN, DAN KEBERLANJUTAN',
        'visi_page_bab5_body' => "Pondok Pesantren Babul Hasanah diharapkan terus berkembang sebagai lembaga pendidikan Islam yang berpegang teguh pada nilai-nilai Al-Qur’an dan sunah, menghormati tradisi keilmuan ulama, serta terbuka terhadap perkembangan pendidikan yang membawa manfaat.\n\nSemangat pendirian pada tahun 1997 perlu menjadi landasan untuk melanjutkan perjuangan pendidikan bagi generasi berikutnya. Pengembangan lembaga hendaknya memperhatikan peningkatan kualitas pembelajaran, kompetensi guru, pembinaan karakter santri, penguatan kajian kitab kuning, penyediaan sarana pendidikan, serta pemanfaatan teknologi secara bertanggung jawab.\n\nPengembangan teknologi tidak harus menghilangkan tradisi pesantren. Sebaliknya, teknologi dapat dimanfaatkan untuk memperluas akses terhadap sumber belajar, mengelola administrasi, mendokumentasikan karya dan kegiatan santri, serta menjaga arsip sejarah lembaga. Pemanfaatannya tetap perlu disertai adab, etika, dan bimbingan guru.\n\nHarapan tersebut sejalan dengan cita-cita membangun generasi yang tidak hanya memiliki kecakapan intelektual, tetapi juga kematangan spiritual, kemuliaan akhlak, dan kepedulian sosial.",

        // Halaman Fasilitas
        'fasilitas_photos' => '["storage/default-fasilitas-1.jpg","storage/default-fasilitas-2.jpg","storage/default-fasilitas-3.jpg"]',
        'fasilitas_heading' => 'Fasilitas Pondok Pesantren Babul Hasanah',
        'fasilitas_desc' => 'Sarana dan prasarana yang mendukung kegiatan belajar, ibadah, dan kehidupan santri di Babul Hasanah.',
        'fasilitas_1_title' => 'Gedung Pembelajaran',
        'fasilitas_1_desc' => 'Gedung belajar dengan ruang majelis, papan tulis, dan perlengkapan pembelajaran untuk pengajian kitab kuning serta pendidikan formal.',
        'fasilitas_2_title' => 'Asrama Santri',
        'fasilitas_2_desc' => 'Asrama putra dan putri dengan kamar tidur, area belajar, dan ruang bersama untuk kegiatan harian santri.',
        'fasilitas_3_title' => 'Masjid',
        'fasilitas_3_desc' => 'Masjid utama tempat shalat berjamaah, pengajian, dan kegiatan keagamaan santri bersama masyarakat.',
        'fasilitas_4_title' => 'Perpustakaan',
        'fasilitas_4_desc' => 'Perpustakaan dengan koleksi kitab kuning, buku pelajaran, dan referensi ilmu pengetahuan untuk santri.',

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
            ['id' => 'pendirian', 'label' => 'BAB I — Riwayat Pendirian'],
            ['id' => 'proses', 'label' => 'BAB II — Proses Pendirian'],
            ['id' => 'kepemimpinan', 'label' => 'BAB III — Kepemimpinan & Perkembangan'],
        ],
        'visi' => [
            ['id' => 'hero', 'label' => 'Hero / Header'],
            ['id' => 'visi', 'label' => 'Visi'],
            ['id' => 'misi', 'label' => 'Misi'],
            ['id' => 'nilai', 'label' => 'Nilai Utama'],
            ['id' => 'bab5', 'label' => 'BAB V — Visi, Harapan, dan Keberlanjutan'],
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
        'fasilitas' => [
            ['id' => 'hero', 'label' => 'Hero / Header'],
            ['id' => 'cards', 'label' => 'Empat Kartu Fasilitas'],
        ],
    ];

    /**
     * Label & kelompok untuk tampilan tabel admin.
     * Urutan di sini menentukan urutan baris pada tabel.
     */
    public const FIELDS = [
        // Halaman Sejarah — dokumen BAB I–III
        'sejarah_doc_eyebrow' => ['label' => 'Label kecil di atas judul', 'group' => 'Sejarah'],
        'sejarah_doc_title' => ['label' => 'Judul halaman', 'group' => 'Sejarah'],
        'sejarah_doc_intro' => ['label' => 'Deskripsi pembuka', 'group' => 'Sejarah'],
        'sejarah_doc_year' => ['label' => 'Tahun perintisan', 'group' => 'Sejarah'],
        'sejarah_doc_year_caption' => ['label' => 'Keterangan tahun', 'group' => 'Sejarah'],
        'sejarah_doc_note' => ['label' => 'Catatan pengantar (di atas Daftar Isi)', 'group' => 'Sejarah'],
        'sejarah_bab1_a_title' => ['label' => 'BAB I A — judul', 'group' => 'Sejarah'],
        'sejarah_bab1_a_body' => ['label' => 'BAB I A — isi', 'group' => 'Sejarah'],
        'sejarah_bab1_b_title' => ['label' => 'BAB I B — judul', 'group' => 'Sejarah'],
        'sejarah_bab1_b_body' => ['label' => 'BAB I B — isi', 'group' => 'Sejarah'],
        'sejarah_bab1_c_title' => ['label' => 'BAB I C — judul', 'group' => 'Sejarah'],
        'sejarah_bab1_c_body' => ['label' => 'BAB I C — isi', 'group' => 'Sejarah'],
        'sejarah_bab2_title' => ['label' => 'Judul BAB II', 'group' => 'Sejarah'],
        'sejarah_bab2_a_title' => ['label' => 'BAB II A — judul', 'group' => 'Sejarah'],
        'sejarah_bab2_a_body' => ['label' => 'BAB II A — isi', 'group' => 'Sejarah'],
        'sejarah_bab2_b_title' => ['label' => 'BAB II B — judul', 'group' => 'Sejarah'],
        'sejarah_bab2_b_body' => ['label' => 'BAB II B — isi', 'group' => 'Sejarah'],
        'sejarah_bab2_c_title' => ['label' => 'BAB II C — judul', 'group' => 'Sejarah'],
        'sejarah_bab2_c_body' => ['label' => 'BAB II C — isi', 'group' => 'Sejarah'],
        'sejarah_bab3_title' => ['label' => 'Judul BAB III', 'group' => 'Sejarah'],
        'sejarah_bab3_a_title' => ['label' => 'BAB III A — judul', 'group' => 'Sejarah'],
        'sejarah_bab3_a_body' => ['label' => 'BAB III A — isi', 'group' => 'Sejarah'],
        'sejarah_bab3_b_title' => ['label' => 'BAB III B — judul', 'group' => 'Sejarah'],
        'sejarah_bab3_b_body' => ['label' => 'BAB III B — isi', 'group' => 'Sejarah'],
        'sejarah_bab3_c_title' => ['label' => 'BAB III C — judul', 'group' => 'Sejarah'],
        'sejarah_bab3_c_body' => ['label' => 'BAB III C — isi', 'group' => 'Sejarah'],
        'sejarah_daftar_title' => ['label' => 'Pendaftaran — judul', 'group' => 'Sejarah'],
        'sejarah_daftar_desc' => ['label' => 'Pendaftaran — deskripsi', 'group' => 'Sejarah'],
        'sejarah_daftar_button' => ['label' => 'Pendaftaran — teks tombol', 'group' => 'Sejarah'],

        // Halaman Visi & Misi
        'visi_page_hero_eyebrow' => ['label' => 'Label kecil di atas judul', 'group' => 'Visi & Misi'],
        'visi_page_hero_title' => ['label' => 'Judul pembuka — baris 1', 'group' => 'Visi & Misi'],
        'visi_page_hero_title_accent' => ['label' => 'Judul pembuka — baris 2 (emas)', 'group' => 'Visi & Misi'],
        'visi_page_hero_desc' => ['label' => 'Deskripsi pembuka', 'group' => 'Visi & Misi'],
        'visi_page_visi_heading' => ['label' => 'Visi — judul bagian', 'group' => 'Visi & Misi'],
        'visi_page_visi_desc' => ['label' => 'Visi — deskripsi', 'group' => 'Visi & Misi'],
        'visi_page_visi' => ['label' => 'Teks visi', 'group' => 'Visi & Misi'],
        'visi_page_misi_heading' => ['label' => 'Judul bagian misi', 'group' => 'Visi & Misi'],
        'visi_page_misi_desc' => ['label' => 'Deskripsi misi', 'group' => 'Visi & Misi'],
        'visi_page_misi' => ['label' => 'Daftar misi', 'group' => 'Visi & Misi'],
        'visi_page_nilai_heading' => ['label' => 'Judul bagian nilai', 'group' => 'Visi & Misi'],
        'visi_page_nilai_desc' => ['label' => 'Deskripsi nilai', 'group' => 'Visi & Misi'],
        'visi_page_nilai' => ['label' => 'Daftar nilai utama', 'group' => 'Visi & Misi'],
        'visi_page_bab5_title' => ['label' => 'Judul BAB V', 'group' => 'Visi & Misi'],
        'visi_page_bab5_body' => ['label' => 'Isi BAB V', 'group' => 'Visi & Misi'],
        'visi_page_motto_label' => ['label' => 'Label motto', 'group' => 'Visi & Misi'],
        'visi_page_motto' => ['label' => 'Motto — baris 1', 'group' => 'Visi & Misi'],
        'visi_page_motto_accent' => ['label' => 'Motto — baris 2 (emas)', 'group' => 'Visi & Misi'],

        // Halaman Pembelajaran
        'pembelajaran_hero_eyebrow' => ['label' => 'Label kecil di atas judul', 'group' => 'Pembelajaran'],
        'pembelajaran_hero_title' => ['label' => 'Judul pembuka — baris 1', 'group' => 'Pembelajaran'],
        'pembelajaran_hero_title_accent' => ['label' => 'Judul pembuka — baris 2 (emas)', 'group' => 'Pembelajaran'],
        'pembelajaran_hero_desc' => ['label' => 'Deskripsi pembuka', 'group' => 'Pembelajaran'],
        'pembelajaran_jalur_eyebrow' => ['label' => 'Jalur — label kecil', 'group' => 'Pembelajaran'],
        'pembelajaran_jalur_heading' => ['label' => 'Jalur — judul bagian', 'group' => 'Pembelajaran'],
        'pembelajaran_jalur_desc' => ['label' => 'Jalur — paragraf pengantar', 'group' => 'Pembelajaran'],
        'pembelajaran_jalur' => ['label' => 'Jalur — kartu penjelasan', 'group' => 'Pembelajaran'],
        'pembelajaran_kajian_eyebrow' => ['label' => 'Kajian — label kecil', 'group' => 'Pembelajaran'],
        'pembelajaran_kajian_heading' => ['label' => 'Kajian — judul bagian', 'group' => 'Pembelajaran'],
        'pembelajaran_kajian_desc' => ['label' => 'Kajian — deskripsi', 'group' => 'Pembelajaran'],
        'pembelajaran_kajian' => ['label' => 'Kajian — bidang dan kitab', 'group' => 'Pembelajaran'],
        'pembelajaran_jenjang_eyebrow' => ['label' => 'Jenjang — label kecil', 'group' => 'Pembelajaran'],
        'pembelajaran_jenjang_heading' => ['label' => 'Jenjang — judul bagian', 'group' => 'Pembelajaran'],
        'pembelajaran_jenjang_desc' => ['label' => 'Jenjang — deskripsi', 'group' => 'Pembelajaran'],
        'pembelajaran_diniyah_title' => ['label' => 'Diniyah — judul jalur', 'group' => 'Pembelajaran'],
        'pembelajaran_diniyah_note' => ['label' => 'Diniyah — catatan', 'group' => 'Pembelajaran'],
        'pembelajaran_diniyah' => ['label' => 'Diniyah — daftar jenjang', 'group' => 'Pembelajaran'],
        'pembelajaran_madrasah_title' => ['label' => 'Madrasah — judul jalur', 'group' => 'Pembelajaran'],
        'pembelajaran_madrasah_note' => ['label' => 'Madrasah — catatan', 'group' => 'Pembelajaran'],
        'pembelajaran_madrasah' => ['label' => 'Madrasah — daftar jenjang', 'group' => 'Pembelajaran'],
        'pembelajaran_metode_eyebrow' => ['label' => 'Metode — label kecil', 'group' => 'Pembelajaran'],
        'pembelajaran_metode_heading' => ['label' => 'Metode — judul bagian', 'group' => 'Pembelajaran'],
        'pembelajaran_metode_desc' => ['label' => 'Metode — deskripsi', 'group' => 'Pembelajaran'],
        'pembelajaran_metode' => ['label' => 'Metode — kartu penjelasan', 'group' => 'Pembelajaran'],
        'pembelajaran_cta_title' => ['label' => 'Ajakan — judul', 'group' => 'Pembelajaran'],
        'pembelajaran_cta_desc' => ['label' => 'Ajakan — deskripsi', 'group' => 'Pembelajaran'],
        'pembelajaran_cta_button' => ['label' => 'Ajakan — teks tombol', 'group' => 'Pembelajaran'],
        'pembelajaran_cta_url' => ['label' => 'Ajakan — tautan tombol', 'group' => 'Pembelajaran'],

        // Halaman Fasilitas (kartu dikelola terpisah, jumlahnya bisa bertambah)
        'fasilitas_heading' => ['label' => 'Judul halaman', 'group' => 'Fasilitas'],
        'fasilitas_desc' => ['label' => 'Deskripsi pembuka', 'group' => 'Fasilitas'],
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

    /**
     * Daftar kartu fasilitas yang sedang aktif.
     * Kartu memakai key fasilitas_{nomor}_title / _desc / _foto sehingga
     * admin bisa menambah atau menghapus kartu kapan saja.
     */
    public static function fasilitasCards(): array
    {
        $all = self::allWithDefaults();

        // Cadangan foto dari data lama (format JSON).
        $legacy = json_decode((string) ($all['fasilitas_photos'] ?? '[]'), true);
        $legacy = is_array($legacy) ? array_values($legacy) : [];

        $max = 0;
        foreach (array_keys($all) as $key) {
            if (preg_match('/^fasilitas_(\d{1,3})_(title|desc|foto)$/', (string) $key, $m)
                && trim((string) $all[$key]) !== '') {
                $max = max($max, (int) $m[1]);
            }
        }

        $cards = [];

        for ($i = 1; $i <= $max; $i++) {
            $title = trim((string) ($all["fasilitas_{$i}_title"] ?? ''));
            $desc = trim((string) ($all["fasilitas_{$i}_desc"] ?? ''));
            $fotoKey = "fasilitas_{$i}_foto";
            $foto = trim((string) ($all[$fotoKey] ?? ''));

            // Foto lama hanya dipakai bila key foto belum pernah disimpan,
            // supaya foto yang sengaja dihapus admin tidak muncul lagi.
            if (! array_key_exists($fotoKey, $all)
                && ($title !== '' || $desc !== '')
                && isset($legacy[$i - 1])) {
                $foto = trim((string) $legacy[$i - 1]);
            }

            if ($title === '' && $desc === '' && $foto === '') {
                continue;
            }

            $cards[] = [
                'index' => $i,
                'title' => $title,
                'desc' => $desc,
                'foto' => $foto,
            ];
        }

        return $cards;
    }

    /** Sub-bagian tampilan admin berdasarkan prefix key. */
    public static function sectionFor(string $key): string
    {
        if (str_starts_with($key, 'visi_page_visi')) {
            return 'Visi';
        }

        if (str_starts_with($key, 'sejarah_doc_note')) {
            return 'Daftar Isi';
        }

        $prefixes = [
            'sejarah_doc_' => 'Hero',
            'sejarah_bab1_' => 'BAB I',
            'sejarah_bab2_' => 'BAB II',
            'sejarah_bab3_' => 'BAB III',
            'sejarah_daftar_' => 'Pendaftaran',
            'sejarah_hero_' => 'Hero',
            'sejarah_budaya_' => 'Budaya',
            'sejarah_kajian_' => 'Kajian',
            'sejarah_motivasi' => 'Motivasi',
            'visi_page_hero_' => 'Hero',
            'visi_page_misi_' => 'Misi',
            'visi_page_nilai_' => 'Nilai',
            'visi_page_bab5_' => 'BAB V',
            'visi_page_motto_' => 'Motto',
            'pembelajaran_hero_' => 'Hero',
            'pembelajaran_jalur' => 'Jalur Belajar',
            'pembelajaran_kajian_' => 'Kajian',
            'pembelajaran_jenjang_' => 'Jenjang',
            'pembelajaran_diniyah_' => 'Jenjang',
            'pembelajaran_madrasah_' => 'Jenjang',
            'pembelajaran_metode_' => 'Metode',
            'pembelajaran_cta_' => 'CTA',
            'fasilitas_' => 'Fasilitas',
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
