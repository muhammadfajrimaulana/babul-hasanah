@extends('layouts.admin')

@section('title', 'Dashboard Pondok Pesantren Babul Hasanah')

@section('content')

@php
    /*
     * Semua data datang dari DashboardController:
     * totalSantri, santriPutra, santriPutri, totalPendaftar, pendaftarMenunggu, pendaftarTerbaru,
     * totalBerita, beritaBulanIni, totalAsatidz, jmlGaleri, jmlPrestasi, tahunAjaran, tren, komposisi, agenda
     */
    $namaDepan = \Illuminate\Support\Str::before(auth()->user()->name ?? 'Admin', ' ');
    $tanggal   = now()->locale('id')->translatedFormat('l, d F Y');

    $ada = fn (string $nama, array $param = []) => \Illuminate\Support\Facades\Route::has($nama) ? route($nama, $param) : null;

    // Jumlah pengurus aktif di bagan (aman bila tabel belum dimigrasi)
    $jmlStruktur = 0;
    try {
        $jmlStruktur = \App\Models\StrukturOrganisasi::aktif()->diKategori('bagan')->count();
    } catch (\Throwable $e) {
    }

    // Kartu statistik: [label, angka, ikon, lencana, keterangan, warna]
    $stat = [
        ['Santri Aktif', $totalSantri,    'fa-user-graduate',   'Aktif',                      "Putra: {$santriPutra} | Putri: {$santriPutri}",   '#12a67a'],
        ['Pendaftar',    $totalPendaftar, 'fa-file-invoice',    $pendaftarMenunggu . ' baru', 'Menunggu verifikasi',                              '#3b82c4'],
        ['Berita',       $totalBerita,    'fa-newspaper',       'Publik',                     $beritaBulanIni . ' artikel terbit bulan ini',      '#c08a1c'],
        ['Asatidz',      $totalAsatidz,   'fa-chalkboard-user', 'Staf',                       'Pengajar & Pembina',                               '#8b5cc6'],
    ];

    // Fitur website: [nama, ikon, warna, deskripsi, link kelola, link lihat, badge jumlah]
    $fitur = [
        ['Profil Pesantren',   'fa-landmark',         '#12a67a', 'Sejarah, visi & misi, struktur organisasi, dan pembelajaran.', $ada('admin.konten.index'),      url('/profil/sejarah'),                          null],
        ['Struktur Organisasi', 'fa-sitemap',         '#0e9f8e', 'Bagan pengurus, ketua jenjang, dan bidang pelayanan beserta foto.', $ada('admin.struktur.index'), $ada('profil.struktur-organisasi'),          $jmlStruktur . ' pengurus'],
        ['Jenjang Pendidikan', 'fa-book-open-reader', '#3b82c4', 'MDTA, PPS Wustha, MTs, MA, Diniyah Formal, dan Ma’had ‘Aly.', $ada('admin.program.index'),     $ada('jenjang.show', ['slug' => 'mdta']),        null],
        ['Berita',             'fa-newspaper',        '#c08a1c', 'Kabar, pengumuman, dan artikel kegiatan pesantren.',            $ada('admin.berita.index'),      $ada('berita.index'),                            $totalBerita . ' berita'],
        ['Galeri',             'fa-images',           '#8b5cc6', 'Album foto dan video dokumentasi kegiatan dan fasilitas.',      $ada('admin.galeri.index'),      $ada('galeri'),                                  $jmlGaleri . ' album'],
        ['Kegiatan',           'fa-calendar-days',    '#0e9f8e', 'Agenda dan kegiatan santri di lingkungan pesantren.',           $ada('admin.kegiatan.index'),    $ada('kegiatan'),                                null],
        ['Prestasi Santri',    'fa-trophy',           '#d6525a', 'Capaian dan penghargaan yang diraih santri.',                   $ada('admin.prestasi.index'),    $ada('prestasi.index'),                          $jmlPrestasi . ' prestasi'],
        ['Pendaftaran Online', 'fa-file-signature',   '#12a67a', 'Formulir dan data calon santri baru.',                          $ada('admin.pendaftaran.index'), $ada('pendaftaran.index'),                       $totalPendaftar . ' pendaftar'],
        ['Data Alumni',        'fa-user-graduate',    '#0e9f8e', 'Data lulusan, angkatan, dan kegiatan alumni setelah lulus.',    $ada('admin.alumni.index'),      null,                                            $totalAlumni . ' alumni'],
        ['Pengguna Dashboard', 'fa-users-gear',       '#d6525a', 'Akun super admin, admin, dan wali santri yang dapat masuk panel.', $ada('super.pengguna.index'),    null,                                            $totalPengguna . ' akun'],
        ['Kontak',             'fa-address-book',     '#3b82c4', 'Alamat, telepon, email, dan peta lokasi pesantren.',            null,                            $ada('kontak'),                                  null],
    ];

    // Pengguna Dashboard hanya tampil untuk Super Admin
    $fitur = array_values(array_filter($fitur, fn ($f) => $f[0] !== 'Pengguna Dashboard' || $isSuperAdmin));

    // Grafik tren pendaftar (data dari controller, skala otomatis)
    $maks = max(10, (int) (ceil(max($tren) / 10) * 10));
    $W = 600; $H = 230; $kiri = 34; $kanan = 12; $atas = 14; $bawah = 28;
    $lebar = $W - $kiri - $kanan; $tinggi = $H - $atas - $bawah;
    $n = count($tren); $i = 0; $pts = [];
    foreach ($tren as $bln => $v) {
        $pts[] = [round($kiri + $i * $lebar / max($n - 1, 1), 1), round($atas + $tinggi * (1 - $v / $maks), 1), $bln, $v];
        $i++;
    }
    $garis = 'M ' . $pts[0][0] . ',' . $pts[0][1];
    for ($k = 1; $k < $n; $k++) {
        $xm = round(($pts[$k - 1][0] + $pts[$k][0]) / 2, 1);
        $garis .= ' C ' . $xm . ',' . $pts[$k - 1][1] . ' ' . $xm . ',' . $pts[$k][1] . ' ' . $pts[$k][0] . ',' . $pts[$k][1];
    }
    $dasar = $atas + $tinggi;
    $area  = $garis . ' L ' . $pts[$n - 1][0] . ',' . $dasar . ' L ' . $pts[0][0] . ',' . $dasar . ' Z';
    $periode = array_key_first($tren) . ' – ' . array_key_last($tren);
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .ad {
        --surface: #ffffff; --border: #e6e3d8; --text: #1f2a24; --muted: #6b756f; --head: #17402f;
        --tint: #eef3ec; --chip: #f4f2ea; --beige: #f2e8d5; --gold: #a98532; --accent: #12b886;
        --ok-bg: #e3f1e8; --ok-tx: #17603f; --wait-bg: #faecc9; --wait-tx: #8a5a0a; --bad-bg: #fbe3e3; --bad-tx: #a02b2b;
        font-family: 'Poppins', system-ui, -apple-system, sans-serif;
        color: var(--text); display: flex; flex-direction: column; gap: 22px; padding-bottom: 12px;
    }
    .dark .ad {
        --surface: #14201a; --border: #26362d; --text: #e8efe9; --muted: #9db0a5; --head: #e8efe9;
        --tint: #1a2b23; --chip: #1c2c24; --beige: #2a2619; --gold: #d3b265; --accent: #2fe0a0;
        --ok-bg: #12382a; --ok-tx: #6ee0ae; --wait-bg: #3a2f12; --wait-tx: #f0c36a; --bad-bg: #3d1c1e; --bad-tx: #f08c8c;
    }
    .ad *, .ad *::before, .ad *::after { box-sizing: border-box; }
    .ad a { text-decoration: none; }
    .ad h1, .ad h3, .ad h4, .ad h5, .ad p { margin: 0; }

    .ad-card { background: var(--surface); border: 1px solid var(--border); border-radius: 18px; }

    .ad-ico {
        --fc: #12a67a; width: 40px; height: 40px; border-radius: 12px; flex: none; display: grid; place-items: center;
        font-size: 16px; color: var(--fc); background: color-mix(in srgb, var(--fc) 14%, transparent);
        transition: all .25s ease;
    }
    .ad-ico.sm { width: 34px; height: 34px; border-radius: 10px; font-size: 14px; }

    .ad-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .ad-hello { font-size: clamp(22px, 3.4vw, 30px); font-weight: 600; color: var(--head); line-height: 1.25; }
    .ad-hello span { color: var(--accent); }
    .ad-sub { font-size: 13px; color: var(--muted); margin-top: 6px; }
    .ad-chips { display: flex; gap: 10px; flex-wrap: wrap; }
    .ad-chip {
        display: inline-flex; align-items: center; gap: 9px; font-size: 12px; font-weight: 500; color: var(--text); background: var(--surface);
        border: 1px solid var(--border); border-radius: 12px; padding: 10px 14px;
    }
    .ad-chip i { color: var(--accent); font-size: 13px; }

    .ad-notice {
        display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap;
        background: var(--tint); border-radius: 14px; padding: 14px 18px; font-size: 13px;
    }
    .ad-notice > span { display: flex; align-items: center; gap: 12px; }
    .ad-link { font-size: 12px; font-weight: 600; color: var(--head); display: inline-flex; align-items: center; gap: 8px; transition: gap .2s ease, color .2s ease; white-space: nowrap; }
    .ad-link:hover { gap: 12px; color: var(--accent); }
    .ad-link i { font-size: 10px; }

    .ad-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
    .ad-stat { padding: 20px 22px; display: flex; flex-direction: column; gap: 12px; transition: transform .2s ease, box-shadow .2s ease; }
    .ad-stat:hover { transform: translateY(-3px); box-shadow: 0 14px 30px rgba(23, 64, 47, .1); }
    .ad-stat-head { display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-weight: 500; color: var(--muted); }
    .ad-num { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .ad-num b { font-size: 34px; font-weight: 600; color: var(--head); line-height: 1; }
    .ad-badge {
        font-size: 11px; font-weight: 600; color: var(--fc, var(--gold)); padding: 3px 9px; border-radius: 999px;
        background: color-mix(in srgb, var(--fc, var(--gold)) 14%, transparent);
    }
    .ad-stat small { font-size: 11.5px; color: var(--muted); display: flex; align-items: center; gap: 7px; }
    .ad-stat small i { font-size: 10px; opacity: .7; }

    .ad-sec { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
    .ad-sec > div:first-child { display: flex; align-items: center; gap: 12px; }
    .ad-ptitle { font-size: 15px; font-weight: 600; color: var(--head); }
    .ad-psub { font-size: 12px; color: var(--muted); margin-top: 3px; }

    .ad-feat { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-top: 16px; }
    .ad-f { padding: 20px; display: flex; flex-direction: column; gap: 14px; transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
    .ad-f:hover { transform: translateY(-3px); box-shadow: 0 14px 30px rgba(23, 64, 47, .1); border-color: var(--fc); }
    .ad-f:hover .ad-ico { background: var(--fc); color: #fff; }
    .ad-f h4 { font-size: 14px; font-weight: 600; color: var(--head); }
    .ad-f p { font-size: 12px; color: var(--muted); line-height: 1.65; margin-top: 4px; }
    .ad-f-foot { margin-top: auto; padding-top: 14px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 10px; font-size: 12px; }
    .ad-f-foot a { font-weight: 600; display: inline-flex; align-items: center; gap: 7px; transition: color .2s ease; }
    .ad-f-foot .kelola { color: var(--fc); }
    .ad-f-foot .lihat { color: var(--muted); }
    .ad-f-foot a:hover { color: var(--accent); }
    .ad-f-foot i { font-size: 10px; }
    .ad-f-foot .note { color: var(--muted); font-size: 11px; display: inline-flex; align-items: center; gap: 6px; }

    .ad-row { display: grid; grid-template-columns: minmax(0, 1fr) 340px; gap: 22px; align-items: start; }
    .ad-col { display: flex; flex-direction: column; gap: 22px; min-width: 0; }
    .ad-panel { padding: 24px 26px; }
    .ad-period { font-size: 12px; font-weight: 500; color: var(--text); border: 1px solid var(--border); border-radius: 10px; padding: 8px 12px; white-space: nowrap; display: inline-flex; align-items: center; gap: 8px; }
    .ad-period i { color: var(--accent); }

    .ad-big { display: flex; align-items: baseline; gap: 12px; margin: 20px 0 8px; flex-wrap: wrap; }
    .ad-big b { font-size: 36px; font-weight: 600; color: var(--head); line-height: 1; }
    .ad-big span { font-size: 12px; color: var(--muted); }

    .ad-chart { width: 100%; height: auto; display: block; overflow: visible; }
    .ad-chart text { font-size: 10px; fill: var(--muted); font-family: inherit; }
    .ad-chart .grid { stroke: var(--border); stroke-width: 1; }
    .ad-chart .ln { fill: none; stroke: var(--accent); stroke-width: 2.6; stroke-linecap: round; }
    .ad-chart .dot { fill: var(--surface); stroke: var(--accent); stroke-width: 2; }
    .ad-chart .dot.last { fill: var(--accent); }

    .ad-seg { display: flex; gap: 4px; height: 10px; margin: 20px 0 18px; }
    .ad-seg span { border-radius: 4px; }
    .ad-leg { display: flex; flex-direction: column; gap: 14px; }
    .ad-leg > div { display: flex; align-items: center; justify-content: space-between; gap: 10px; font-size: 13px; color: var(--muted); }
    .ad-leg > div > span { display: flex; align-items: center; gap: 10px; }
    .ad-leg b { color: var(--text); font-weight: 600; font-size: 12.5px; }

    .ad-table-head { padding: 22px 26px 18px; display: flex; justify-content: space-between; align-items: center; gap: 14px; }
    .ad-table-head > div:first-child { display: flex; align-items: center; gap: 12px; }
    .ad-cols, .ad-line {
        display: grid; grid-template-columns: minmax(0, 2.2fr) minmax(0, 1fr) minmax(0, 1.3fr) 120px; gap: 14px; align-items: center; padding: 0 26px;
    }
    .ad-cols { background: var(--chip); height: 40px; font-size: 10.5px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .ad-line { min-height: 64px; border-top: 1px solid var(--border); font-size: 12.5px; color: var(--muted); transition: background .2s ease; }
    .ad-line:hover { background: var(--chip); }
    .ad-line .c-jenjang, .ad-line .c-asal { display: flex; align-items: center; gap: 8px; min-width: 0; }
    .ad-line .c-jenjang i, .ad-line .c-asal i { color: var(--accent); font-size: 11px; flex: none; }
    .ad-who { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .ad-av {
        width: 38px; height: 38px; border-radius: 50%; background: var(--tint); color: var(--head); font-size: 11.5px; font-weight: 600;
        display: grid; place-items: center; flex: none;
    }
    .ad-who h4 { font-size: 13px; font-weight: 600; color: var(--text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ad-who small { font-size: 11px; color: var(--muted); display: block; }
    .ad-who .mob { display: none; }
    .ad-pill { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; padding: 5px 11px; border-radius: 999px; justify-self: start; }
    .ad-pill i { font-size: 10px; }
    .ad-pill.ok { background: var(--ok-bg); color: var(--ok-tx); }
    .ad-pill.wait { background: var(--wait-bg); color: var(--wait-tx); }
    .ad-pill.bad { background: var(--bad-bg); color: var(--bad-tx); }
    .ad-empty { text-align: center; padding: 30px; font-size: 12.5px; color: var(--muted); border-top: 1px solid var(--border); }
    .ad-empty i { display: block; font-size: 26px; color: var(--border); margin-bottom: 10px; }
    .ad-foot { padding: 14px 26px; border-top: 1px solid var(--border); font-size: 11.5px; color: var(--muted); display: flex; align-items: center; gap: 8px; }

    .ad-agenda { display: flex; flex-direction: column; gap: 18px; margin-top: 20px; }
    .ad-ag { display: flex; align-items: center; gap: 14px; }
    .ad-date { width: 46px; height: 50px; border-radius: 12px; background: var(--chip); display: flex; flex-direction: column; align-items: center; justify-content: center; flex: none; }
    .ad-date b { font-size: 18px; font-weight: 600; color: var(--head); line-height: 1; }
    .ad-date small { font-size: 9px; font-weight: 500; color: var(--muted); margin-top: 3px; letter-spacing: .06em; }
    .ad-ag h5 { font-size: 13px; font-weight: 600; color: var(--text); }
    .ad-ag p { font-size: 11px; color: var(--muted); margin-top: 4px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .ad-ag p span { display: inline-flex; align-items: center; gap: 5px; }
    .ad-ag p i { font-size: 10px; color: var(--accent); }
    .ad-none { font-size: 12px; color: var(--muted); margin-top: 18px; }

    .ad-help { background: var(--beige); border-radius: 18px; padding: 20px 22px; display: flex; gap: 14px; align-items: flex-start; }
    .ad-help h5 { font-size: 14px; font-weight: 600; color: var(--text); }
    .ad-help p { font-size: 12px; color: var(--muted); margin-top: 4px; line-height: 1.65; }

    .ad-quick { padding: 18px 26px; display: flex; align-items: center; justify-content: space-between; gap: 18px; flex-wrap: wrap; }
    .ad-quick > div:first-child { display: flex; align-items: center; gap: 12px; }
    .ad-quick-btns { display: flex; gap: 12px; flex-wrap: wrap; }
    .ad-btn {
        display: inline-flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: var(--head);
        background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 12px 18px; transition: all .2s ease;
    }
    .ad-btn:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-1px); }
    .ad-btn.main { background: var(--head); border-color: var(--head); color: #fff; }
    .ad-btn.main:hover { background: var(--accent); border-color: var(--accent); color: #fff; }
    .dark .ad-btn.main { background: var(--accent); border-color: var(--accent); color: #0f2018; }
    .ad-btn i { font-size: 13px; }

    @media (max-width: 1200px) { .ad-feat { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 1100px) {
        .ad-stats { grid-template-columns: repeat(2, 1fr); }
        .ad-row { grid-template-columns: 1fr; }
    }
    @media (max-width: 720px) {
        .ad { gap: 16px; }
        .ad-stats { gap: 12px; }
        .ad-stat { padding: 16px; }
        .ad-num b { font-size: 28px; }
        .ad-panel { padding: 20px 18px; }
        .ad-table-head { padding: 18px; }
        .ad-cols { display: none; }
        .ad-line { grid-template-columns: minmax(0, 1fr) auto; padding: 12px 18px; }
        .ad-line .c-jenjang, .ad-line .c-asal { display: none; }
        .ad-who .mob { display: block; }
        .ad-foot { padding: 14px 18px; }
        .ad-quick { padding: 18px; }
        .ad-quick-btns { width: 100%; }
        .ad-btn { flex: 1; justify-content: center; }
    }
    @media (max-width: 560px) { .ad-feat { grid-template-columns: 1fr; } }
    @media (max-width: 420px) { .ad-stats { grid-template-columns: 1fr; } }
</style>

<div class="ad">

    {{-- ===================== SAPAAN ===================== --}}
    <div class="ad-top">
        <div>
            <h1 class="ad-hello">Assalamu’alaikum, <span>{{ $namaDepan }}</span> <i class="fa-solid fa-hands-praying" style="font-size:.7em;color:var(--gold)"></i></h1>
            <p class="ad-sub">Berikut ringkasan aktivitas dan perkembangan pesantren hari ini.</p>
        </div>
        <div class="ad-chips">
            <span class="ad-chip"><i class="fa-solid fa-graduation-cap"></i> Tahun Ajaran {{ $tahunAjaran }}</span>
            <span class="ad-chip"><i class="fa-regular fa-calendar"></i> {{ $tanggal }}</span>
        </div>
    </div>

    {{-- ===================== PEMBERITAHUAN ===================== --}}
    <div class="ad-notice">
        <span>
            <span class="ad-ico sm" style="--fc:#12a67a"><i class="fa-solid fa-clipboard-check"></i></span>
            @if ($pendaftarMenunggu > 0)
                Ada {{ $pendaftarMenunggu }} pendaftaran yang menunggu verifikasi. Mari bantu calon santri melanjutkan langkahnya.
            @else
                Semua pendaftaran sudah diverifikasi.
            @endif
        </span>
        <a href="{{ route('admin.pendaftaran.index') }}" class="ad-link">Tinjau Pendaftaran <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    {{-- ===================== STATISTIK ===================== --}}
    <div class="ad-stats">
        @foreach ($stat as $s)
            <div class="ad-card ad-stat" style="--fc: {{ $s[5] }}">
                <div class="ad-stat-head"><span>{{ $s[0] }}</span><span class="ad-ico"><i class="fa-solid {{ $s[2] }}"></i></span></div>
                <div class="ad-num"><b>{{ number_format($s[1], 0, ',', '.') }}</b><span class="ad-badge">{{ $s[3] }}</span></div>
                <small><i class="fa-solid fa-circle"></i>{{ $s[4] }}</small>
            </div>
        @endforeach
    </div>

    {{-- ===================== FITUR WEBSITE ===================== --}}
    <div>
        <div class="ad-sec">
            <div>
                <span class="ad-ico" style="--fc:#12a67a"><i class="fa-solid fa-globe"></i></span>
                <div>
                    <h3 class="ad-ptitle">Fitur Website</h3>
                    <p class="ad-psub">Halaman yang tampil di website, sesuai menu navigasi pesantren</p>
                </div>
            </div>
            <a href="{{ url('/') }}" target="_blank" rel="noopener noreferrer" class="ad-link">Buka website <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        </div>

        <div class="ad-feat">
            @foreach ($fitur as $f)
                <div class="ad-card ad-f" style="--fc: {{ $f[2] }}">
                    <div style="display:flex;gap:14px;align-items:flex-start">
                        <span class="ad-ico"><i class="fa-solid {{ $f[1] }}"></i></span>
                        <div>
                            <h4>{{ $f[0] }}
                                @if (!empty($f[6]))
                                    <span class="ad-badge" style="margin-left:6px">{{ $f[6] }}</span>
                                @endif
                            </h4>
                            <p>{{ $f[3] }}</p>
                        </div>
                    </div>
                    <div class="ad-f-foot">
                        @if ($f[4])
                            <a href="{{ $f[4] }}" class="kelola"><i class="fa-solid fa-sliders"></i> Kelola</a>
                        @else
                            <span class="note"><i class="fa-regular fa-eye"></i> Halaman publik</span>
                        @endif
                        @if ($f[5])
                            <a href="{{ $f[5] }}" target="_blank" rel="noopener noreferrer" class="lihat">Lihat <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ===================== GRAFIK + KOMPOSISI ===================== --}}
    <div class="ad-row">

        <div class="ad-card ad-panel">
            <div class="ad-sec">
                <div>
                    <span class="ad-ico" style="--fc:#12a67a"><i class="fa-solid fa-chart-line"></i></span>
                    <div>
                        <h3 class="ad-ptitle">Statistik Tren Pendaftar Santri</h3>
                        <p class="ad-psub">Jumlah calon santri baru per bulan</p>
                    </div>
                </div>
                <span class="ad-period"><i class="fa-regular fa-calendar-check"></i> 6 bulan terakhir ({{ $periode }})</span>
            </div>

            <div class="ad-big"><b>{{ max($tren) }}</b><span>puncak pendaftar masuk per bulan</span></div>

            <svg class="ad-chart" viewBox="0 0 {{ $W }} {{ $H }}" role="img" aria-label="Grafik tren pendaftar per bulan">
                <defs>
                    <linearGradient id="adGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#12b886" stop-opacity=".22" />
                        <stop offset="100%" stop-color="#12b886" stop-opacity="0" />
                    </linearGradient>
                </defs>

                @for ($g = 0; $g <= 5; $g++)
                    @php $gy = round($atas + $tinggi * (1 - $g / 5), 1); @endphp
                    <line class="grid" x1="{{ $kiri }}" x2="{{ $W - $kanan }}" y1="{{ $gy }}" y2="{{ $gy }}" />
                    <text x="{{ $kiri - 10 }}" y="{{ $gy + 3 }}" text-anchor="end">{{ round($maks / 5 * $g) }}</text>
                @endfor

                <path d="{{ $area }}" fill="url(#adGrad)" />
                <path d="{{ $garis }}" class="ln" />

                @foreach ($pts as $idx => $p)
                    <circle class="dot {{ $idx === $n - 1 ? 'last' : '' }}" cx="{{ $p[0] }}" cy="{{ $p[1] }}" r="{{ $idx === $n - 1 ? 4.5 : 3.5 }}" />
                    <text x="{{ $p[0] }}" y="{{ $H - 8 }}" text-anchor="middle">{{ $p[2] }}</text>
                @endforeach
            </svg>
        </div>

        <div class="ad-card ad-panel">
            <div class="ad-sec" style="flex-wrap:nowrap">
                <div>
                    <span class="ad-ico" style="--fc:#c08a1c"><i class="fa-solid fa-chart-pie"></i></span>
                    <div>
                        <h3 class="ad-ptitle">Komposisi Santri Aktif</h3>
                        <p class="ad-psub">Berdasarkan tingkat jenjang</p>
                    </div>
                </div>
            </div>

            <div class="ad-big" style="margin-top:24px"><b>{{ number_format($totalSantri, 0, ',', '.') }}</b><span>total santri</span></div>

            @if (count($komposisi))
                <div class="ad-seg">
                    @foreach ($komposisi as $k)
                        <span style="width: {{ $k[1] }}%; background: {{ $k[3] }}"></span>
                    @endforeach
                </div>

                <div class="ad-leg">
                    @foreach ($komposisi as $k)
                        <div>
                            <span><span class="ad-ico sm" style="--fc: {{ $k[3] }}; width:30px; height:30px; font-size:12px"><i class="fa-solid {{ $k[4] }}"></i></span>{{ $k[0] }}</span>
                            <b>{{ $k[1] }}% ({{ $k[2] }})</b>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="ad-none">Belum ada data santri.</p>
            @endif
        </div>
    </div>

    {{-- ===================== PENDAFTAR TERBARU + AGENDA ===================== --}}
    <div class="ad-row">

        <div class="ad-card" style="overflow:hidden">
            <div class="ad-table-head">
                <div>
                    <span class="ad-ico" style="--fc:#3b82c4"><i class="fa-solid fa-user-plus"></i></span>
                    <div>
                        <h3 class="ad-ptitle">Pendaftar Terbaru</h3>
                        <p class="ad-psub">Calon santri yang baru mengisi formulir</p>
                    </div>
                </div>
                <a href="{{ route('admin.pendaftaran.index') }}" class="ad-link">Lihat semua <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <div class="ad-cols">
                <span>Nama Calon Santri</span>
                <span>Jenjang</span>
                <span>Asal Sekolah</span>
                <span>Status</span>
            </div>

            @forelse ($pendaftarTerbaru as $pendaftar)
                @php
                    $statusAsli = $pendaftar->status_pendaftaran ?: 'menunggu';
                    $kelas = match (strtolower($statusAsli)) {
                        'diterima' => 'ok',
                        'ditolak'  => 'bad',
                        default    => 'wait',
                    };
                    $ikonStatus = ['ok' => 'fa-circle-check', 'bad' => 'fa-circle-xmark', 'wait' => 'fa-clock'][$kelas];
                    $inisial = collect(preg_split('/\s+/', trim($pendaftar->nama_lengkap)))
                        ->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
                @endphp
                <div class="ad-line">
                    <div class="ad-who">
                        <span class="ad-av">{{ $inisial }}</span>
                        <div style="min-width:0">
                            <h4>{{ $pendaftar->nama_lengkap }}</h4>
                            <small>ID: {{ $pendaftar->no_pendaftaran }}</small>
                            <small class="mob">{{ $pendaftar->jenjang_tujuan }} • {{ $pendaftar->asal_sekolah }}</small>
                        </div>
                    </div>
                    <span class="c-jenjang"><i class="fa-solid fa-school"></i>{{ $pendaftar->jenjang_tujuan }}</span>
                    <span class="c-asal"><i class="fa-solid fa-building-columns"></i>{{ $pendaftar->asal_sekolah }}</span>
                    <span class="ad-pill {{ $kelas }}"><i class="fa-solid {{ $ikonStatus }}"></i>{{ ucfirst($statusAsli) }}</span>
                </div>
            @empty
                <div class="ad-empty"><i class="fa-regular fa-folder-open"></i>Belum ada data pendaftar terbaru.</div>
            @endforelse

            <div class="ad-foot"><i class="fa-solid fa-list-ul"></i> Menampilkan {{ count($pendaftarTerbaru) }} dari {{ $totalPendaftar }} pendaftar</div>
        </div>

        <div class="ad-col">
            <div class="ad-card ad-panel">
                <div class="ad-sec" style="flex-wrap:nowrap">
                    <div>
                        <span class="ad-ico" style="--fc:#8b5cc6"><i class="fa-solid fa-calendar-check"></i></span>
                        <h3 class="ad-ptitle">Agenda Terdekat</h3>
                    </div>
                </div>
                @if (count($agenda))
                    <div class="ad-agenda">
                        @foreach ($agenda as $a)
                            <div class="ad-ag">
                                <span class="ad-date"><b>{{ $a[0] }}</b><small>{{ $a[4] }}</small></span>
                                <div>
                                    <h5>{{ $a[1] }}</h5>
                                    <p><span><i class="fa-regular fa-clock"></i>{{ $a[2] }}</span><span><i class="fa-solid fa-location-dot"></i>{{ $a[3] }}</span></p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="ad-none">Belum ada agenda mendatang.</p>
                @endif
            </div>

            <div class="ad-help">
                <span class="ad-ico" style="--fc:#c08a1c"><i class="fa-regular fa-circle-question"></i></span>
                <div>
                    <h5>Pusat Bantuan</h5>
                    <p>Kendala server atau database? Hubungi tim pengembang internal.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== AKSI CEPAT ===================== --}}
    <div class="ad-card ad-quick">
        <div>
            <span class="ad-ico" style="--fc:#d6525a"><i class="fa-solid fa-bolt"></i></span>
            <div>
                <h3 class="ad-ptitle">Aksi Cepat</h3>
                <p class="ad-psub">Pintasan menu operasional sistem</p>
            </div>
        </div>
        <div class="ad-quick-btns">
            <a href="#" class="ad-btn"><i class="fa-solid fa-user-plus"></i> Tambah Data Santri</a>
            <a href="{{ $ada('admin.berita.create') ?? '#' }}" class="ad-btn"><i class="fa-regular fa-pen-to-square"></i> Tulis Berita Baru</a>
            <a href="{{ $ada('admin.galeri.create') ?? '#' }}" class="ad-btn"><i class="fa-solid fa-images"></i> Tambah Album Galeri</a>
            <a href="{{ $ada('admin.prestasi.create') ?? '#' }}" class="ad-btn"><i class="fa-solid fa-trophy"></i> Tambah Prestasi</a>
            <a href="{{ $ada('admin.struktur.create') ?? '#' }}" class="ad-btn"><i class="fa-solid fa-sitemap"></i> Tambah Struktur</a>
            <a href="{{ $ada('admin.alumni.create') ?? '#' }}" class="ad-btn"><i class="fa-solid fa-user-graduate"></i> Tambah Alumni</a>
            @if ($isSuperAdmin)
                <a href="{{ $ada('super.pengguna.create') ?? '#' }}" class="ad-btn"><i class="fa-solid fa-users-gear"></i> Tambah Pengguna</a>
            @endif
            <a href="#" class="ad-btn main"><i class="fa-solid fa-cloud-arrow-down"></i> Rekapitulasi Data</a>
        </div>
    </div>

</div>

@endsection