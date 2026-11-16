@use('Illuminate\Support\Carbon')
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Ijazah Digital {{ $konteks['nama'] }}</title>
    <style>
        @page { margin: 20px; }
        * { box-sizing: border-box; }
        body {
            font-family: "Times New Roman", Times, serif;
            color: #111827;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
        }
        .bingkai {
            border: 6px double #065f46;
            outline: 1px solid #065f46;
            outline-offset: 4px;
            padding: 26px 34px 22px;
            min-height: 1000px;
            position: relative;
        }
        .kop { text-align: center; }
        .kop .nama { font-size: 19px; font-weight: bold; letter-spacing: 0.5px; margin: 0; color: #065f46; }
        .kop .sub { font-size: 11px; margin: 3px 0 0; color: #374151; }
        .kop .alamat { font-size: 9.5px; margin: 2px 0 0; color: #6b7280; }
        .garis-kop { border-top: 2px solid #065f46; margin: 10px 0 18px; }
        .judul { text-align: center; margin: 18px 0 6px; }
        .judul h1 { font-size: 26px; letter-spacing: 6px; margin: 0; text-transform: uppercase; color: #065f46; }
        .judul p { font-size: 10.5px; margin: 4px 0 0; color: #4b5563; letter-spacing: 1.4px; text-transform: uppercase; }
        .nomor { text-align: center; font-size: 10.5px; color: #374151; margin-bottom: 22px; }
        .pembuka { text-align: center; font-size: 11.5px; color: #374151; margin: 16px 0 4px; }
        .nama-alumni {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #111827;
            margin: 6px 0 2px;
            letter-spacing: 0.5px;
        }
        .identitas {
            width: 66%;
            margin: 0 auto 16px;
            border-collapse: collapse;
        }
        .identitas td { padding: 3px 4px; font-size: 11px; vertical-align: top; }
        .identitas .label { width: 116px; color: #4b5563; }
        .identitas .titik { width: 10px; }
        .pernyataan {
            text-align: center;
            font-size: 11.5px;
            color: #374151;
            margin: 18px auto 6px;
            max-width: 86%;
        }
        .predikat {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 1.5px;
            color: #065f46;
            margin: 4px 0 0;
            text-transform: uppercase;
        }
        .catatan {
            margin: 22px auto 0;
            max-width: 88%;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            padding: 8px 12px;
            font-size: 9.5px;
            color: #374151;
            text-align: center;
        }
        .tanda { width: 100%; margin-top: 30px; }
        .tanda td { width: 50%; text-align: center; font-size: 11px; vertical-align: top; }
        .tanda .ruang { height: 64px; }
        .tanda .garis { border-top: 1px solid #111827; width: 68%; margin: 0 auto; padding-top: 3px; }
        .verifikasi {
            margin-top: 26px;
            border: 1px dashed #6b7280;
            padding: 8px 11px;
            font-size: 9px;
            color: #374151;
        }
        .kaki { margin-top: 12px; text-align: center; font-size: 8.5px; color: #6b7280; }
        .cap {
            position: absolute;
            right: 54px;
            bottom: 168px;
            width: 92px;
            height: 92px;
            border: 3px double #065f46;
            border-radius: 50%;
            transform: rotate(-12deg);
            color: #065f46;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1px;
            padding-top: 28px;
            opacity: 0.75;
        }
    </style>
</head>

<body>
    @php
        $tanggalLulus = $konteks['student']?->graduated_at
            ? Carbon::parse($konteks['student']->graduated_at)
            : ($konteks['tahun_lulus'] ? Carbon::create((int) $konteks['tahun_lulus'], 6, 30) : Carbon::now());
    @endphp

    <div class="bingkai">
        <div class="kop">
            <p class="nama">PONDOK PESANTREN BABUL HASANAH</p>
            <p class="sub">Lembaga Pendidikan Diniyah &amp; Madrasah</p>
            <p class="alamat">Dokumen resmi ijazah digital — diterbitkan melalui Portal Alumni</p>
        </div>

        <div class="garis-kop"></div>

        <div class="judul">
            <h1>Ijazah</h1>
            <p>Digital · Transkrip Kelulusan Santri</p>
        </div>

        <div class="nomor">Nomor: {{ $nomorIjazah }}</div>

        <p class="pembuka">Yang bertanda tangan di bawah ini menerangkan bahwa:</p>

        <p class="nama-alumni">{{ $konteks['nama'] }}</p>

        <table class="identitas">
            <tr>
                <td class="label">NIS / Nomor Induk</td>
                <td class="titik">:</td>
                <td><b>{{ $konteks['nis'] }}</b></td>
            </tr>
            <tr>
                <td class="label">Jenjang</td>
                <td class="titik">:</td>
                <td>{{ $konteks['jenjang'] ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Tempat / Tanggal Lahir</td>
                <td class="titik">:</td>
                <td>—</td>
            </tr>
            <tr>
                <td class="label">Tahun Lulus</td>
                <td class="titik">:</td>
                <td>{{ $konteks['tahun_lulus'] ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td class="titik">:</td>
                <td>{{ $konteks['email'] ?: '—' }}</td>
            </tr>
        </table>

        <p class="pernyataan">
            Berdasarkan hasil penilaian akademik dan dinyatakan
            <b>LULUS</b> dari jenjang {{ $konteks['jenjang'] ?: 'pendidikan' }}
            Pondok Pesantren Babul Hasanah pada tanggal
            {{ $tanggalLulus->translatedFormat('d F Y') }}.
        </p>

        <p class="predikat">Lulus</p>

        <div class="catatan">
            Ijazah digital ini diterbitkan berdasarkan status kelulusan yang tercatat pada sistem akademik
            pesantren. Transkrip nilai lengkap dapat diunduh melalui Portal Alumni.
        </div>

        <div class="cap">
            PONDOK<br>PESANTREN<br>BABUL HASANAH
        </div>

        <table class="tanda">
            <tr>
                <td>
                    Mengetahui,<br>Kepala Madrasah
                    <div class="ruang"></div>
                    <div class="garis">&nbsp;</div>
                </td>
                <td>
                    {{ $tanggalLulus->translatedFormat('d F Y') }}<br>Kepala Pesantren
                    <div class="ruang"></div>
                    <div class="garis">&nbsp;</div>
                </td>
            </tr>
        </table>

        <div class="verifikasi">
            <b>Kode verifikasi dokumen:</b> {{ $kodeVerifikasi }}<br>
            Keaslian ijazah digital ini dapat diverifikasi oleh sekretariat Pondok Pesantren Babul Hasanah
            dengan menyebutkan nomor ijazah dan kode verifikasi di atas.
        </div>

        <div class="kaki">
            Dihasilkan otomatis oleh Portal Alumni Pondok Pesantren Babul Hasanah.
            Dokumen berlaku sebagai ijazah digital internal pesantren.
        </div>
    </div>
</body>

</html>
