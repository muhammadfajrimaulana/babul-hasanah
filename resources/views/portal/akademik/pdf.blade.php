@use('App\Support\Akademik')
@use('Illuminate\Support\Carbon')
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rapor {{ $anak->nama }} - {{ $filter['tahun'] }}</title>
    <style>
        @page { margin: 24px 28px; }
        * { box-sizing: border-box; }
        body {
            font-family: "Times New Roman", Times, serif;
            color: #111827;
            font-size: 12px;
            line-height: 1.45;
            margin: 0;
        }
        .kop { text-align: center; border-bottom: 3px double #111827; padding-bottom: 10px; margin-bottom: 16px; }
        .kop .nama { font-size: 18px; font-weight: bold; letter-spacing: 0.4px; margin: 0; }
        .kop .sub { font-size: 11px; margin: 3px 0 0; color: #374151; }
        .kop .alamat { font-size: 10px; margin: 2px 0 0; color: #6b7280; }
        .judul { text-align: center; margin: 14px 0 12px; }
        .judul h1 { font-size: 14px; margin: 0; text-transform: uppercase; letter-spacing: 0.8px; }
        .judul p { font-size: 11px; margin: 3px 0 0; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        .identitas { margin-bottom: 14px; }
        .identitas td { padding: 2px 4px; vertical-align: top; font-size: 11px; }
        .identitas .label { width: 120px; color: #4b5563; }
        .identitas .titik { width: 10px; }
        .nilai th, .nilai td { border: 1px solid #9ca3af; padding: 5px 6px; font-size: 10.5px; }
        .nilai th { background: #e5e7eb; text-align: center; text-transform: uppercase; font-size: 9.5px; letter-spacing: 0.3px; }
        .nilai td.center { text-align: center; }
        .nilai td.kanan { text-align: right; }
        .nilai tr.total td { background: #f3f4f6; font-weight: bold; }
        .ringkas { margin-top: 12px; }
        .ringkas td { border: 1px solid #9ca3af; padding: 6px 8px; font-size: 10.5px; }
        .ringkas .label { background: #f3f4f6; width: 150px; }
        .catatan { margin-top: 12px; border: 1px solid #9ca3af; padding: 8px 10px; font-size: 10.5px; min-height: 46px; }
        .catatan b { display: block; font-size: 9.5px; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 3px; color: #374151; }
        .tanda { margin-top: 24px; }
        .tanda td { width: 33.33%; text-align: center; font-size: 10.5px; vertical-align: top; }
        .tanda .ruang { height: 70px; }
        .tanda .garis { border-top: 1px solid #111827; width: 82%; margin: 0 auto; padding-top: 3px; }
        .kaki { margin-top: 18px; border-top: 1px solid #d1d5db; padding-top: 6px; font-size: 8.5px; color: #6b7280; text-align: center; }
        .badge { display: inline-block; border: 1px solid #6b7280; border-radius: 8px; padding: 0 6px; font-size: 9px; }
        .kosong { text-align: center; padding: 22px; border: 1px dashed #9ca3af; color: #6b7280; font-size: 10.5px; }
    </style>
</head>

<body>
    <div class="kop">
        <p class="nama">PONDOK PESANTREN BABUL HASANAH</p>
        <p class="sub">Lembaga Pendidikan Diniyah &amp; Madrasah</p>
        <p class="alamat">Dokumen resmi rapor digital — diterbitkan melalui Portal Wali Santri</p>
    </div>

    <div class="judul">
        <h1>Rapor Hasil Belajar Santri</h1>
        <p>Tahun Ajaran {{ $filter['tahun'] }} · Semester {{ Akademik::labelSemester($filter['semester']) }}</p>
    </div>

    <table class="identitas">
        <tr>
            <td class="label">Nama Santri</td>
            <td class="titik">:</td>
            <td><b>{{ $anak->nama }}</b></td>
            <td class="label">Kelas</td>
            <td class="titik">:</td>
            <td>{{ $anak->kelas ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">NISN / Nomor Induk</td>
            <td class="titik">:</td>
            <td>{{ $anak->nis ?: '—' }}</td>
            <td class="label">Jenjang</td>
            <td class="titik">:</td>
            <td>{{ $anak->jenjang ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Wali Santri</td>
            <td class="titik">:</td>
            <td>{{ $anak->namaWali ?? auth()->user()->name }}</td>
            <td class="label">Tanggal Cetak</td>
            <td class="titik">:</td>
            <td>{{ Carbon::now()->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    @if ($rapor['mapel']->isEmpty())
        <div class="kosong">
            Belum ada nilai berstatus terbit untuk periode ini.
        </div>
    @else
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
                    <th style="width: 9%;">Nilai Akhir</th>
                    <th style="width: 8%;">Predikat</th>
                    <th style="width: 15%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rapor['mapel'] as $row)
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td>{{ $row['nama'] }}</td>
                        <td class="center">{{ Akademik::formatNilai($row['kkm']) }}</td>
                        <td class="center">{{ Akademik::formatNilai($row['tugas']) }}</td>
                        <td class="center">{{ Akademik::formatNilai($row['uts']) }}</td>
                        <td class="center">{{ Akademik::formatNilai($row['uas']) }}</td>
                        <td class="center">{{ Akademik::formatNilai($row['hafalan']) }}</td>
                        <td class="center"><b>{{ Akademik::formatNilai($row['akhir']) }}</b></td>
                        <td class="center"><span class="badge">{{ $row['predikat'] }}</span></td>
                        <td>{{ $row['tuntas'] ? 'Tuntas' : 'Belum tuntas' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="ringkas">
            <tr>
                <td class="label">Rata-rata Nilai</td>
                <td><b>{{ Akademik::formatNilai($rapor['rata']) }}</b></td>
                <td class="label">Predikat</td>
                <td><b>{{ $rapor['predikat'] }}</b></td>
                <td class="label">Mata Pelajaran</td>
                <td>{{ $rapor['jumlah'] }} mapel</td>
            </tr>
            <tr>
                <td class="label">Tuntas KKM</td>
                <td>{{ $rapor['tuntas'] }} dari {{ $rapor['jumlah'] }} mapel</td>
                <td class="label">Sumber Data</td>
                <td colspan="3">{{ $rapor['sumber'] === 'lama' ? 'Arsip nilai akademik' : 'Nilai akademik terbit' }}</td>
            </tr>
        </table>

        <div class="catatan">
            <b>Catatan Pengajar</b>
            {{ $rapor['catatan'] ?: '—' }}
        </div>
    @endif

    <table class="tanda">
        <tr>
            <td>
                Mengetahui,<br>Wali Santri
                <div class="ruang"></div>
                <div class="garis">{{ $anak->namaWali ?? auth()->user()->name }}</div>
            </td>
            <td>
                Wali Kelas
                <div class="ruang"></div>
                <div class="garis">&nbsp;</div>
            </td>
            <td>
                Kepala Madrasah
                <div class="ruang"></div>
                <div class="garis">&nbsp;</div>
            </td>
        </tr>
    </table>

    <div class="kaki">
        Dokumen dihasilkan otomatis oleh Portal Wali Santri Pondok Pesantren Babul Hasanah.
        Hanya nilai berstatus <b>Terbit</b> yang dimuat dalam rapor ini.
    </div>
</body>

</html>
