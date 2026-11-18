@extends('layouts.auth')

@section('title', 'Buat Kata Sandi Baru - Pondok Pesantren Babul Hasanah')

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
        <span class="pp-icon"><i class="fa-solid fa-lock"></i></span>
        <span class="pp-eyebrow">Pemulihan Akun</span>
        <h1 class="pp-title">Buat <span>Kata Sandi Baru</span></h1>
        <p class="pp-sub">
            Masukkan kata sandi baru untuk akun portal Anda. Minimal 6 karakter dan pastikan mudah Anda ingat.
        </p>

        @if ($errors->any())
            <div class="pp-alert error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('password.store') }}" method="POST" id="resetForm" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="pp-field">
                <div class="pp-label"><label for="email">Alamat Email</label></div>
                <div class="pp-input">
                    <i class="fa-regular fa-envelope lead"></i>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           value="{{ old('email', $email) }}" placeholder="nama@email.com"
                           class="@error('email') invalid @enderror">
                </div>
                @error('email')
                    <span class="pp-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                @enderror
            </div>

            <div class="pp-field">
                <div class="pp-label"><label for="password">Kata Sandi Baru</label></div>
                <div class="pp-input has-eye">
                    <i class="fa-solid fa-lock lead"></i>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                           placeholder="Minimal 6 karakter"
                           class="@error('password') invalid @enderror">
                    <button type="button" class="pp-eye" data-target="password" aria-label="Tampilkan atau sembunyikan kata sandi">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <span class="pp-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                @enderror
            </div>

            <div class="pp-field">
                <div class="pp-label"><label for="password_confirmation">Ulangi Kata Sandi Baru</label></div>
                <div class="pp-input has-eye">
                    <i class="fa-solid fa-lock lead"></i>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                           placeholder="Ketik ulang kata sandi baru">
                    <button type="button" class="pp-eye" data-target="password_confirmation" aria-label="Tampilkan atau sembunyikan kata sandi">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="pp-btn" id="resetBtn">
                <span class="pp-spin"></span>
                <span id="resetText">Simpan Kata Sandi Baru</span>
                <i class="fa-solid fa-check"></i>
            </button>
        </form>

        <ol class="pp-steps">
            <li class="done">
                <span class="dot"><i class="fa-solid fa-check"></i></span>
                <strong>Email</strong>
                Terverifikasi
            </li>
            <li class="active">
                <span class="dot">2</span>
                <strong>Sandi baru</strong>
                Sedang diatur
            </li>
            <li>
                <span class="dot">3</span>
                <strong>Masuk</strong>
                Dashboard
            </li>
        </ol>

        <div class="pp-tips">
            <i class="fa-solid fa-lightbulb"></i>
            <span>Gunakan kombinasi huruf besar, huruf kecil, dan angka. Hindari memakai kata sandi yang sama dengan akun lain.</span>
        </div>

        <p class="pp-foot">
            <a href="{{ route('login') }}"><i class="fa-solid fa-arrow-left"></i> Kembali ke halaman masuk</a>
        </p>
    </div>

    <p class="pp-copy">&copy; {{ date('Y') }} Pondok Pesantren Babul Hasanah. All rights reserved.</p>

</div>

<script>
    // Tampilkan / sembunyikan kata sandi
    document.querySelectorAll('.pp-eye').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById(btn.dataset.target);
            const icon = btn.querySelector('i');
            const tampil = input.type === 'password';
            input.type = tampil ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !tampil);
            icon.classList.toggle('fa-eye-slash', tampil);
        });
    });

    document.getElementById('resetForm').addEventListener('submit', function () {
        const btn = document.getElementById('resetBtn');
        btn.classList.add('loading');
        document.getElementById('resetText').textContent = 'Menyimpan...';
    });
</script>

@endsection
