@extends('layouts.app')

@section('title', 'Sejarah - Pondok Pesantren Babul Hasanah')

@section('content')

<style>
    .tl { position: relative; max-width: 960px; margin: 0 auto; }
    .tl::before { content: ""; position: absolute; left: 50%; top: 0; bottom: 0; width: 3px; transform: translateX(-50%);
        background: linear-gradient(var(--bh-green-500), var(--bh-gold)); border-radius: 3px; }
    .tl-item { position: relative; width: 50%; padding: 0 46px 50px 0; }
    .tl-item:nth-child(even) { margin-left: 50%; padding: 0 0 50px 46px; }
    .tl-dot { position: absolute; top: 18px; right: -13px; width: 26px; height: 26px; border-radius: 50%;
        background: #fff; border: 6px solid var(--bh-green-500); box-shadow: 0 0 0 6px rgba(16,185,129,.18); }
    .tl-item:nth-child(even) .tl-dot { right: auto; left: -13px; border-color: var(--bh-gold); box-shadow: 0 0 0 6px rgba(183,121,31,.18); }
    .tl-tag { display: inline-block; font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
        color: var(--bh-gold); background: var(--bh-gold-soft); padding: 5px 14px; border-radius: 999px; margin-bottom: 12px; }
    .quote-box { background: linear-gradient(135deg, var(--bh-green-800), var(--bh-green-600)); color: #fff; border-radius: 26px;
        padding: 50px 44px; position: relative; overflow: hidden; }
    .quote-box i { position: absolute; font-size: 130px; opacity: .1; top: 6px; left: 22px; }
    .quote-box p { position: relative; font-size: 1.3rem; line-height: 1.8; margin: 0 0 16px; font-weight: 500; }
    .quote-box span { position: relative; color: #fde68a; font-weight: 700; }
    @media (max-width: 768px) {
        .tl::before { left: 14px; }
        .tl-item, .tl-item:nth-child(even) { width: 100%; margin-left: 0; padding: 0 0 40px 48px; }
        .tl-dot, .tl-item:nth-child(even) .tl-dot { left: 1px; right: auto; }
    }
</style>

<div class="bh-page">

    {{-- HERO --}}
    <section class="bh-hero">
        <div class="bh-container">
            <div class="bh-crumb"><a href="{{ url('/') }}">Beranda</a> <i class="fa-solid fa-chevron-right" style="font-size:10px"></i> <span>Profil</span> <i class="fa-solid fa-chevron-right" style="font-size:10px"></i> <span>Sejarah</span></div><br>
            <span class="bh-badge">Tentang Pesantren</span>
            <h1>Sejarah <span>Babul Hasanah</span></h1>
            <p>Perjalanan panjang yang dilandasi keikhlasan, perjuangan, dan cita-cita mencetak generasi berilmu serta berakhlak mulia.</p>
        </div>
        <div class="bh-wave"><svg viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0,30 C360,70 1080,-10 1440,30 L1440,60 L0,60 Z" fill="#fff"/></svg></div>
    </section>

    {{-- STATS --}}
    <section class="bh-stats">
        <div class="bh-container">
            <div class="bh-stat-wrap">
                <div class="bh-stat bh-reveal"><b data-count="25" data-suffix="+">0</b><span>Tahun Mengabdi</span></div>
                <div class="bh-stat bh-reveal d1"><b data-count="1000" data-suffix="+">0</b><span>Santri Aktif</span></div>
                <div class="bh-stat bh-reveal d2"><b data-count="50" data-suffix="+">0</b><span>Ustadz &amp; Ustadzah</span></div>
                <div class="bh-stat bh-reveal d3"><b data-count="3000" data-suffix="+">0</b><span>Alumni</span></div>
            </div>
        </div>
    </section>

    {{-- PENGANTAR --}}
    <section class="bh-section">
        <div class="bh-container">
            <div class="bh-grid c2" style="align-items:center; gap:56px">
                <div class="bh-reveal">
                    <span class="bh-eyebrow">Mengenal Lebih Dekat</span>
                    <h2 class="bh-title">Berawal dari Niat Tulus untuk Menyebarkan Ilmu</h2>
                    <p class="bh-lead">Pondok Pesantren Babul Hasanah berdiri dari kepedulian para pendirinya terhadap pendidikan agama masyarakat sekitar. Dari majelis pengajian sederhana, pesantren ini tumbuh menjadi lembaga pendidikan Islam yang menekankan pendalaman kitab kuning, pembinaan akhlak, dan pengabdian kepada umat.</p>
                    <p class="bh-lead">Nama <b>“Babul Hasanah”</b> yang berarti <i>pintu kebaikan</i> menjadi doa dan harapan agar setiap santri yang masuk melaluinya keluar sebagai pembawa kebaikan bagi keluarga, masyarakat, dan bangsa.</p>
                </div>
                <div class="bh-reveal d2">
                    <div class="bh-card" style="background:linear-gradient(135deg,var(--bh-green-50),#fff); text-align:center; padding:50px 34px">
                        <div class="bh-icon" style="margin:0 auto 20px; width:80px; height:80px; font-size:34px"><i class="fa-solid fa-mosque"></i></div>
                        <h3 style="font-size:1.5rem">Babul Hasanah</h3>
                        <p>“Pintu Kebaikan” — tempat menimba ilmu, menempa akhlak, dan menyiapkan generasi Rabbani.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TIMELINE --}}
    <section class="bh-section alt">
        <div class="bh-container">
            <div class="bh-head bh-reveal">
                <span class="bh-eyebrow">Jejak Langkah</span>
                <h2 class="bh-title">Perjalanan Kami dari Masa ke Masa</h2>
            </div>

            <div class="tl">
                <div class="tl-item bh-reveal">
                    <span class="tl-dot"></span>
                    <div class="bh-card">
                        <span class="tl-tag">Awal Mula</span>
                        <h3>Majelis Pengajian Sederhana</h3>
                        <p>Pesantren dirintis oleh pendiri bersama masyarakat dengan menyelenggarakan pengajian kitab di mushalla dan rumah warga.</p>
                    </div>
                </div>
                <div class="tl-item bh-reveal">
                    <span class="tl-dot"></span>
                    <div class="bh-card">
                        <span class="tl-tag">Masa Perintisan</span>
                        <h3>Berdirinya Pondok &amp; Asrama Santri</h3>
                        <p>Seiring bertambahnya santri dari berbagai daerah, dibangun asrama dan ruang belajar permanen sebagai pusat kegiatan pesantren.</p>
                    </div>
                </div>
                <div class="tl-item bh-reveal">
                    <span class="tl-dot"></span>
                    <div class="bh-card">
                        <span class="tl-tag">Masa Pengembangan</span>
                        <h3>Penguatan Kurikulum Kitab Kuning</h3>
                        <p>Pesantren menyusun jenjang pembelajaran yang sistematis, mulai dari nahwu-shorof hingga fiqih, tafsir, dan hadits.</p>
                    </div>
                </div>
                <div class="tl-item bh-reveal">
                    <span class="tl-dot"></span>
                    <div class="bh-card">
                        <span class="tl-tag">Masa Kini</span>
                        <h3>Pesantren yang Terbuka &amp; Modern</h3>
                        <p>Dengan tetap memegang tradisi salaf, pesantren memanfaatkan teknologi informasi untuk layanan pendaftaran, informasi, dan dakwah digital.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- NILAI --}}
    <section class="bh-section">
        <div class="bh-container">
            <div class="quote-box bh-reveal">
                <i class="fa-solid fa-quote-left"></i>
                <p>“Barang siapa menempuh jalan untuk menuntut ilmu, Allah mudahkan baginya jalan menuju surga.”</p>
                <span>— HR. Muslim</span>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bh-section" style="padding-top:0">
        <div class="bh-container">
            <div class="bh-cta bh-reveal">
                <h2>Jadilah Bagian dari Keluarga Besar Babul Hasanah</h2>
                <p>Daftarkan putra-putri Anda dan mulailah perjalanan menuntut ilmu yang penuh berkah.</p>
                <a href="{{ url('/pendaftaran') }}" class="bh-btn">Daftar Online <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
</div>
@endsection