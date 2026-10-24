@extends('layouts.app')

@section('title', 'Kontak Pondok Pesantren Babul Hasanah')

@section('content')

@php
    /*
     * DATA KONTAK - sesuaikan dengan data resmi pesantren.
     * Nomor WhatsApp memakai format internasional tanpa tanda + dan tanpa 0 di depan.
     */
    $wa      = '6281234567890';
    $telp    = '081234567890';
    $telpTampil = '0812-3456-7890';
    $email   = 'info@babulhasanah.sch.id';
    $alamat  = 'Jl. Lintas Pinarik-Papaso Km. 14, Desa Manggis, Kec. Batang Lubu Sutam, Kabupaten Padang Lawas, Sumatera Utara 22763';
    $peta    = 'https://www.google.com/maps?q=' . urlencode('Pondok Pesantren Babul Hasanah, Batang Lubu Sutam, Padang Lawas') . '&output=embed';
    $petaLink = 'https://www.google.com/maps/search/?api=1&query=' . urlencode('Pondok Pesantren Babul Hasanah, Batang Lubu Sutam, Padang Lawas');

    $faq = [
        ['Kapan pendaftaran santri baru dibuka?', 'Pendaftaran Tahun Ajaran 2026/2027 sudah dibuka. Anda bisa mendaftar langsung lewat halaman Pendaftaran Online, atau bertanya dulu kepada panitia melalui WhatsApp.'],
        ['Jenjang apa saja yang tersedia?', 'Tersedia MDTA, PPS Wustha, MTs, MA, Diniyah Formal Wustha dan Ulya, serta Ma’had ‘Aly. Rincian tiap jenjang ada di menu Jenjang.'],
        ['Apakah calon wali santri boleh berkunjung ke pesantren?', 'Boleh. Sebaiknya hubungi kami terlebih dahulu agar jadwal kunjungan bisa diatur dan Anda dapat ditemani oleh pengurus.'],
        ['Bagaimana mengetahui rincian biaya pendidikan?', 'Rincian biaya dapat ditanyakan langsung kepada panitia melalui WhatsApp atau datang ke sekretariat pesantren pada jam layanan.'],
    ];
@endphp

<style>
    /* ===== Halaman Kontak (memakai variabel warna dari layouts/app) ===== */
    .kt-hero {
        position: relative; overflow: hidden; background: var(--cream); text-align: center;
        padding: 64px 0 112px; border-radius: 0 0 36px 36px;
    }
    .kt-hero::before, .kt-hero::after {
        content: ""; position: absolute; border-radius: 50%; pointer-events: none;
    }
    .kt-hero::before { width: 360px; height: 360px; left: -120px; top: -140px; background: radial-gradient(circle, rgba(18, 184, 134, .16), transparent 70%); }
    .kt-hero::after { width: 320px; height: 320px; right: -100px; bottom: -150px; background: radial-gradient(circle, rgba(183, 121, 31, .16), transparent 70%); }
    .kt-hero .site-container { position: relative; z-index: 1; }
    .kt-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--gold); }
    .kt-title { font-size: clamp(30px, 4.4vw, 46px); font-weight: 700; color: var(--green); line-height: 1.2; margin-top: 8px; }
    .kt-title span { color: var(--emerald-d); }
    .kt-sub { font-size: 14.5px; color: var(--muted); max-width: 560px; margin: 12px auto 0; line-height: 1.75; }

    /* Kartu info (menumpuk ke hero) */
    .kt-info { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-top: -64px; position: relative; z-index: 2; }
    .kt-box {
        background: #fff; border: 1px solid var(--line); border-radius: 22px; padding: 24px 22px;
        box-shadow: 0 12px 34px rgba(23, 64, 47, .08); display: block;
        transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
    }
    a.kt-box:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--emerald); }
    .kt-ic {
        width: 48px; height: 48px; border-radius: 14px; background: #e8f6ef; color: var(--emerald-d);
        display: grid; place-items: center; font-size: 18px; margin-bottom: 16px; transition: all .25s ease;
    }
    a.kt-box:hover .kt-ic { background: var(--emerald); color: #fff; }
    .kt-box h3 { font-size: 11px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--gold); }
    .kt-box p { font-size: 14px; font-weight: 500; color: var(--green); line-height: 1.6; margin-top: 6px; word-break: break-word; }
    .kt-box small { display: block; font-size: 12px; color: var(--muted); margin-top: 4px; line-height: 1.6; }

    /* Form + peta */
    .kt-main { display: grid; grid-template-columns: 1.05fr 1fr; gap: 28px; margin-top: 56px; align-items: start; }
    .kt-card { background: #fff; border: 1px solid var(--line); border-radius: 26px; padding: 32px; }
    .kt-card-title { font-size: 21px; font-weight: 600; color: var(--green); margin-top: 6px; }
    .kt-card-sub { font-size: 13px; color: var(--muted); line-height: 1.7; margin: 8px 0 22px; }

    .kt-field { margin-bottom: 16px; }
    .kt-field label { display: block; font-size: 12px; font-weight: 600; color: var(--green); margin-bottom: 7px; }
    .kt-field label em { font-style: normal; font-weight: 400; color: var(--muted); }
    .kt-field input, .kt-field select, .kt-field textarea {
        width: 100%; font-family: inherit; font-size: 14px; color: var(--ink); background: var(--paper);
        border: 1px solid var(--line); border-radius: 12px; padding: 12px 14px; outline: none;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .kt-field textarea { resize: vertical; min-height: 120px; line-height: 1.6; }
    .kt-field input:focus, .kt-field select:focus, .kt-field textarea:focus {
        border-color: var(--emerald); background: #fff; box-shadow: 0 0 0 4px rgba(18, 184, 134, .12);
    }
    .kt-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    .kt-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 6px; }
    .kt-actions .site-btn { flex: 1; min-width: 180px; border: 0; cursor: pointer; font-family: inherit; }
    .kt-wa { background: #25d366; box-shadow: 0 8px 20px rgba(37, 211, 102, .28); }
    .kt-wa:hover { background: #1ebe5a; }
    .kt-mail { background: #fff; color: var(--green); border: 1px solid var(--line) !important; box-shadow: none; }
    .kt-mail:hover { background: var(--green); color: #fff; border-color: var(--green) !important; }
    .site-btn[aria-disabled="true"] { opacity: .45; pointer-events: none; }
    .kt-note { font-size: 11.5px; color: var(--muted); margin-top: 14px; line-height: 1.6; }
    .kt-note i { color: var(--emerald-d); margin-right: 6px; }

    /* Peta */
    .kt-side { display: flex; flex-direction: column; gap: 22px; }
    .kt-map { border-radius: 26px; overflow: hidden; border: 1px solid var(--line); background: #fff; }
    .kt-map iframe { width: 100%; height: 320px; border: 0; display: block; }
    .kt-map-foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 20px; flex-wrap: wrap; }
    .kt-map-foot span { font-size: 12.5px; color: var(--muted); display: flex; align-items: center; gap: 8px; }
    .kt-map-foot span i { color: var(--emerald-d); }
    .kt-map-foot a { font-size: 13px; font-weight: 600; color: var(--emerald-d); transition: color .2s ease; }
    .kt-map-foot a:hover { color: var(--green); }

    .kt-social { background: var(--green); color: #cfe0d6; border-radius: 26px; padding: 26px; box-shadow: var(--shadow-lg); }
    .kt-social h4 { color: #fff; font-size: 16px; font-weight: 600; }
    .kt-social p { font-size: 12.5px; line-height: 1.7; margin: 6px 0 16px; }
    .kt-social-list { display: flex; gap: 10px; flex-wrap: wrap; }
    .kt-social-list a {
        width: 42px; height: 42px; border: 1px solid rgba(255, 255, 255, .2); border-radius: 50%;
        display: grid; place-items: center; color: #fff; font-size: 16px; transition: all .25s ease;
    }
    .kt-social-list a:hover { background: var(--emerald); border-color: var(--emerald); transform: translateY(-3px); }

    /* FAQ */
    .kt-sec-head { text-align: center; margin: 72px 0 28px; }
    .kt-sec-title { font-size: 24px; font-weight: 600; color: var(--green); margin-top: 6px; }
    .kt-faq { max-width: 780px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }
    .kt-q { background: #fff; border: 1px solid var(--line); border-radius: 18px; overflow: hidden; transition: border-color .2s ease, box-shadow .2s ease; }
    .kt-q.open { border-color: var(--emerald); box-shadow: 0 10px 28px rgba(23, 64, 47, .08); }
    .kt-q button {
        width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 16px; text-align: left;
        background: none; border: 0; cursor: pointer; font-family: inherit; font-size: 14.5px; font-weight: 600;
        color: var(--green); padding: 18px 22px;
    }
    .kt-q button i { color: var(--emerald-d); font-size: 12px; transition: transform .25s ease; flex: none; }
    .kt-q.open button i { transform: rotate(180deg); }
    .kt-a { padding: 0 22px 20px; font-size: 13.5px; color: var(--muted); line-height: 1.8; }

    /* CTA */
    .kt-cta {
        margin: 72px 0 0; background: var(--green); border-radius: 30px; padding: 44px 36px; color: #cfe0d6;
        display: flex; align-items: center; justify-content: space-between; gap: 28px; flex-wrap: wrap;
        position: relative; overflow: hidden; box-shadow: var(--shadow-lg);
    }
    .kt-cta::after {
        content: ""; position: absolute; width: 300px; height: 300px; border-radius: 50%; right: -80px; top: -120px;
        background: radial-gradient(circle, rgba(47, 224, 160, .22), transparent 70%); pointer-events: none;
    }
    .kt-cta > * { position: relative; z-index: 1; }
    .kt-cta h3 { color: #fff; font-size: 24px; font-weight: 600; line-height: 1.3; }
    .kt-cta p { font-size: 13.5px; line-height: 1.7; margin-top: 8px; max-width: 480px; }

    .kt-wrap { padding-bottom: 72px; }

    @media (max-width: 1024px) {
        .kt-info { grid-template-columns: repeat(2, 1fr); }
        .kt-main { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .kt-hero { padding: 44px 0 96px; border-radius: 0 0 28px 28px; }
        .kt-info { grid-template-columns: 1fr; margin-top: -56px; gap: 14px; }
        .kt-card { padding: 24px 20px; border-radius: 22px; }
        .kt-row { grid-template-columns: 1fr; gap: 0; }
        .kt-actions .site-btn { min-width: 100%; }
        .kt-map iframe { height: 260px; }
        .kt-cta { padding: 32px 24px; border-radius: 24px; }
        .kt-cta .site-btn { width: 100%; }
        .kt-sec-head { margin-top: 56px; }
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('kontakForm', (wa, email) => ({
            nama: '', hp: '', topik: 'Pendaftaran Santri Baru', pesan: '',

            get siap() { return this.nama.trim() !== '' && this.pesan.trim() !== ''; },
            get teks() {
                let t = 'Assalamu’alaikum, saya ' + this.nama.trim() + '.\n'
                      + 'Topik: ' + this.topik + '\n\n' + this.pesan.trim();
                if (this.hp.trim()) t += '\n\nNo. HP saya: ' + this.hp.trim();
                return t;
            },
            get linkWa() { return this.siap ? 'https://wa.me/' + wa + '?text=' + encodeURIComponent(this.teks) : '#'; },
            get linkMail() {
                return this.siap
                    ? 'mailto:' + email + '?subject=' + encodeURIComponent(this.topik + ' - ' + this.nama.trim()) + '&body=' + encodeURIComponent(this.teks)
                    : '#';
            },
        }));
    });
</script>

{{-- ===================== HERO ===================== --}}
<section class="kt-hero" id="kontak">
    <div class="site-container">
        <span class="kt-eyebrow">Hubungi Kami</span>
        <h1 class="kt-title">Kami Siap <span>Membantu Anda</span></h1>
        <p class="kt-sub">Punya pertanyaan seputar pendaftaran, jenjang pendidikan, atau ingin berkunjung ke Pondok Pesantren Babul Hasanah? Silakan hubungi kami.</p>
    </div>
</section>

<div class="site-container kt-wrap">

    {{-- ===================== KARTU INFO ===================== --}}
    <div class="kt-info">
        <a href="{{ $petaLink }}" target="_blank" rel="noopener noreferrer" class="kt-box">
            <span class="kt-ic"><i class="fa-solid fa-location-dot"></i></span>
            <h3>Alamat</h3>
            <p>Jl. Lintas Pinarik-Papaso Km. 14, Desa Manggis</p>
            <small>Kec. Batang Lubu Sutam, Kab. Padang Lawas, Sumatera Utara 22763</small>
        </a>
        <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer" class="kt-box">
            <span class="kt-ic"><i class="fa-brands fa-whatsapp"></i></span>
            <h3>Telepon / WhatsApp</h3>
            <p>{{ $telpTampil }}</p>
            <small>Klik untuk chat langsung dengan panitia</small>
        </a>
        <a href="mailto:{{ $email }}" class="kt-box">
            <span class="kt-ic"><i class="fa-solid fa-envelope"></i></span>
            <h3>Email</h3>
            <p>{{ $email }}</p>
            <small>Balasan diberikan pada jam layanan</small>
        </a>
        <div class="kt-box">
            <span class="kt-ic"><i class="fa-regular fa-clock"></i></span>
            <h3>Jam Layanan</h3>
            <p>Senin - Sabtu</p>
            <small>08.00 - 16.00 WIB (Jumat sampai 11.30 WIB)</small>
        </div>
    </div>

    {{-- ===================== FORM + PETA ===================== --}}
    <div class="kt-main">

        {{-- Form pesan --}}
        <div class="kt-card" x-data="kontakForm('{{ $wa }}', '{{ $email }}')">
            <span class="kt-eyebrow">Kirim Pesan</span>
            <h2 class="kt-card-title">Tulis Pertanyaan Anda</h2>
            <p class="kt-card-sub">Isi formulir singkat ini, lalu pilih kirim lewat WhatsApp atau email. Pesan Anda akan tersusun otomatis.</p>

            <div class="kt-row">
                <div class="kt-field">
                    <label for="kt-nama">Nama lengkap</label>
                    <input id="kt-nama" type="text" x-model="nama" placeholder="Nama Anda" autocomplete="name">
                </div>
                <div class="kt-field">
                    <label for="kt-hp">No. HP <em>(opsional)</em></label>
                    <input id="kt-hp" type="tel" x-model="hp" placeholder="08xx-xxxx-xxxx" autocomplete="tel">
                </div>
            </div>

            <div class="kt-field">
                <label for="kt-topik">Topik</label>
                <select id="kt-topik" x-model="topik">
                    <option>Pendaftaran Santri Baru</option>
                    <option>Informasi Jenjang Pendidikan</option>
                    <option>Biaya dan Fasilitas</option>
                    <option>Kunjungan ke Pesantren</option>
                    <option>Lainnya</option>
                </select>
            </div>

            <div class="kt-field">
                <label for="kt-pesan">Pesan</label>
                <textarea id="kt-pesan" x-model="pesan" placeholder="Tulis pertanyaan atau keperluan Anda di sini..."></textarea>
            </div>

            <div class="kt-actions">
                <a :href="linkWa" target="_blank" rel="noopener noreferrer" class="site-btn kt-wa" :aria-disabled="!siap">
                    <i class="fa-brands fa-whatsapp"></i> Kirim via WhatsApp
                </a>
                <a :href="linkMail" class="site-btn kt-mail" :aria-disabled="!siap">
                    <i class="fa-solid fa-envelope"></i> Kirim via Email
                </a>
            </div>
            <p class="kt-note"><i class="fa-solid fa-circle-info"></i>Tombol aktif setelah nama dan pesan terisi. Pesan akan dibuka di aplikasi WhatsApp atau email Anda.</p>
        </div>

        {{-- Peta + sosial media --}}
        <div class="kt-side">
            <div class="kt-map">
                <iframe src="{{ $peta }}" title="Peta lokasi Pondok Pesantren Babul Hasanah" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                <div class="kt-map-foot">
                    <span><i class="fa-solid fa-location-dot"></i> Padang Lawas, Sumatera Utara</span>
                    <a href="{{ $petaLink }}" target="_blank" rel="noopener noreferrer">Buka di Google Maps <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:11px;margin-left:4px"></i></a>
                </div>
            </div>

            <div class="kt-social">
                <h4>Ikuti Kami</h4>
                <p>Dapatkan kabar kegiatan dan dokumentasi terbaru pesantren melalui media sosial kami.</p>
                <div class="kt-social-list">
                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== FAQ ===================== --}}
    <div class="kt-sec-head">
        <span class="kt-eyebrow">Sering Ditanyakan</span>
        <h2 class="kt-sec-title">Pertanyaan Umum</h2>
    </div>

    <div class="kt-faq" x-data="{ buka: 0 }">
        @foreach ($faq as $i => $f)
            <div class="kt-q" :class="{ 'open': buka === {{ $i }} }">
                <button type="button" @click="buka = (buka === {{ $i }} ? null : {{ $i }})" :aria-expanded="buka === {{ $i }}">
                    <span>{{ $f[0] }}</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="kt-a" x-show="buka === {{ $i }}" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0">{{ $f[1] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ===================== CTA ===================== --}}
    <div class="kt-cta">
        <div>
            <h3>Siap Bergabung Menjadi Santri Babul Hasanah?</h3>
            <p>Daftarkan putra-putri Anda secara online. Prosesnya mudah dan panitia siap membantu bila ada kendala.</p>
        </div>
        <a href="{{ route('pendaftaran.index') }}" class="site-btn">Daftar Online Sekarang <i class="fa-solid fa-arrow-right"></i></a>
    </div>

</div>

@endsection
