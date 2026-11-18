@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi - Pondok Pesantren Babul Hasanah')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

@include('auth.partials.form-style')

<div class="pp-page">

    <a href="{{ route('home') }}" class="pp-brand">
        <span class="pp-brand-img"><img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo Babul Hasanah"></span>
        <span><small>Pondok Pesantren</small><strong>Babul Hasanah</strong></span>
    </a>

    <div class="pp-card">
        <span class="pp-icon"><i class="fa-solid fa-key"></i></span>
        <span class="pp-eyebrow">Pemulihan Akun</span>
        <h1 class="pp-title">Lupa <span>Kata Sandi?</span></h1>
        <p class="pp-sub">
            Masukkan email wali santri yang terdaftar. Tautan untuk membuat kata sandi baru akan
            kami kirim ke email tersebut.
        </p>

        @if (session('status'))
            <div class="pp-alert">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="pp-alert error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" id="forgotForm" novalidate>
            @csrf

            <div class="pp-field">
                <div class="pp-label"><label for="email">Alamat Email</label></div>
                <div class="pp-input">
                    <i class="fa-regular fa-envelope lead"></i>
                    <input id="email" name="email" type="email" autocomplete="email" required autofocus
                           value="{{ old('email') }}" placeholder="nama@email.com"
                           class="@error('email') invalid @enderror">
                </div>
                <p class="pp-hint"><i class="fa-solid fa-circle-info"></i> Email harus sama dengan yang terdaftar pada akun wali santri.</p>
            </div>

            <button type="submit" class="pp-btn" id="forgotBtn">
                <span class="pp-spin"></span>
                <span id="forgotText">Kirim Tautan Reset</span>
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>

        <ol class="pp-steps">
            <li class="active">
                <span class="dot">1</span>
                <strong>Isi email</strong>
                Email terdaftar
            </li>
            <li>
                <span class="dot">2</span>
                <strong>Buka email</strong>
                Klik tautan reset
            </li>
            <li>
                <span class="dot">3</span>
                <strong>Buat sandi</strong>
                Masuk kembali
            </li>
        </ol>

        <div class="pp-tips">
            <i class="fa-solid fa-inbox"></i>
            <span>Email belum masuk? Periksa folder <strong>Spam/Promosi</strong>, atau hubungi Sekretariat Pesantren untuk memastikan email Anda sudah terdaftar.</span>
        </div>

        <p class="pp-foot">
            <a href="{{ route('login') }}"><i class="fa-solid fa-arrow-left"></i> Kembali ke halaman masuk</a>
        </p>
    </div>

    <p class="pp-copy">&copy; {{ date('Y') }} Pondok Pesantren Babul Hasanah. All rights reserved.</p>

</div>

<script>
    document.getElementById('forgotForm').addEventListener('submit', function () {
        const btn = document.getElementById('forgotBtn');
        btn.classList.add('loading');
        document.getElementById('forgotText').textContent = 'Mengirim...';
    });
</script>

@endsection
