@extends('layouts.admin')

@section('title', 'Pengaturan Akun | Pondok Pesantren Babul Hasanah')

@section('content')
@php
    $user = auth()->user();
    $avatarUrl = $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/img/logo/logo.png');
    $dashboardUrl = route(\App\Support\Akses::dashboardRoute($user));
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .pf {
        --surface: #ffffff; --border: #e6e3d8; --text: #1f2a24; --muted: #6b756f; --head: #17402f;
        --tint: #eef3ec; --chip: #f4f2ea; --accent: #12b886; --gold: #a98532;
        font-family: 'Poppins', system-ui, -apple-system, sans-serif;
        color: var(--text); display: flex; flex-direction: column; gap: 24px;
    }
    .dark .pf {
        --surface: #14201a; --border: #26362d; --text: #e8efe9; --muted: #9db0a5; --head: #e8efe9;
        --tint: #1a2b23; --chip: #1c2c24; --accent: #2fe0a0; --gold: #d3b265;
    }
    .pf *, .pf *::before, .pf *::after { box-sizing: border-box; }
    .pf a { text-decoration: none; color: inherit; }
    .pf h1, .pf h2, .pf h3, .pf p { margin: 0; }

    .pf-top {
        display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;
    }
    .pf-title h1 { font-size: 26px; font-weight: 700; color: var(--head); }
    .pf-title p { font-size: 14px; color: var(--muted); margin-top: 6px; }

    .pf-back {
        display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600;
        color: var(--head); border: 1px solid var(--border); background: var(--surface);
        padding: 10px 16px; border-radius: 12px; transition: all .2s ease;
    }
    .pf-back:hover { border-color: var(--accent); color: var(--accent); }

    .pf-grid { display: grid; grid-template-columns: 340px 1fr; gap: 24px; align-items: start; }
    @media (max-width: 900px) { .pf-grid { grid-template-columns: 1fr; } }

    /* Avatar card */
    .pf-card { background: var(--surface); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; }
    .pf-avatar-wrap { padding: 26px; display: flex; flex-direction: column; align-items: center; gap: 16px; border-bottom: 1px solid var(--border); }
    .pf-avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--tint); background: var(--chip); }
    .pf-avatar-edit {
        position: relative; display: inline-flex; align-items: center; justify-content: center;
        width: 40px; height: 40px; border-radius: 50%; background: var(--head); color: #fff;
        cursor: pointer; transition: all .2s ease; margin-top: -20px;
    }
    .pf-avatar-edit:hover { background: var(--accent); transform: scale(1.05); }
    .pf-avatar-edit i { font-size: 14px; }
    .pf-name { font-size: 20px; font-weight: 700; color: var(--head); text-align: center; }
    .pf-email { font-size: 13px; color: var(--muted); text-align: center; word-break: break-all; }
    .pf-role {
        font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em;
        padding: 4px 12px; border-radius: 999px; background: var(--chip); color: var(--head);
        display: inline-block; margin-top: 6px;
    }

    .pf-avatar-actions { margin-top: 10px; display: flex; gap: 10px; }
    .pf-avatar-form { display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .pf-avatar-form .pf-avatar-actions { margin-top: 0; }
    .pf-file { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden;
        clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
    .pf-btn-upload {
        display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600;
        background: var(--head); color: #fff; border: none; padding: 10px 18px; border-radius: 12px;
        cursor: pointer; transition: all .2s ease;
    }
    .pf-btn-upload:hover { background: var(--accent); }
    .pf-btn-upload input { display: none; }

    /* Form section */
    .pf-section { background: var(--surface); border: 1px solid var(--border); border-radius: 20px; padding: 24px; display: flex; flex-direction: column; gap: 20px; }
    .pf-section h3 { font-size: 16px; font-weight: 700; color: var(--head); display: flex; align-items: center; gap: 10px; }
    .pf-section h3 i { color: var(--accent); font-size: 15px; }

    .pf-form { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 640px) { .pf-form { grid-template-columns: 1fr; } }

    .pf-field { display: flex; flex-direction: column; gap: 7px; }
    .pf-field.full { grid-column: 1 / -1; }
    .pf-label { font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .04em; }
    .pf-input {
        width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 12px;
        background: var(--surface); color: var(--text); font-family: inherit; font-size: 14px;
        transition: all .2s ease; box-sizing: border-box;
    }
    .pf-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 20%, transparent); }
    .pf-hint { font-size: 11px; color: var(--muted); margin-top: 2px; }

    .pf-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 8px; }
    .pf-btn-save {
        display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 700;
        background: var(--head); color: #fff; border: none; padding: 12px 24px; border-radius: 12px;
        cursor: pointer; transition: all .2s ease;
    }
    .pf-btn-save:hover { background: var(--accent); transform: translateY(-1px); }
    .pf-btn-cancel {
        display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600;
        background: var(--surface); color: var(--text); border: 1px solid var(--border);
        padding: 12px 24px; border-radius: 12px; cursor: pointer; transition: all .2s ease;
    }
    .pf-btn-cancel:hover { background: var(--chip); }

    /* Password section */
    .pf-password { background: var(--tint); border-radius: 16px; padding: 20px; }
    .pf-password h3 { font-size: 16px; font-weight: 700; color: var(--head); }
    .pf-note { font-size: 12.5px; color: var(--muted); margin-top: 6px; line-height: 1.6; }

    .pf-alert { padding: 12px 16px; border-radius: 12px; font-size: 13px; display: flex; gap: 10px; align-items: center; }
    .pf-alert.ok { background: #e3f1e8; color: #17603f; }
    .pf-alert.bad { background: #fbe3e3; color: #a02b2b; }

    .pf-divider { display: flex; align-items: center; gap: 12px; color: var(--muted); font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .pf-divider::before, .pf-divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
</style>

<div class="pf">

    @if (session('success'))
        <div class="pf-alert ok"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="pf-alert bad"><i class="fa-solid fa-circle-exclamation"></i>{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="pf-alert bad">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                @foreach ($errors->all() as $pesan)
                    <div>{{ $pesan }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="pf-top">
        <div class="pf-title">
            <h1>Pengaturan Akun</h1>
            <p>Kelola nama, email, foto profil, dan password Anda.</p>
        </div>
        <a href="{{ $dashboardUrl }}" class="pf-back"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>

    <div class="pf-grid">
        <!-- Kolom kiri: Foto profil -->
        <div class="pf-card">
            <div class="pf-avatar-wrap">
                <img src="{{ $avatarUrl }}" alt="Foto Profil" class="pf-avatar" onerror="this.src='{{ asset('assets/img/logo/logo.png') }}'">
                <label for="avatarInput" class="pf-avatar-edit" title="Ubah Foto Profil">
                    <i class="fa-solid fa-camera"></i>
                </label>
                <form method="POST" action="{{ route('admin.profil.avatar') }}" enctype="multipart/form-data" class="pf-avatar-form">
                    @csrf
                    <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/webp"
                        class="pf-file" onchange="this.form.submit()">
                    <div class="pf-avatar-actions">
                        <label for="avatarInput" class="pf-btn-upload">
                            <i class="fa-solid fa-image"></i> Pilih Foto
                        </label>
                        <noscript>
                            <button type="submit" class="pf-btn-upload"><i class="fa-solid fa-upload"></i> Unggah</button>
                        </noscript>
                    </div>
                    <span class="pf-hint">JPG, PNG, atau WEBP &middot; maks. 2 MB</span>
                </form>
                <h2 class="pf-name">{{ $user->name }}</h2>
                <span class="pf-email">{{ $user->email }}</span>
                <span class="pf-role">{{ \App\Support\Akses::label($user->role) }}</span>
            </div>
        </div>

        <!-- Kolom kanan: Form -->
        <div>
            <!-- Form Ubah Nama & Email -->
            <form method="POST" action="{{ route('admin.profil.update') }}">
                @csrf
                @method('PUT')
                <div class="pf-section">
                    <h3><i class="fa-solid fa-user"></i> Ubah Nama &amp; Email</h3>
                    <div class="pf-form">
                        <div class="pf-field">
                            <label class="pf-label">Nama Lengkap</label>
                            <input type="text" name="name" class="pf-input" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="pf-field">
                            <label class="pf-label">Email Login</label>
                            <input type="email" name="email" class="pf-input" value="{{ old('email', $user->email) }}" required>
                            <span class="pf-hint">Email ini digunakan untuk masuk ke panel admin.</span>
                        </div>
                        <div class="pf-field full">
                            <label class="pf-label">Peran Akun</label>
                            <input type="text" class="pf-input" value="{{ \App\Support\Akses::label($user->role) }}" disabled>
                            <span class="pf-hint">Peran hanya dapat diubah oleh Super Admin melalui menu Pengguna.</span>
                        </div>
                    </div>
                    <div class="pf-actions">
                        <button type="submit" class="pf-btn-save"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
                    </div>
                </div>
            </form>

            <!-- Form Ubah Password -->
            <div class="pf-password">
                <h3><i class="fa-solid fa-key"></i> Reset / Ganti Password</h3>
                <form method="POST" action="{{ route('admin.profil.password') }}">
                    @csrf
                    @method('PUT')
                    <div class="pf-form">
                        <div class="pf-field full">
                            <label class="pf-label">Password Baru</label>
                            <input type="password" name="password" class="pf-input" placeholder="••••••••" required>
                            <span class="pf-hint">Minimal 8 karakter.</span>
                        </div>
                        <div class="pf-field full">
                            <label class="pf-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="pf-input" placeholder="••••••••" required>
                        </div>
                    </div>
                    <div class="pf-actions" style="justify-content: flex-end;">
                        <button type="submit" class="pf-btn-save"><i class="fa-solid fa-key"></i> Ubah Password</button>
                    </div>
                </form>
            </div>

            <div class="pf-note">
                <strong>Catatan:</strong> Setelah mengubah email atau password, Anda mungkin perlu login ulang pada sesi berikutnya. Foto profil akan langsung diperbarui setelah diunggah.
            </div>
        </div>
    </div>

</div>

@endsection