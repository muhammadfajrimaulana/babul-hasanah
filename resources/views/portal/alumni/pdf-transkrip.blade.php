@use('App\Support\Akademik')
@use('Illuminate\Support\Carbon')
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Transkrip Nilai {{ $konteks['nama'] }}</title>
    <style>
        @page { margin: 24px 26px; }
        * { box-sizing: border-box; }
        body {
            font-family: "Times New Roman", Times, serif;
            color: #111827;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
        }
        .kop { text-align: center; border-bottom: 3px double #111827; padding-bottom: 9px; margin-bottom: 12px; }
        .kop .nama { font-size: 18px; font-weight: bold; letter-spacing: 0.4px; margin: 0; }
        .kop .sub { font-size: 11px; margin: 3px 0 0; color: #374151; }
        .kop .alamat { font-size: 9.5px; margin: 2px 0 0; color: #6b7280; }
        .judul { text-align: center; margin: 12px 0 10px; }
        .judul h1 { font-size: 14px; margin: 0; text-transform: uppercase; letter-spacing: 0.9px; }
        .judul p { font-size: 10px; margin: 3px 0 0; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        .identitas { margin-bottom: 12px; }
        .identitas td { padding: 2px 4px; vertical-align: top; font-size: 10.5px; }
        .identitas .label { width: 118px; color: #4b5563; }
        .identitas .titik { width: 9px; }
        .periode { margin-top: 10px; page-break-inside: avoid; }
        .periode-judul {
            background: #eef2ff;
            border: 1px solid #9ca3af;
            border-bottom: none;
            padding: 5px 8px;
            font-size: 10.5px;
            font-weight: bold;
            color: #312e81;
        }
        .periode-judul span { float: right; font-weight: normal; color: #4b5563; }
        .nilai th, .nilai td { border: 1px solid #9ca3af; padding: 4px 5px; font-size: 9.5px; }
        .nilai th { background: #e5e7eb; text-align: center; text-transform: uppercase; font-size: 8.5px; letter-spacing: 0.25px; }
        .nilai td.center { text-align: center; }
        .nilai tr.total td { background: #f3f4f6; font-weight: bold; }
        .rekap { margin-top: 12px; }
        .rekap td { border: 1px solid #9ca3af; padding: 5px 8px; font-size: 10px; }
        .rekap .label { background: #f3f4f6; width: 140px; }
        .rekap .nilai { text-align: center; font-weight: bold; }
        .tanda { margin-top: 22px; page-break-inside: avoid; }
        .tanda td { width: 50%; text-align: center; font-size: 10.5px; vertical-align: top; }
        .tanda .ruang { height: 62px; }
        .tanda .garis { border-top: 1px solid #111827; width: 76%; margin: 0 auto; padding-top: 3px; }
        .verifikasi { margin-top: 14px; border: 1px dashed #6b7280; padding: 7px 9px; font-size: 9px; color: #374151; }
        .kaki { margin-top: 12px; border-top: 1px solid #d1d5db; padding-top: 5px; font-size: 8px; color: #6b7280; text-align: center; }
        .kosong { text-align: center; padding: 20px; border: 1px dashed #9ca3af; color: #6b7280; font-size: 10px; }
    </style>
</head>

<body>
    <div class="kop">
        <p class="nama">PONDOK PESANTREN BABUL HASANAH</p>
        <p class="sub">Lembaga Pendidikan Diniyah &amp; Madrasah</p>
        <p class="alamat">Dokumen resmi transkrip digital — diterbitkan melalui Portal Alumni</p>
    </div>

    <div class="judul">
        <h1>Transkrip Nilai Lengkap</h1>
        <p>Nomor Ijazah: {{ $nomorIjazah }}</p>
    </div>

    <table class="identitas">
        <tr>
            <td class="label">Nama Alumni</td>
            <td class="titik">:</td>
            <td><b>{{ $konteks['nama'] }}</b></td>
            <td class="label">NIS / Nomor Induk</td>
            <td class="titik">:</td>
            <td>{{ $konteks['nis'] }}</td>
        </tr>
        <tr>
            <td class="label">Jenjang</td>
            <td class="titik">:</td>
            <td>{{ $konteks['jenjang'] ?: '—' }}</td>
            <td class="label">Tahun Lulus</td>
            <td class="titik">:</td>
            <td>{{ $konteks['tahun_lulus'] ?: '—' }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td class="titik">:</td>
            <td>{{ $konteks['email'] ?: '—' }}</td>
            <td class="label">Tanggal Cetak</td>
            <td class="titik">:</td>
            <td>{{ Carbon::now()->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    @if ($transkrip['periode']->isEmpty())
        <div class="kosong">
            Belum ada nilai berstatus terbit pada akun alumni ini.
        </div>
    @else
        @foreach ($transkrip['periode'] as $blok)
            <div class="periode">
                <div class="periode-judul">
                    Tahun Ajaran {{ $blok['tahun'] }} — Semester {{ Akademik::labelSemester($blok['semester']) }}
                    <span>{{ count($blok['mapel']) }} mata pelajaran · rata {{ Akademik::formatNilai($blok['rata']) }} ({{ $blok['predikat'] }})</span>
                </div>
                <table class="nilai">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 27%;">Mata Pelajaran</th>
                            <th style="width: 7%;">KKM</th>
                            <th style="width: 7%;">Tugas</th>
                            <th style="width: 7%;">UTS</th>
                            <th style="width: 7%;">UAS</th>
                            <th style="width: 8%;">Hafalan</th>
                            <th style="width: 9%;">Akhir</th>
                            <th style="width: 8%;">Predikat</th>
                            <th style="width: 15%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($blok['mapel'] as $mapel)
                            <tr>
                                <td class="center">{{ $loop->iteration }}</td>
                                <td>{{ $mapel['nama'] }}</td>
                                <td class="center">{{ Akademik::formatNilai($mapel['kkm']) }}</td>
                                <td class="center">{{ Akademik::formatNilai($mapel['tugas']) }}</td>
                                <td class="center">{{ Akademik::formatNilai($mapel['uts']) }}</td>
                                <td class="center">{{ Akademik::formatNilai($mapel['uas']) }}</td>
                                <td class="center">{{ Akademik::formatNilai($mapel['hafalan']) }}</td>
                                <td class="center"><b>{{ Akademik::formatNilai($mapel['akhir']) }}</b></td>
                                <td class="center">{{ $mapel['predikat'] }}</td>
                                <td>{{ $mapel['tuntas'] ? 'Tuntas' : 'Belum tuntas' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <table class="rekap">
            <tr>
                <td class="label">Rata-rata Keseluruhan</td>
                <td class="nilai">{{ Akademik::formatNilai($transkrip['rata']) }}</td>
                <td class="label">Predikat</td>
                <td class="nilai">{{ $transkrip['predikat'] }}</td>
            </tr>
            <tr>
                <td class="label">Jumlah Mata Pelajaran</td>
                <td class="nilai">{{ $transkrip['jumlah_mapel'] }}</td>
                <td class="label">Jumlah Periode</td>
                <td class="nilai">{{ $transkrip['jumlah_periode'] }} semester</td>
            </tr>
            <tr>
                <td class="label">Sumber Data</td>
                <td colspan="3">
                    {{ $transkrip['sumber'] === 'lama' ? 'Arsip nilai akademik pesantren' : 'Nilai akademik terbit (published)' }}
                    @if ($transkrip['terakhir'])
                        · terakhir diterbitkan {{ Carbon::parse($transkrip['terakhir'])->translatedFormat('d F Y') }}
                    @endif
                </td>
            </tr>
        </table>
    @endif

    <table class="tanda">
        <tr>
            <td>
                Mengetahui,<br>Kepala Madrasah
                <div class="ruang"></div>
                <div class="garis">&nbsp;</div>
            </td>
            <td>
                {{ Carbon::now()->translatedFormat('d F Y') }}<br>Bagian Akademik
                <div class="ruang"></div>
                <div class="garis">&nbsp;</div>
            </td>
        </tr>
    </table>

    <div class="verifikasi">
        <b>Kode verifikasi dokumen:</b> {{ $kodeVerifikasi }}<br>
        Dokumen ini sah tanpa tanda tangan basah. Keaslian dapat diverifikasi oleh sekretariat
        Pondok Pesantren Babul Hasanah dengan menyebutkan nomor ijazah dan kode verifikasi di atas.
    </div>

    <div class="kaki">
        Dihasilkan otomatis oleh Portal Alumni Pondok Pesantren Babul Hasanah.
        Hanya nilai berstatus <b>Terbit</b> yang dimuat dalam transkrip ini.
    </div>
</body>

</html>
