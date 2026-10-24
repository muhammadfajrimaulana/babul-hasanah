@extends('layouts.app')

@section('title', 'Struktur Organisasi - Pondok Pesantren Babul Hasanah')

@section('content')

<style>
    .org { display: flex; flex-direction: column; align-items: center; gap: 0; }
    .org-row { display: flex; justify-content: center; gap: 26px; flex-wrap: wrap; position: relative; }
    .org-line { width: 3px; height: 40px; background: linear-gradient(var(--bh-green-500), var(--bh-gold)); border-radius: 3px; }
    .org-hbar { height: 3px; width: min(820px, 88%); background: linear-gradient(90deg, var(--bh-green-500), var(--bh-gold)); border-radius: 3px; }
    .person { width: 240px; background: #fff; border-radius: 22px; text-align: center; padding: 28px 20px 24px; border: 1px solid #e5efe8;
        box-shadow: 0 12px 30px -14px rgba(20,83,45,.22); transition: .35s; position: relative; }
    .person:hover { transform: translateY(-8px); border-color: var(--bh-green-500); box-shadow: 0 24px 44px -16px rgba(20,83,45,.35); }
    .person .avatar { width: 96px; height: 96px; border-radius: 50%; margin: 0 auto 16px; display: grid; place-items: center; font-size: 38px; color: #fff;
        background: linear-gradient(135deg, var(--bh-green-500), var(--bh-green-800)); border: 5px solid var(--bh-green-100); overflow: hidden; }
    .person .avatar img { width: 100%; height: 100%; object-fit: cover; }
    .person.gold .avatar { background: linear-gradient(135deg, #d99a2b, var(--bh-gold)); border-color: var(--bh-gold-soft); }
    .person h4 { margin: 0 0 6px; font-size: 1.02rem; font-weight: 700; color: var(--bh-ink); }
    .person span { display: inline-block; font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--bh-green-600);
        background: var(--bh-green-50); padding: 5px 14px; border-radius: 999px; }
    .person.top { width: 290px; background: linear-gradient(135deg, var(--bh-green-900), var(--bh-green-600)); border: 0; }
    .person.top h4 { color: #fff; font-size: 1.15rem; }
    .person.top span { background: rgba(255,255,255,.16); color: #fde68a; }
    .person.top .avatar { border-color: rgba(255,255,255,.3); background: linear-gradient(135deg, #d99a2b, var(--bh-gold)); }
    .dept { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 22px; }
    .dept .bh-card { text-align: center; padding: 26px 20px; }
    .dept .bh-icon { margin: 0 auto 14px; }
    @media (max-width: 640px) { 
        .org-hbar { display: none; } 
        .person, .person.top { width: 100%; max-width: 320px; } 
        .org-row { gap: 18px; } 
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
                <span>Struktur Organisasi</span>
            </div>
            <br>
            <span class="bh-badge">Kepengurusan</span>
            <h1>Struktur <span>Organisasi</span></h1>
            <p>Para pengasuh dan pengurus yang berkhidmat menjaga, membimbing, dan mengelola pesantren dengan penuh amanah.</p>
        </div>
        <div class="bh-wave">
            <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
                <path d="M0,30 C360,70 1080,-10 1440,30 L1440,60 L0,60 Z" fill="#fff"/>
            </svg>
        </div>
    </section>

    {{-- BAGAN ORGANISASI --}}
    <section class="bh-section">
        <div class="bh-container">
            <div class="bh-head bh-reveal">
                <span class="bh-eyebrow">Bagan Kepengurusan</span>
                <h2 class="bh-title">Susunan Pengurus Pesantren</h2>
            </div>

            <div class="org">
                <!-- Pengasuh -->
                <div class="person top bh-reveal">
                    <div class="avatar"><i class="fa-solid fa-user-tie"></i></div>
                    <h4>KH. Nama Pengasuh</h4>
                    <span>Pengasuh / Pimpinan</span>
                </div>

                <div class="org-line"></div>

                <!-- Mudir -->
                <div class="person gold bh-reveal d1">
                    <div class="avatar"><i class="fa-solid fa-user"></i></div>
                    <h4>Ust. Nama Mudir</h4>
                    <span>Mudir / Kepala Pondok</span>
                </div>

                <div class="org-line"></div>
                <div class="org-hbar"></div>
                <div class="org-line" style="height:26px"></div>

                <!-- Barisan Pengurus Utama -->
                <div class="org-row">
                    <div class="person bh-reveal">
                        <div class="avatar"><i class="fa-solid fa-pen-nib"></i></div>
                        <h4>Ust. Nama Sekretaris</h4>
                        <span>Sekretaris</span>
                    </div>
                    <div class="person bh-reveal d1">
                        <div class="avatar"><i class="fa-solid fa-wallet"></i></div>
                        <h4>Ust. Nama Bendahara</h4>
                        <span>Bendahara</span>
                    </div>
                    <div class="person bh-reveal d2">
                        <div class="avatar"><i class="fa-solid fa-user-graduate"></i></div>
                        <h4>Ust. Nama Pendidikan</h4>
                        <span>Kepala Pendidikan</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- BIDANG & SEKSI --}}
    <section class="bh-section alt">
        <div class="bh-container">
            <div class="bh-head bh-reveal">
                <span class="bh-eyebrow">Bidang &amp; Seksi</span>
                <h2 class="bh-title">Bidang Pelayanan Pesantren</h2>
                <p class="bh-lead">Setiap bidang memiliki peran penting dalam menunjang kegiatan belajar dan kehidupan santri.</p>
            </div>

            <div class="dept">
                <div class="bh-card bh-reveal">
                    <div class="bh-icon"><i class="fa-solid fa-book-open"></i></div>
                    <h3>Pendidikan</h3>
                    <p>Kurikulum, jadwal, dan evaluasi pembelajaran.</p>
                </div>
                <div class="bh-card bh-reveal d1">
                    <div class="bh-icon gold"><i class="fa-solid fa-shield-heart"></i></div>
                    <h3>Keamanan &amp; Disiplin</h3>
                    <p>Ketertiban dan pembinaan kedisiplinan santri.</p>
                </div>
                <div class="bh-card bh-reveal d2">
                    <div class="bh-icon"><i class="fa-solid fa-language"></i></div>
                    <h3>Bahasa</h3>
                    <p>Pengembangan bahasa Arab dan Inggris.</p>
                </div>
                <div class="bh-card bh-reveal d3">
                    <div class="bh-icon gold"><i class="fa-solid fa-house-chimney"></i></div>
                    <h3>Sarana &amp; Kebersihan</h3>
                    <p>Perawatan fasilitas dan lingkungan pondok.</p>
                </div>
                <div class="bh-card bh-reveal d4">
                    <div class="bh-icon"><i class="fa-solid fa-kit-medical"></i></div>
                    <h3>Kesehatan</h3>
                    <p>Pelayanan kesehatan dan pertolongan pertama.</p>
                </div>
                <div class="bh-card bh-reveal">
                    <div class="bh-icon gold"><i class="fa-solid fa-bullhorn"></i></div>
                    <h3>Humas &amp; Media</h3>
                    <p>Informasi, publikasi, dan hubungan masyarakat.</p>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection