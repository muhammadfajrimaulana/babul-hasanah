@extends('layouts.admin')

@section('title', $item ? 'Ubah Info Harian' : 'Tambah Info Harian')

@section('content')

@php
    $adalahJadwal = $jenis === 'jadwal';
    $lama = session()->hasOldInput();

    $aktif   = $lama ? old('aktif')   : ($item?->aktif ?? true);
    $penting = $lama ? old('penting') : ($item?->penting ?? false);

    $nilai = fn (string $kolom, $default = '') => old($kolom, $item?->{$kolom} ?? $default);
    $tgl   = fn (string $kolom) => old($kolom, $item?->{$kolom}?->format('Y-m-d'));
@endphp

<style>
    .if {
        --surface: #ffffff; --border: #e6e3d8; --text: #1f2a24; --muted: #6b756f; --head: #17402f;
        --chip: #f4f2ea; --accent: #12b886; --bad-tx: #a02b2b; --bad-bg: #fbe3e3;
        font-family: 'Poppins', system-ui, -apple-system, sans-serif; color: var(--text); max-width: 720px;
    }
    .dark .if {
        --surface: #14201a; --border: #26362d; --text: #e8efe9; --muted: #9db0a5; --head: #e8efe9;
        --chip: #1c2c24; --accent: #2fe0a0; --bad-tx: #f08c8c; --bad-bg: #3d1c1e;
    }
    .if *, .if *::before, .if *::after { box-sizing: border-box; }
    .if a { text-decoration: none; }
    .if h1, .if p { margin: 0; }
    .if-back { font-size: 12.5px; color: var(--muted); display: inline-flex; gap: 8px; align-items: center; margin-bottom: 14px; }
    .if-back:hover { color: var(--accent); }
    .if h1 { font-size: 22px; font-weight: 600; color: var(--head); margin-bottom: 18px; }
    .if-card { background: var(--surface); border: 1px solid var(--border); border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; }
    .if-f label.t { display: block; font-size: 12.5px; font-weight: 600; color: var(--head); margin-bottom: 7px; }
    .if-f label.t small { font-weight: 400; color: var(--muted); }
    .if-f input[type=text], .if-f input[type=date], .if-f input[type=number], .if-f select, .if-f textarea {
        width: 100%; font-family: inherit; font-size: 13.5px; color: var(--text); background: var(--surface);
        border: 1px solid var(--border); border-radius: 12px; padding: 11px 14px; transition: border-color .2s ease;
    }
    .if-f textarea { min-height: 96px; resize: vertical; }
    .if-f input:focus, .if-f select:focus, .if-f textarea:focus { outline: none; border-color: var(--accent); }
    .if-f .hint { font-size: 11.5px; color: var(--muted); margin-top: 6px; }
    .if-f .err { font-size: 12px; color: var(--bad-tx); margin-top: 6px; }
    .if-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .if-check { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; cursor: pointer; }
    .if-check input { width: 17px; height: 17px; margin-top: 2px; accent-color: var(--accent); }
    .if-check small { display: block; color: var(--muted); font-size: 11.5px; margin-top: 2px; }
    .if-act { display: flex; gap: 12px; flex-wrap: wrap; padding-top: 6px; }
    .if-btn {
        display: inline-flex; align-items: center; gap: 9px; font-family: inherit; font-size: 13px; font-weight: 600;
        border-radius: 12px; padding: 12px 22px; cursor: pointer; border: 1px solid var(--border); background: var(--surface); color: var(--head);
    }
    .if-btn.main { background: var(--head); border-color: var(--head); color: #fff; }
    .if-btn.main:hover { background: var(--accent); border-color: var(--accent); }
    .dark .if-btn.main { background: var(--accent); border-color: var(--accent); color: #0f2018; }
    @media (max-width: 560px) { .if-2 { grid-template-columns: 1fr; } .if-card { padding: 20px; } }
</style>

<div class="if">
    <a href="{{ route('admin.info.index', ['jenis' => $jenis]) }}" class="if-back"><i class="fa-solid fa-arrow-left"></i> Kembali ke daftar</a>
    <h1>{{ $item ? 'Ubah' : 'Tambah' }} {{ $adalahJadwal ? 'Jadwal Harian' : 'Info Harian' }}</h1>

    <form method="POST" class="if-card"
          action="{{ $item ? route('admin.info.update', $item) : route('admin.info.store') }}">
        @csrf
        @if ($item) @method('PUT') @endif
        <input type="hidden" name="jenis" value="{{ $jenis }}">

        @if ($adalahJadwal)
            <div class="if-2">
                <div class="if-f">
                    <label class="t" for="jam">Jam</label>
                    <input type="text" id="jam" name="jam" value="{{ $nilai('jam') }}" placeholder="04.30" maxlength="5" required>
                    @error('jam')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div class="if-f">
                    <label class="t" for="urutan">Urutan <small>(jika jam sama)</small></label>
                    <input type="number" id="urutan" name="urutan" value="{{ $nilai('urutan', 0) }}" min="0">
                    @error('urutan')<p class="err">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="if-f">
                <label class="t" for="judul">Kegiatan</label>
                <input type="text" id="judul" name="judul" value="{{ $nilai('judul') }}" placeholder="Subuh berjamaah &amp; halaqah Al-Qur'an" maxlength="150" required>
                @error('judul')<p class="err">{{ $message }}</p>@enderror
            </div>
        @else
            <div class="if-f">
                <label class="t" for="kategori">Kategori</label>
                <select id="kategori" name="kategori" required>
                    <option value="">— Pilih kategori —</option>
                    @foreach (\App\Models\InfoHarian::KATEGORI as $nama => $ikon)
                        <option value="{{ $nama }}" @selected($nilai('kategori') === $nama)>{{ $nama }}</option>
                    @endforeach
                </select>
                @error('kategori')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div class="if-f">
                <label class="t" for="judul">Judul</label>
                <input type="text" id="judul" name="judul" value="{{ $nilai('judul') }}" placeholder="Contoh: Kajian Ahad pagi diliburkan" maxlength="150" required>
                @error('judul')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div class="if-f">
                <label class="t" for="isi">Keterangan <small>(opsional, maksimal 500 karakter)</small></label>
                <textarea id="isi" name="isi" maxlength="500">{{ $nilai('isi') }}</textarea>
                @error('isi')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div class="if-2">
                <div class="if-f">
                    <label class="t" for="tanggal">Mulai tayang</label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ $tgl('tanggal') ?: now()->toDateString() }}">
                    @error('tanggal')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div class="if-f">
                    <label class="t" for="berlaku_sampai">Berlaku sampai <small>(opsional)</small></label>
                    <input type="date" id="berlaku_sampai" name="berlaku_sampai" value="{{ $tgl('berlaku_sampai') }}">
                    <p class="hint">Setelah tanggal ini info otomatis hilang dari beranda.</p>
                    @error('berlaku_sampai')<p class="err">{{ $message }}</p>@enderror
                </div>
            </div>
            <label class="if-check">
                <input type="checkbox" name="penting" value="1" @checked($penting)>
                <span>Tandai penting<small>Judulnya ikut berjalan di bar INFO paling atas beranda.</small></span>
            </label>
        @endif

        <label class="if-check">
            <input type="checkbox" name="aktif" value="1" @checked($aktif)>
            <span>Aktif<small>Matikan untuk menyembunyikan dari beranda tanpa menghapus.</small></span>
        </label>

        <div class="if-act">
            <button type="submit" class="if-btn main"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
            <a href="{{ route('admin.info.index', ['jenis' => $jenis]) }}" class="if-btn">Batal</a>
        </div>
    </form>
</div>

@endsection
