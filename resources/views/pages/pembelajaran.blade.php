@extends('layouts.app')

@section('title', 'Pembelajaran - Pondok Pesantren Babul Hasanah')

@section('content')

<style>
    .kitab { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 16px; }
    .kitab em { 
        font-style: normal; 
        font-size: 12px; 
        font-weight: 600; 
        color: var(--bh-green-800); 
        background: var(--bh-green-100); 
        padding: 5px 12px; 
        border-radius: 999px; 
    }
    .level { 
        position: relative; 
        background: #fff; 
        border-radius: 22px; 
        padding: 34px 26px 28px; 
        border: 1px solid #e5efe8; 
        text-align: center;
        box-shadow: 0 12px 30px -14px rgba(20,83,45,.2); 
        transition: .35s; 
    }
    .level:hover { transform: translateY(-8px); border-color: var(--bh-green-500); }
    .level .no { 
        position: absolute; 
        top: -18px; 
        left: 50%; 
        transform: translateX(-50%); 
        width: 40px; 
        height: 40px; 
        border-radius: 50%; 
        display: grid; 
        place-items: center;
        font-weight: 800; 
        color: #fff; 
        background: linear-gradient(135deg, #d99a2b, var(--bh-gold)); 
        box-shadow: 0 8px 16px -6px rgba(183,121,31,.7); 
    }
    .level h3 { margin: 6px 0 8px; color: var(--bh-green-800); font-weight: 800; }
    .level p { margin: 0; color: var(--bh-muted); line-height: 1.7; font-size: .95rem; }

    .sched { 
        max-width: 860px; 
        margin: 0 auto; 
        background: #fff; 
        border-radius: 24px; 
        overflow: hidden; 
        border: 1px solid #e5efe8; 
        box-shadow: 0 20px 44px -22px rgba(20,83,45,.3); 
    }
    .sched-row { 
        display: flex; 
        align-items: center; 
        gap: 20px; 
        padding: 18px 26px; 
        border-bottom: 1px solid #eef5f0; 
        transition: background .25s; 
    }
    .sched-row:last-child { border-bottom: 0; }
    .sched-row:hover { background: var(--bh-green-50); }
    .sched-time { flex: 0 0 130px; font-weight: 800; color: var(--bh-green-600); }
    .sched-dot { 
        flex: 0 0 12px; 
        height: 12px; 
        border-radius: 50%; 
        background: var(--bh-gold); 
        box-shadow: 0 0 0 5px var(--bh-gold-soft); 
    }
    .sched-act b { display: block; color: var(--bh-ink); }
    .sched-act small { color: var(--bh-muted); }

    @media (max-width: 640px) { 
        .sched-time { flex-basis: 96px; font-size: .9rem; } 
        .sched-row { padding: 16px; gap: 12px; } 
    }
</style>

<div class="bh-page">

    {{-- HERO --}}
    <section class="bh-hero">
        <div class="bh-container">
            <div class="bh-crumb">
                <a href="{{ url('/') }}">Beranda</a> 
                <i class="fa-solid fa-chevron-right" style="font-size:10px"></i> 
                <span>Profil</span> 
                <i class="fa-solid fa-chevron-right" style="font-size:10px"></i> 
                <span>Pembelajaran</span>
            </div>
            <br>
            <span class="bh-badge">Sistem Pendidikan</span>
            <h1>Pembelajaran <span>Kitab Kuning</span></h1>
            <p>Kurikulum terpadu yang memadukan tradisi keilmuan salaf, pembinaan akhlak, dan kegiatan penunjang untuk membentuk santri berilmu dan beradab.</p>
        </div>
        <div class="bh-wave">
            <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
                <path d="M0,30 C360,70 1080,-10 1440,30 L1440,60 L0,60 Z" fill="#fff"/>
            </svg>
        </div>
    </section>

    {{-- MATA PELAJARAN --}}
    <section class="bh-section">
        <div class="bh-container">
            <div class="bh-head bh-reveal">
                <span class="bh-eyebrow">Bidang Kajian</span>
                <h2 class="bh-title">Ilmu yang Dipelajari Santri</h2>
                <p class="bh-lead">Seluruh pelajaran disampaikan melalui kitab-kitab klasik yang diajarkan secara bertahap sesuai tingkat kemampuan santri.</p>
            </div>

            <div class="bh-grid c3">
                <div class="bh-card bh-reveal">
                    <div class="bh-icon"><i class="fa-solid fa-spell-check"></i></div>
                    <h3>Nahwu &amp; Shorof</h3>
                    <p>Fondasi membaca kitab gundul: tata bahasa dan perubahan kata dalam bahasa Arab.</p>
                    <div class="kitab">
                        <em>Jurumiyah</em><em>Imrithi</em><em>Alfiyah</em><em>Amtsilati Tasrifiyah</em>
                    </div>
                </div>

                <div class="bh-card bh-reveal d1">
                    <div class="bh-icon gold"><i class="fa-solid fa-scale-balanced"></i></div>
                    <h3>Fiqih</h3>
                    <p>Hukum ibadah dan muamalah menurut mazhab Syafi'i secara mendalam dan praktis.</p>
                    <div class="kitab">
                        <em>Safinatun Najah</em><em>Fathul Qorib</em><em>Fathul Mu'in</em>
                    </div>
                </div>

                <div class="bh-card bh-reveal d2">
                    <div class="bh-icon"><i class="fa-solid fa-star-and-crescent"></i></div>
                    <h3>Tauhid / Aqidah</h3>
                    <p>Memperkokoh keyakinan sesuai ajaran Ahlussunnah wal Jama'ah.</p>
                    <div class="kitab">
                        <em>Aqidatul Awam</em><em>Kifayatul Awam</em><em>Tijan Ad-Darari</em>
                    </div>
                </div>

                <div class="bh-card bh-reveal">
                    <div class="bh-icon gold"><i class="fa-solid fa-heart-pulse"></i></div>
                    <h3>Akhlak &amp; Tasawuf</h3>
                    <p>Menata adab, hati, dan perilaku sebagai penuntut ilmu.</p>
                    <div class="kitab">
                        <em>Ta'lim Muta'allim</em><em>Akhlaq lil Banin</em><em>Bidayatul Hidayah</em>
                    </div>
                </div>

                <div class="bh-card bh-reveal d1">
                    <div class="bh-icon"><i class="fa-solid fa-scroll"></i></div>
                    <h3>Hadits &amp; Tafsir</h3>
                    <p>Memahami sabda Nabi dan kandungan Al-Qur'an melalui kajian para ulama.</p>
                    <div class="kitab">
                        <em>Arba'in Nawawi</em><em>Riyadhus Shalihin</em><em>Tafsir Jalalain</em>
                    </div>
                </div>

                <div class="bh-card bh-reveal d2">
                    <div class="bh-icon gold"><i class="fa-solid fa-book-quran"></i></div>
                    <h3>Al-Qur'an</h3>
                    <p>Tahsin, tajwid, dan program tahfidz bagi santri yang berminat.</p>
                    <div class="kitab">
                        <em>Tahsin</em><em>Tajwid</em><em>Tahfidz</em>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- METODE --}}
    <section class="bh-section alt">
        <div class="bh-container">
            <div class="bh-head bh-reveal">
                <span class="bh-eyebrow">Cara Belajar</span>
                <h2 class="bh-title">Metode Pembelajaran</h2>
            </div>
            <div class="bh-grid c4">
                <div class="bh-card bh-reveal">
                    <div class="bh-icon"><i class="fa-solid fa-user-group"></i></div>
                    <h3>Sorogan</h3>
                    <p>Santri membaca kitab satu per satu di hadapan ustadz untuk dikoreksi langsung.</p>
                </div>
                <div class="bh-card bh-reveal d1">
                    <div class="bh-icon gold"><i class="fa-solid fa-chalkboard-user"></i></div>
                    <h3>Bandongan</h3>
                    <p>Ustadz membacakan dan menjelaskan kitab, santri menyimak dan memberi makna.</p>
                </div>
                <div class="bh-card bh-reveal d2">
                    <div class="bh-icon"><i class="fa-solid fa-brain"></i></div>
                    <h3>Muhafazhah</h3>
                    <p>Menghafal nazham dan matan kitab untuk memperkuat penguasaan ilmu.</p>
                </div>
                <div class="bh-card bh-reveal d3">
                    <div class="bh-icon gold"><i class="fa-solid fa-comments"></i></div>
                    <h3>Bahtsul Masail</h3>
                    <p>Diskusi kajian hukum untuk melatih nalar dan kemampuan merujuk kitab.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- JENJANG --}}
    <section class="bh-section">
        <div class="bh-container">
            <div class="bh-head bh-reveal">
                <span class="bh-eyebrow">Tahapan Belajar</span>
                <h2 class="bh-title">Jenjang Pendidikan</h2>
            </div>
            <div class="bh-grid c3" style="margin-top:30px">
                <div class="level bh-reveal">
                    <div class="no">1</div>
                    <h3>Ibtidaiyah</h3>
                    <p>Pengenalan dasar membaca Al-Qur'an, ibadah, akhlak, dan pengantar bahasa Arab.</p>
                </div>
                <div class="level bh-reveal d1">
                    <div class="no">2</div>
                    <h3>Wustho</h3>
                    <p>Pendalaman nahwu-shorof, fiqih, dan tauhid dengan kitab tingkat menengah.</p>
                </div>
                <div class="level bh-reveal d2">
                    <div class="no">3</div>
                    <h3>'Ulya</h3>
                    <p>Kajian lanjutan fiqih, tafsir, hadits, serta pelatihan bahtsul masail.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- JADWAL --}}
    <section class="bh-section alt">
        <div class="bh-container">
            <div class="bh-head bh-reveal">
                <span class="bh-eyebrow">Rutinitas Santri</span>
                <h2 class="bh-title">Jadwal Harian Santri</h2>
            </div>
            <div class="sched bh-reveal">
                <div class="sched-row">
                    <div class="sched-time">04.00 – 05.30</div>
                    <span class="sched-dot"></span>
                    <div class="sched-act">
                        <b>Qiyamul Lail &amp; Shalat Subuh</b>
                        <small>Dilanjutkan wirid dan mengaji Al-Qur'an</small>
                    </div>
                </div>
                <div class="sched-row">
                    <div class="sched-time">06.00 – 07.00</div>
                    <span class="sched-dot"></span>
                    <div class="sched-act">
                        <b>Pengajian Kitab Pagi</b>
                        <small>Bandongan bersama ustadz</small>
                    </div>
                </div>
                <div class="sched-row">
                    <div class="sched-time">07.30 – 12.00</div>
                    <span class="sched-dot"></span>
                    <div class="sched-act">
                        <b>Kegiatan Belajar Formal / Madrasah</b>
                        <small>Sesuai jenjang dan kelas masing-masing</small>
                    </div>
                </div>
                <div class="sched-row">
                    <div class="sched-time">13.00 – 15.00</div>
                    <span class="sched-dot"></span>
                    <div class="sched-act">
                        <b>Shalat Dzuhur &amp; Istirahat</b>
                        <small>Makan siang dan muraja'ah</small>
                    </div>
                </div>
                <div class="sched-row">
                    <div class="sched-time">16.00 – 17.30</div>
                    <span class="sched-dot"></span>
                    <div class="sched-act">
                        <b>Ekstrakurikuler &amp; Pembinaan Bahasa</b>
                        <small>Muhadharah, bahasa Arab/Inggris, olahraga</small>
                    </div>
                </div>
                <div class="sched-row">
                    <div class="sched-time">18.30 – 21.00</div>
                    <span class="sched-dot"></span>
                    <div class="sched-act">
                        <b>Pengajian Kitab Malam</b>
                        <small>Sorogan, hafalan, dan diskusi</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bh-section">
        <div class="bh-container">
            <div class="bh-cta bh-reveal">
                <h2>Siap Belajar Bersama Kami?</h2>
                <p>Mulai langkah pertama menuju ilmu yang bermanfaat dan akhlak yang mulia.</p>
                <a href="{{ url('/pendaftaran') }}" class="bh-btn">Daftar Online <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

</div>
@endsection