@extends('layouts.admin')

@section('title', 'Info Harian')

@section('content')

@php
    $labelJenis = ['info' => 'Info & Pengumuman', 'jadwal' => 'Jadwal Harian'];
@endphp

<style>
    .ia {
        --surface: #ffffff; --border: #e6e3d8; --text: #1f2a24; --muted: #6b756f; --head: #17402f;
        --chip: #f4f2ea; --accent: #12b886; --gold: #a98532;
        --ok-bg: #e3f1e8; --ok-tx: #17603f; --wait-bg: #faecc9; --wait-tx: #8a5a0a; --bad-bg: #fbe3e3; --bad-tx: #a02b2b;
        font-family: 'Poppins', system-ui, -apple-system, sans-serif; color: var(--text);
        display: flex; flex-direction: column; gap: 20px;
    }
    .dark .ia {
        --surface: #14201a; --border: #26362d; --text: #e8efe9; --muted: #9db0a5; --head: #e8efe9;
        --chip: #1c2c24; --accent: #2fe0a0; --gold: #d3b265;
        --ok-bg: #12382a; --ok-tx: #6ee0ae; --wait-bg: #3a2f12; --wait-tx: #f0c36a; --bad-bg: #3d1c1e; --bad-tx: #f08c8c;
    }
    .ia *, .ia *::before, .ia *::after { box-sizing: border-box; }
    .ia a { text-decoration: none; }
    .ia h1, .ia p { margin: 0; }
    .ia-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; flex-wrap: wrap; }
    .ia-top h1 { font-size: 24px; font-weight: 600; color: var(--head); }
    .ia-top p { font-size: 13px; color: var(--muted); margin-top: 4px; }
    .ia-btn {
        display: inline-flex; align-items: center; gap: 9px; font-size: 13px; font-weight: 600; border-radius: 12px;
        padding: 11px 18px; border: 1px solid var(--border); background: var(--surface); color: var(--head);
        cursor: pointer; font-family: inherit; transition: all .2s ease;
    }
    .ia-btn:hover { border-color: var(--accent); color: var(--accent); }
    .ia-btn.main { background: var(--head); border-color: var(--head); color: #fff; }
    .ia-btn.main:hover { background: var(--accent); border-color: var(--accent); color: #fff; }
    .dark .ia-btn.main { background: var(--accent); border-color: var(--accent); color: #0f2018; }
    .ia-alert { padding: 12px 16px; border-radius: 12px; font-size: 13px; display: flex; gap: 10px; align-items: center; }
    .ia-alert.ok { background: var(--ok-bg); color: var(--ok-tx); }
    .ia-alert.bad { background: var(--bad-bg); color: var(--bad-tx); }
    .ia-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
    .ia-tabs a {
        display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500; color: var(--muted);
        background: var(--surface); border: 1px solid var(--border); border-radius: 999px; padding: 9px 16px; transition: all .2s ease;
    }
    .ia-tabs a span { font-size: 11px; background: var(--chip); border-radius: 999px; padding: 2px 8px; }
    .ia-tabs a.on { background: var(--head); border-color: var(--head); color: #fff; font-weight: 600; }
    .dark .ia-tabs a.on { background: var(--accent); border-color: var(--accent); color: #0f2018; }
    .ia-tabs a.on span { background: rgba(255, 255, 255, .18); color: inherit; }
    .ia-card { background: var(--surface); border: 1px solid var(--border); border-radius: 18px; overflow: hidden; }
    .ia-scroll { overflow-x: auto; }
    .ia table { width: 100%; border-collapse: collapse; min-width: 640px; }
    .ia th {
        text-align: left; font-size: 10.5px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase;
        color: var(--muted); background: var(--chip); padding: 12px 18px;
    }
    .ia td { padding: 14px 18px; border-top: 1px solid var(--border); font-size: 12.5px; color: var(--muted); vertical-align: middle; }
    .ia tr:hover td { background: var(--chip); }
    .ia td.nm { color: var(--text); font-weight: 600; font-size: 13px; }
    .ia td.nm small { display: block; font-weight: 400; color: var(--muted); font-size: 11.5px; margin-top: 3px; max-width: 360px; }
    .ia-pin { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; background: var(--ok-bg); color: var(--ok-tx); padding: 2px 8px; border-radius: 999px; margin-left: 6px; }
    .ia-jam { font-size: 15px; font-weight: 600; color: var(--head); }
    .ia-pill { display: inline-block; font-size: 11px; font-weight: 600; padding: 4px 11px; border-radius: 999px; }
    .ia-pill.ok { background: var(--ok-bg); color: var(--ok-tx); }
    .ia-pill.wait { background: var(--wait-bg); color: var(--wait-tx); }
    .ia-pill.bad { background: var(--bad-bg); color: var(--bad-tx); }
    .ia-pill.off { background: var(--chip); color: var(--muted); }
    .ia-act { display: flex; gap: 8px; justify-content: flex-end; }
    .ia-act a, .ia-act button {
        width: 34px; height: 34px; border-radius: 10px; border: 1px solid var(--border); background: var(--surface);
        color: var(--muted); display: grid; place-items: center; cursor: pointer; font-size: 13px; transition: all .2s ease;
    }
    .ia-act a:hover { color: var(--accent); border-color: var(--accent); }
    .ia-act button:hover { color: var(--bad-tx); border-color: var(--bad-tx); }
    .ia-empty { text-align: center; padding: 48px 20px; color: var(--muted); font-size: 13px; }
    .ia-empty i { display: block; font-size: 30px; color: var(--border); margin-bottom: 12px; }
</style>

<div class="ia">
    <div class="ia-top">
        <div>
            <h1>Info Harian</h1>
            <p>Kelola pengumuman dan jadwal yang tampil di bagian atas beranda website.</p>
        </div>
        <a href="{{ route('admin.info.create', ['jenis' => $jenis]) }}" class="ia-btn main">
            <i class="fa-solid fa-plus"></i> Tambah {{ $jenis === 'info' ? 'Info' : 'Jadwal' }}
        </a>
    </div>

    @if (session('success'))
        <div class="ia-alert ok"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="ia-alert bad"><i class="fa-solid fa-circle-exclamation"></i>{{ session('error') }}</div>
    @endif

    <div class="ia-tabs">
        @foreach ($labelJenis as $k => $v)
            <a href="{{ route('admin.info.index', ['jenis' => $k]) }}" class="{{ $jenis === $k ? 'on' : '' }}">
                {{ $v }} <span>{{ $jumlah[$k] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <div class="ia-card">
        @if ($items->isEmpty())
            <div class="ia-empty"><i class="fa-regular fa-folder-open"></i>Belum ada data. Klik “Tambah” untuk membuat yang pertama.</div>
        @else
            <div class="ia-scroll">
                <table>
                    <thead>
                        <tr>
                            @if ($jenis === 'info')
                                <th>Judul</th><th>Kategori</th><th>Tayang</th><th>Status</th>
                            @else
                                <th>Jam</th><th>Kegiatan</th><th>Urutan</th><th>Status</th>
                            @endif
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $it)
                            @php
                                if (! $it->aktif) {
                                    $st = ['Nonaktif', 'off'];
                                } elseif ($it->kedaluwarsa) {
                                    $st = ['Kedaluwarsa', 'bad'];
                                } elseif ($it->jenis === 'info' && $it->tanggal && $it->tanggal->isFuture()) {
                                    $st = ['Terjadwal', 'wait'];
                                } else {
                                    $st = ['Tayang', 'ok'];
                                }
                            @endphp
                            <tr>
                                @if ($jenis === 'info')
                                    <td class="nm">
                                        {{ $it->judul }}
                                        @if ($it->penting)<span class="ia-pin">Penting</span>@endif
                                        @if ($it->isi)<small>{{ \Illuminate\Support\Str::limit($it->isi, 90) }}</small>@endif
                                    </td>
                                    <td><i class="fa-solid {{ $it->ikon }}" style="color:var(--gold);margin-right:6px"></i>{{ $it->kategori }}</td>
                                    <td>
                                        {{ $it->tanggal?->format('d/m/Y') }}
                                        @if ($it->berlaku_sampai) <br><small>s/d {{ $it->berlaku_sampai->format('d/m/Y') }}</small> @endif
                                    </td>
                                @else
                                    <td class="ia-jam">{{ $it->jam }}</td>
                                    <td class="nm">{{ $it->judul }}</td>
                                    <td>{{ $it->urutan }}</td>
                                @endif
                                <td><span class="ia-pill {{ $st[1] }}">{{ $st[0] }}</span></td>
                                <td>
                                    <div class="ia-act">
                                        <a href="{{ route('admin.info.edit', $it) }}" title="Ubah"><i class="fa-solid fa-pen"></i></a>
                                        <form method="POST" action="{{ route('admin.info.destroy', $it) }}"
                                              onsubmit="return confirm('Hapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection
