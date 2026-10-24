@extends('layouts.app')

@section('title', 'Visi & Misi - Pondok Pesantren Babul Hasanah')

@section('content')

<style>
    .visi-box { 
        position: relative; 
        background: linear-gradient(135deg, var(--bh-green-900), var(--bh-green-600)); 
        color: #fff;
        border-radius: 30px; 
        padding: 64px 50px; 
        text-align: center; 
        overflow: hidden; 
        box-shadow: 0 30px 60px -25px rgba(20,83,45,.6); 
    }
    .visi-box::before, .visi-box::after { 
        content: ""; 
        position: absolute; 
        border-radius: 50%; 
        background: rgba(255,255,255,.07); 
    }
    .visi-box::before { width: 280px; height: 280px; left: -90px; bottom: -110px; }
    .visi-box::after { width: 200px; height: 200px; right: -60px; top: -70px; }
    .visi-box > * { position: relative; }
    .visi-box .chip { 
        display: inline-flex; 
        gap: 10px; 
        align-items: center; 
        background: rgba(255,255,255,.14); 
        padding: 8px 20px;
        border-radius: 999px; 
        font-size: 13px; 
        font-weight: 700; 
        letter-spacing: .16em; 
        text-transform: uppercase; 
        color: #fde68a; 
        margin-bottom: 24px; 
    }
    .visi-box h2 { 
        font-size: clamp(1.4rem, 3vw, 2.2rem); 
        line-height: 1.6; 
        font-weight: 700; 
        max-width: 860px; 
        margin: 0 auto; 
    }

    .misi-list { display: grid; gap: 18px; max-width: 900px; margin: 0 auto; }
    .misi-item { 
        display: flex; 
        gap: 22px; 
        align-items: flex-start; 
        background: #fff; 
        border: 1px solid #e5efe8; 
        border-radius: 20px;
        padding: 24px 28px; 
        box-shadow: 0 10px 30px -14px rgba(20,83,45,.18); 
        transition: .3s; 
    }
    .misi-item:hover { 
        transform: translateX(10px); 
        border-color: var(--bh-green-500); 
        box-shadow: 0 18px 38px -16px rgba(20,83,45,.3); 
    }
    .misi-no { 
        flex: 0 0 52px; 
        height: 52px; 
        border-radius: 16px; 
        display: grid; 
        place-items: center; 
        font-weight: 800; 
        font-size: 1.3rem; 
        color: #fff;
        background: linear-gradient(135deg, var(--bh-green-500), var(--bh-green-600)); 
        box-shadow: 0 10px 18px -8px rgba(16,185,129,.7); 
    }
    .misi-item:nth-child(even) .misi-no { 
        background: linear-gradient(135deg, #d99a2b, var(--bh-gold)); 
        box-shadow: 0 10px 18px -8px rgba(183,121,31,.7); 
    }
    .misi-item h4 { margin: 0 0 6px; font-size: 1.1rem; font-weight: 700; color: var(--bh-green-800); }
    .misi-item p { margin: 0; color: var(--bh-muted); line-height: 1.75; }
    .motto { 
        text-align: center; 
        padding: 56px 30px; 
        border-radius: 26px; 
        border: 2px dashed var(--bh-green-500); 
        background: var(--bh-green-50); 
    }
    .motto h3 { 
        font-size: clamp(1.4rem, 3vw, 2rem); 
        color: var(--bh-green-800); 
        margin: 8px 0 0; 
        font-weight: 800; 
    }
    @media (max-width: 640px) { 
        .visi-box { padding: 44px 24px; } 
        .misi-item { padding: 20px; gap: 16px; } 
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
                <span>Visi &amp; Misi</span>
            </div>
            <br>
            <span class="bh-badge">Arah &amp; Tujuan</span>
            <h1>Visi &amp; <span>Misi</span></h1>
            <p>Landasan cita-cita dan langkah nyata kami dalam membangun generasi unggul, berilmu, dan berakhlak mulia.</p>
        </div>
        <div class="bh-wave">
            <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
                <path d="M0,30 C360,70 1080,-10 1440,30 L1440,60 L0,60 Z" fill="#fff"/>
            </svg>
        </div>
    </section>

    {{-- VISI --}}
    <section class="bh-section">
        <div class="bh-container">
            <div class="visi-box bh-reveal">
                <span class="chip"><i class="fa-solid fa-eye"></i> Visi Kami</span>
                <h2>Membangun Generasi Unggul dalam Penguasaan Kitab Kuning, Berakhlak Mulia, dan Berwawasan Luas.</h2>
            </div>
        </div>
    </section>

    {{-- MISI --}}
    <section class="bh-section alt">
        <div class="bh-container">
            <div class="bh-head bh-reveal">
                <span class="bh-eyebrow">Langkah Nyata</span>
                <h2 class="bh-title">Misi Pondok Pesantren</h2>
                <p class="bh-lead">Untuk mewujudkan visi tersebut, kami menjalankan misi berikut dengan penuh komitmen.</p>
            </div>

            <div class="misi-list">
                <div class="misi-item bh-reveal">
                    <div class="misi-no">1</div>
                    <div>
                        <h4>Pendalaman Ilmu Agama</h4>
                        <p>Menyelenggarakan pendidikan kitab kuning secara berjenjang dan sistematis dengan sanad keilmuan yang jelas.</p>
                    </div>
                </div>

                <div class="misi-item bh-reveal d1">
                    <div class="misi-no">2</div>
                    <div>
                        <h4>Pembinaan Akhlak &amp; Karakter</h4>
                        <p>Menanamkan adab, kedisiplinan, kemandirian, dan keteladanan dalam kehidupan santri sehari-hari.</p>
                    </div>
                </div>

                <div class="misi-item bh-reveal d2">
                    <div class="misi-no">3</div>
                    <div>
                        <h4>Penguatan Wawasan &amp; Keterampilan</h4>
                        <p>Membekali santri dengan wawasan keilmuan, kemampuan berbahasa, dan keterampilan hidup yang relevan dengan zaman.</p>
                    </div>
                </div>

                <div class="misi-item bh-reveal d3">
                    <div class="misi-no">4</div>
                    <div>
                        <h4>Lingkungan Pesantren yang Kondusif</h4>
                        <p>Menciptakan suasana belajar yang aman, nyaman, dan islami untuk mendukung tumbuh kembang santri.</p>
                    </div>
                </div>

                <div class="misi-item bh-reveal d4">
                    <div class="misi-no">5</div>
                    <div>
                        <h4>Pengabdian kepada Masyarakat</h4>
                        <p>Mengembangkan dakwah dan kemaslahatan umat melalui kegiatan sosial, keagamaan, dan pendidikan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- NILAI-NILAI --}}
    <section class="bh-section">
        <div class="bh-container">
            <div class="bh-head bh-reveal">
                <span class="bh-eyebrow">Pegangan Kami</span>
                <h2 class="bh-title">Nilai-Nilai Utama</h2>
            </div>
            <div class="bh-grid c4">
                <div class="bh-card bh-reveal">
                    <div class="bh-icon"><i class="fa-solid fa-book-quran"></i></div>
                    <h3>Religius</h3>
                    <p>Berpegang teguh pada Al-Qur'an, Sunnah, dan tradisi ulama salaf.</p>
                </div>
                <div class="bh-card bh-reveal d1">
                    <div class="bh-icon gold"><i class="fa-solid fa-heart"></i></div>
                    <h3>Ikhlas</h3>
                    <p>Belajar dan mengabdi semata-mata mengharap ridha Allah.</p>
                </div>
                <div class="bh-card bh-reveal d2">
                    <div class="bh-icon"><i class="fa-solid fa-hands-holding-circle"></i></div>
                    <h3>Mandiri</h3>
                    <p>Melatih santri hidup sederhana, disiplin, dan bertanggung jawab.</p>
                </div>
                <div class="bh-card bh-reveal d3">
                    <div class="bh-icon gold"><i class="fa-solid fa-people-group"></i></div>
                    <h3>Ukhuwah</h3>
                    <p>Menjalin persaudaraan yang erat antarsantri dan masyarakat.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- MOTTO --}}
    <section class="bh-section" style="padding-top:0">
        <div class="bh-container">
            <div class="motto bh-reveal">
                <span class="bh-eyebrow">Motto Pesantren</span>
                <h3>“Berilmu, Beramal, Berakhlak Mulia”</h3>
            </div>
        </div>
    </section>

</div>
@endsection