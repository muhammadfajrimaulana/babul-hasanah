@use('App\Support\Akademik')
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor Nilai - {{ $anak->nama_lengkap }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111827;
            margin: 0;
            padding: 32px;
            background: #f1f5f9;
        }
        .sheet {
            max-width: 820px;
            margin: 0 auto;
            background: #fff;
            padding: 36px 40px;
            border: 1px solid #e2e8f0;
        }
        .kop {
            text-align: center;
            border-bottom: 3px double #111827;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .kop h1 { margin: 0; font-size: 18px; letter-spacing: 0.5px; }
        .kop p { margin: 2px 0 0; font-size: 11px; color: #475569; }
        .identitas { display: flex; justify-content: space-between; gap: 24px; font-size: 12px; margin-bottom: 18px; }
        .identitas table { border-collapse: collapse; }
        .identitas td { padding: 2px 6px 2px 0; vertical-align: top; }
        .identitas td:first-child { color: #475569; width: 96px; }
        table.nilai { width: 100%; border-collapse: collapse; font-size: 12px; }
        table.nilai th, table.nilai td { border: 1px solid #94a3b8; padding: 6px 8px; }
        table.nilai th { background: #e2e8f0; text-align: center; font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.4px; }
        table.nilai td.center { text-align: center; }
        .ringkas { display: flex; gap: 12px; margin: 18px 0; font-size: 12px; }
        .ringkas div { flex: 1; border: 1px solid #cbd5e1; padding: 8px 12px; }
        .ringkas span { display: block; font-size: 10px; color: #475569; text-transform: uppercase; letter-spacing: 0.4px; }
        .ringkas strong { font-size: 16px; }
        .catatan { margin-top: 14px; font-size: 11px; border: 1px dashed #94a3b8; padding: 8px 10px; }
        .ttd { margin-top: 40px; display: flex; justify-content: space-between; font-size: 12px; text-align: center; }
        .ttd div { width: 220px; }
        .ttd .ruang { height: 68px; }
        .toolbar { max-width: 820px; margin: 0 auto 14px; display: flex; justify-content: flex-end; gap: 8px; font-family: system-ui, -apple-system, sans-serif; }
        .toolbar button, .toolbar a { border: 0; border-radius: 8px; padding: 9px 16px; font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: none; }
        .toolbar .cetak { background: #047857; color: #fff; }
        .toolbar .tutup { background: #e2e8f0; color: #334155; }
        @media print {
            body { background: #fff; padding: 0; }
            .sheet { border: 0; max-width: none; padding: 0; }
            .toolbar { display: none; }
            @page { size: A4; margin: 18mm 16mm; }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <a href="{{ route('portal.nilai', ['anak' => $anak->id, 'tahun_ajaran' => $filter['tahun'], 'semester' => $filter['semester']]) }}" class="tutup">Kembali</a>
        <button type="button" class="cetak" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <div class="sheet">
        <div class="kop">
            <h1>PONDOK PESANTREN BABUL HASANAH</h1>
            <p>Rapor Nilai Santri · Portal Wali Santri</p>
        </div>

        <div class="identitas">
            <table>
                <tr><td>Nama Santri</td><td>: <strong>{{ $anak->nama_lengkap }}</strong></td></tr>
                <tr><td>NIS / Nomor Induk</td><td>: {{ $anak->nomor_induk }}</td></tr>
                <tr><td>Wali Santri</td><td>: {{ $anak->nama_wali ?? auth()->user()->name }}</td></tr>
            </table>
            <table>
                <tr><td>Jenjang</td><td>: {{ $anak->jenjang_pendidikan }}</td></tr>
                <tr><td>Kelas</td><td>: {{ $anak->kelas ?? '—' }}</td></tr>
                <tr><td>Periode</td><td>: {{ $filter['tahun'] }} — {{ Akademik::labelSemester($filter['semester']) }}</td></tr>
            </table>
        </div>

        <table class="nilai">
            <thead>
                <tr>
                    <th style="width: 34px;">No</th>
                    <th>Mata Pelajaran</th>
                    <th style="width: 52px;">Tugas</th>
                    <th style="width: 46px;">UTS</th>
                    <th style="width: 46px;">UAS</th>
                    <th style="width: 60px;">Hafalan</th>
                    <th style="width: 56px;">Akhir</th>
                    <th style="width: 48px;">Huruf</th>
                    <th style="width: 56px;">Ket.</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['mapel'] as $row)
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td>{{ $row['mapel']?->nama ?? 'Mata pelajaran terhapus' }}</td>
                        <td class="center">{{ Akademik::formatNilai($row['tugas']) }}</td>
                        <td class="center">{{ Akademik::formatNilai($row['uts']) }}</td>
                        <td class="center">{{ Akademik::formatNilai($row['uas']) }}</td>
                        <td class="center">{{ Akademik::formatNilai($row['hafalan']) }}</td>
                        <td class="center"><strong>{{ Akademik::formatNilai($row['akhir']) }}</strong></td>
                        <td class="center">{{ $row['predikat'] }}</td>
                        <td class="center">{{ $row['tuntas'] ? 'Tuntas' : 'Belum' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="center">Belum ada nilai yang diterbitkan pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="ringkas">
            <div><span>Rata-rata</span><strong>{{ Akademik::formatNilai($data['rata']) }}</strong></div>
            <div><span>Predikat</span><strong>{{ $data['predikat'] }}</strong></div>
            <div><span>Mata Pelajaran</span><strong>{{ $data['jumlah'] }}</strong></div>
            <div><span>Tuntas KKM</span><strong>{{ $data['tuntas'] }}/{{ $data['jumlah'] }}</strong></div>
        </div>

        @if ($data['catatan'])
            <div class="catatan"><strong>Catatan Pengajar:</strong> {{ $data['catatan'] }}</div>
        @endif

        <div class="catatan">
            Rapor ini dihasilkan otomatis oleh sistem dan hanya memuat nilai yang sudah diterbitkan.
        </div>

        <div class="ttd">
            <div>
                <p>Wali Kelas</p>
                <div class="ruang"></div>
                <p>(....................................)</p>
            </div>
            <div>
                <p>Kepala Madrasah / Pengasuh</p>
                <div class="ruang"></div>
                <p>(....................................)</p>
            </div>
        </div>
    </div>
</body>

</html>
