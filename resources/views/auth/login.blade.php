@extends('layouts.auth')

@section('title', 'Portal Login - Pondok Pesantren Babul Hasanah')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* ===== Halaman Login (palet sama dengan layouts/app, berdiri sendiri) ===== */
    .lg-page {
        --green: #17402f; --green-2: #1d4d3a; --cream: #f1eee6; --paper: #fbfaf6; --gold: #b7791f;
        --ink: #1f2a24; --muted: #6b756f; --line: #e4e0d4; --emerald: #12b886; --emerald-d: #0e9f73;
        --emerald-l: #2fe0a0; --shadow-lg: 0 22px 50px rgba(23, 64, 47, .14);

        font-family: 'Poppins', system-ui, -apple-system, sans-serif; color: var(--ink); background: var(--paper);
        min-height: 100vh; min-height: 100dvh; display: grid; grid-template-columns: 1fr 1fr;
    }
    .lg-page *, .lg-page *::before, .lg-page *::after { box-sizing: border-box; }
    .lg-page a { text-decoration: none; color: inherit; }

    /* ---------- Panel kiri (branding) ---------- */
    .lg-brand {
        position: relative; overflow: hidden; background: var(--green); color: #cfe0d6;
        padding: 48px 56px; display: flex; flex-direction: column; justify-content: space-between;
        border-radius: 0 36px 36px 0;
        background-image:
            radial-gradient(circle at 15% 105%, rgba(47, 224, 160, .22), transparent 45%),
            radial-gradient(circle at 100% 0%, rgba(183, 121, 31, .22), transparent 40%),
            radial-gradient(rgba(255, 255, 255, .07) 1px, transparent 1px);
        background-size: auto, auto, 24px 24px;
    }
    .lg-brand > * { position: relative; z-index: 1; }

    .lg-logo { display: inline-flex; align-items: center; gap: 12px; }
    .lg-logo-img {
        width: 48px; height: 48px; border-radius: 14px; background: rgba(255, 255, 255, .12); padding: 7px; flex: none;
        border: 1px solid rgba(255, 255, 255, .2); transition: transform .25s ease;
    }
    .lg-logo:hover .lg-logo-img { transform: scale(1.06); }
    .lg-logo-img img { width: 100%; height: 100%; object-fit: contain; display: block; }
    .lg-logo small { display: block; font-size: 11px; font-weight: 500; color: #a9c4b6; line-height: 1.1; }
    .lg-logo strong { display: block; font-size: 18px; font-weight: 600; color: #fff; line-height: 1.3; }

    .lg-hero { max-width: 460px; }
    .lg-tag {
        display: inline-flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 600; letter-spacing: .1em;
        text-transform: uppercase; color: var(--emerald-l); background: rgba(255, 255, 255, .1);
        border: 1px solid rgba(255, 255, 255, .18); padding: 7px 14px; border-radius: 999px;
    }
    .lg-hero h1 { color: #fff; font-size: clamp(30px, 3.2vw, 42px); font-weight: 600; line-height: 1.25; margin: 20px 0 14px; }
    .lg-hero h1 span { color: var(--emerald-l); }
    .lg-hero > p { font-size: 14px; line-height: 1.8; color: #cfe0d6; }

    .lg-points { list-style: none; margin: 28px 0 0; padding: 0; display: flex; flex-direction: column; gap: 14px; }
    .lg-points li { display: flex; align-items: center; gap: 12px; font-size: 13.5px; color: #e6f0ea; }
    .lg-points i {
        width: 30px; height: 30px; border-radius: 50%; background: rgba(47, 224, 160, .16); color: var(--emerald-l);
        display: grid; place-items: center; font-size: 12px; flex: none;
    }

    .lg-copy { font-size: 11.5px; color: #a9c4b6; }

    /* ---------- Panel kanan (form) ---------- */
    .lg-side { display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 32px 24px; position: relative; }
    .lg-back {
        width: 100%; max-width: 440px; margin-bottom: 16px; font-size: 13px; font-weight: 500; color: var(--muted);
        display: inline-flex; align-items: center; gap: 8px; transition: color .2s ease;
    }
    .lg-back i { font-size: 11px; transition: transform .2s ease; }
    .lg-back:hover { color: var(--emerald-d); }
    .lg-back:hover i { transform: translateX(-3px); }

    .lg-card {
        width: 100%; max-width: 440px; background: #fff; border: 1px solid var(--line); border-radius: 28px;
        padding: 40px 36px; box-shadow: var(--shadow-lg);
    }
    .lg-mobile-logo { display: none; align-items: center; gap: 12px; margin-bottom: 22px; }
    .lg-mobile-logo .lg-logo-img { background: #e8f6ef; border-color: transparent; width: 44px; height: 44px; }
    .lg-mobile-logo small { color: var(--emerald-d); }
    .lg-mobile-logo strong { color: var(--green); font-size: 17px; }

    .lg-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--gold); }
    .lg-title { font-size: 26px; font-weight: 600; color: var(--green); margin-top: 6px; line-height: 1.25; }
    .lg-title span { color: var(--emerald-d); }
    .lg-sub { font-size: 13px; color: var(--muted); line-height: 1.7; margin: 8px 0 24px; }

    .lg-alert {
        display: flex; align-items: flex-start; gap: 10px; font-size: 12.5px; font-weight: 500; line-height: 1.6;
        background: #eaf7f1; border: 1px solid #bfe8d6; color: #0b6b4d; padding: 12px 14px; border-radius: 14px; margin-bottom: 18px;
    }
    .lg-alert i { margin-top: 3px; color: var(--emerald-d); }

    .lg-field { margin-bottom: 16px; }
    .lg-label { display: flex; align-items: center; justify-content: space-between; margin-bottom: 7px; }
    .lg-label label { font-size: 12px; font-weight: 600; color: var(--green); }
    .lg-label a { font-size: 12px; font-weight: 500; color: var(--emerald-d); transition: color .2s ease; }
    .lg-label a:hover { color: var(--green); }

    .lg-input { position: relative; }
    .lg-input > i.lead {
        position: absolute; left: 16px; top: 50%; transform: translateY(-50%); font-size: 14px; color: #9aa59f;
        pointer-events: none; transition: color .2s ease;
    }
    .lg-input input {
        width: 100%; height: 50px; font-family: inherit; font-size: 14px; color: var(--ink); background: var(--paper);
        border: 1px solid var(--line); border-radius: 14px; padding: 0 16px 0 44px; outline: none;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .lg-input input::placeholder { color: #a9b2ad; }
    .lg-input input:focus { border-color: var(--emerald); background: #fff; box-shadow: 0 0 0 4px rgba(18, 184, 134, .12); }
    .lg-input:focus-within > i.lead { color: var(--emerald-d); }
    .lg-input input.invalid { border-color: #e5484d; background: #fff7f7; }
    .lg-input input.invalid:focus { box-shadow: 0 0 0 4px rgba(229, 72, 77, .12); }
    .lg-input.has-eye input { padding-right: 48px; }
    .lg-eye {
        position: absolute; right: 8px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border: 0;
        background: none; border-radius: 10px; cursor: pointer; color: #9aa59f; font-size: 14px; transition: all .2s ease;
    }
    .lg-eye:hover { background: var(--cream); color: var(--green); }
    .lg-error { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; color: #d92d33; margin-top: 7px; }

    .lg-remember { display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 12.5px; color: var(--muted); margin: 4px 0 20px; user-select: none; }
    .lg-remember input { width: 17px; height: 17px; accent-color: var(--emerald-d); cursor: pointer; margin: 0; }
    .lg-remember:hover { color: var(--ink); }

    .lg-btn {
        width: 100%; height: 52px; border: 0; cursor: pointer; font-family: inherit; font-size: 14px; font-weight: 600; color: #fff;
        background: var(--emerald); border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; gap: 10px;
        box-shadow: 0 8px 20px rgba(18, 184, 134, .28); transition: all .25s ease;
    }
    .lg-btn:hover { background: var(--emerald-d); transform: translateY(-2px); }
    .lg-btn:active { transform: translateY(0); }
    .lg-btn i { font-size: 12px; transition: transform .25s ease; }
    .lg-btn:hover i { transform: translateX(4px); }
    .lg-btn.loading { pointer-events: none; opacity: .85; }
    .lg-btn.loading i { display: none; }
    .lg-spin {
        display: none; width: 16px; height: 16px; border-radius: 50%; border: 2px solid rgba(255, 255, 255, .4);
        border-top-color: #fff; animation: lgspin .7s linear infinite;
    }
    .lg-btn.loading .lg-spin { display: inline-block; }
    @keyframes lgspin { to { transform: rotate(360deg); } }

    .lg-divider { display: flex; align-items: center; gap: 12px; margin: 22px 0; font-size: 11px; font-weight: 500; color: var(--muted); }
    .lg-divider::before, .lg-divider::after { content: ""; flex: 1; height: 1px; background: var(--line); }

    .lg-google {
        width: 100%; height: 50px; display: flex; align-items: center; justify-content: center; gap: 12px;
        background: #fff; border: 1px solid var(--line); border-radius: 14px; font-size: 13.5px; font-weight: 600; color: var(--ink);
        transition: all .2s ease;
    }
    .lg-google:hover { border-color: var(--emerald); background: #f6fbf8; transform: translateY(-1px); }
    .lg-google svg { width: 20px; height: 20px; flex: none; }

    .lg-foot { text-align: center; font-size: 12.5px; color: var(--muted); margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--line); }
    .lg-foot a { font-weight: 600; color: var(--emerald-d); transition: color .2s ease; }
    .lg-foot a:hover { color: var(--green); }

    /* ---------- Responsif ---------- */
    @media (max-width: 960px) {
        .lg-page { grid-template-columns: 1fr; background: var(--cream); }
        .lg-brand { display: none; }
        .lg-side { padding: 28px 18px 40px; justify-content: flex-start; }
        .lg-mobile-logo { display: flex; }
        .lg-card { padding: 32px 24px; border-radius: 24px; }
    }
    @media (max-width: 420px) {
        .lg-title { font-size: 23px; }
    }
</style>

<div class="lg-page">

    {{-- ===================== PANEL KIRI ===================== --}}
    <aside class="lg-brand">
        <a href="{{ route('home') }}" class="lg-logo">
            <span class="lg-logo-img"><img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo Babul Hasanah"></span>
            <span><small>Pondok Pesantren</small><strong>Babul Hasanah</strong></span>
        </a>

        <div class="lg-hero">
            <span class="lg-tag"><i class="fa-solid fa-graduation-cap"></i> Portal Akademik Terpadu</span>
            <h1>Mencetak Generasi <span>Berilmu, Beramal</span> &amp; Berakhlak.</h1>
            <p>Platform manajemen data santri, pengumuman, dan administrasi harian pondok pesantren secara terpadu dan digital.</p>

            <ul class="lg-points">
                <li><i class="fa-solid fa-check"></i> Data santri tersimpan rapi dan terpusat</li>
                <li><i class="fa-solid fa-check"></i> Pengumuman dan informasi akademik terkini</li>
                <li><i class="fa-solid fa-check"></i> Portal khusus untuk wali santri</li>
            </ul>
        </div>

        <div class="lg-copy">&copy; {{ date('Y') }} Pondok Pesantren Babul Hasanah. All rights reserved.</div>
    </aside>

    {{-- ===================== PANEL KANAN ===================== --}}
    <main class="lg-side">

        <a href="{{ route('home') }}" class="lg-back"><i class="fa-solid fa-arrow-left"></i> Kembali ke beranda</a>

        <div class="lg-card">

            {{-- Logo (hanya tampil di HP) --}}
            <a href="{{ route('home') }}" class="lg-logo lg-mobile-logo">
                <span class="lg-logo-img"><img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo Babul Hasanah"></span>
                <span><small>Pondok Pesantren</small><strong>Babul Hasanah</strong></span>
            </a>

            <span class="lg-eyebrow">Masuk Sistem</span>
            <h2 class="lg-title">Selamat <span>Datang!</span></h2>
            <p class="lg-sub">Masukkan email dan kata sandi Anda untuk mengakses sistem.</p>

            @if (session('status'))
                <div class="lg-alert">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" id="loginForm" novalidate>
                @csrf

                {{-- Email --}}
                <div class="lg-field">
                    <div class="lg-label"><label for="email">Alamat Email</label></div>
                    <div class="lg-input">
                        <i class="fa-regular fa-envelope lead"></i>
                        <input id="email" name="email" type="email" autocomplete="email" required autofocus
                               value="{{ old('email') }}" placeholder="nama@email.com"
                               class="@error('email') invalid @enderror">
                    </div>
                    @error('email')
                        <span class="lg-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Kata sandi --}}
                <div class="lg-field">
                    <div class="lg-label">
                        <label for="password">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Lupa sandi?</a>
                        @endif
                    </div>
                    <div class="lg-input has-eye">
                        <i class="fa-solid fa-lock lead"></i>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               placeholder="Masukkan kata sandi" class="@error('password') invalid @enderror">
                        <button type="button" class="lg-eye" onclick="togglePassword()" aria-label="Tampilkan atau sembunyikan kata sandi">
                            <i id="toggleIcon" class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="lg-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>

                <label class="lg-remember">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Ingat saya di perangkat ini</span>
                </label>

                <button type="submit" class="lg-btn" id="loginBtn">
                    <span class="lg-spin"></span>
                    <span id="loginText">Masuk ke Dashboard</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div class="lg-divider">atau masuk dengan</div>

            <a href="#" class="lg-google">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z" />
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.2v3.15C3.16 21.35 7.22 24 12 24z" />
                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.2C.44 8.12 0 9.87 0 11.7s.44 3.58 1.2 5.12l4.08-2.55z" />
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.22 0 3.16 2.65 1.2 6.58l4.08 3.15c.95-2.83 3.6-4.98 6.72-4.98z" />
                </svg>
                <span>Masuk dengan Google</span>
            </a>

            <p class="lg-foot">
                Belum mempunyai akun?
                <a href="{{ route('pendaftaran.index') }}">Daftarkan Santri</a>
            </p>
        </div>
    </main>

</div>

<script>
    // Tampilkan / sembunyikan kata sandi
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        const tampil = input.type === 'password';
        input.type = tampil ? 'text' : 'password';
        icon.classList.toggle('fa-eye', !tampil);
        icon.classList.toggle('fa-eye-slash', tampil);
    }

    // Tombol berubah jadi "memproses" saat form dikirim, mencegah klik ganda
    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn = document.getElementById('loginBtn');
        btn.classList.add('loading');
        document.getElementById('loginText').textContent = 'Memproses...';
    });
</script>

@endsection