<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>Prestasi Baru Ananda</title>
</head>
<body style="margin:0; padding:0; background-color:#f1eee6; font-family:'Segoe UI', Tahoma, Arial, sans-serif; color:#1f2a24; -webkit-text-size-adjust:100%;">

    <div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">
        Kabar baik: ananda {{ $namaSantri }} meraih prestasi {{ $judul }}.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f1eee6; padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                       style="max-width:600px; background-color:#ffffff; border-radius:18px; overflow:hidden; border:1px solid #e4e0d4;">

                    {{-- Kepala surat --}}
                    <tr>
                        <td style="background-color:#17402f; padding:26px 32px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td valign="middle" width="46">
                                        <div style="width:44px; height:44px; border-radius:12px; background-color:#235743; text-align:center;">
                                            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo Babul Hasanah"
                                                 width="32" height="32"
                                                 style="display:block; margin:6px auto; border:0; outline:none; text-decoration:none;">
                                        </div>
                                    </td>
                                    <td valign="middle" style="padding-left:12px;">
                                        <div style="font-size:10.5px; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:#a9c4b6;">
                                            Pondok Pesantren
                                        </div>
                                        <div style="font-size:18px; font-weight:700; color:#ffffff; line-height:1.3;">
                                            Babul Hasanah
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Isi --}}
                    <tr>
                        <td style="padding:34px 32px 8px;">
                            <span style="display:inline-block; padding:6px 12px; border-radius:999px; background-color:#fdf6e3; color:#9a6416; font-size:11px; font-weight:700; letter-spacing:.04em;">
                                PRESTASI BARU
                            </span>

                            <p style="margin:18px 0 16px; font-size:15px; line-height:1.7; color:#1f2a24;">
                                Assalamu&rsquo;alaikum warahmatullahi wabarakatuh,
                            </p>

                            <p style="margin:0 0 16px; font-size:14px; line-height:1.8; color:#3c4741;">
                                Bapak/Ibu <strong style="color:#17402f;">{{ $namaWali }}</strong>, kami kabarkan
                                bahwa ananda <strong style="color:#17402f;">{{ $namaSantri }}</strong>
                                ({{ $jenjang }} &mdash; Kelas {{ $kelas }}) telah meraih prestasi:
                            </p>

                            {{-- Kotak prestasi --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                   style="background-color:#fbfaf6; border:1px solid #e4e0d4; border-radius:14px; margin-bottom:22px;">
                                <tr>
                                    <td style="padding:20px;">
                                        <div style="font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#b7791f; margin-bottom:8px;">
                                            {{ $label }} &middot; Tingkat {{ $tingkat }}
                                        </div>
                                        <div style="font-size:17px; font-weight:700; color:#17402f; line-height:1.4; margin-bottom:10px;">
                                            {{ $judul }}
                                        </div>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="96" valign="top" style="font-size:12.5px; color:#6b756f; padding-bottom:5px;">Bidang</td>
                                                <td valign="top" style="font-size:13px; color:#17402f; font-weight:600; padding-bottom:5px;">{{ $kategori }}</td>
                                            </tr>
                                            <tr>
                                                <td width="96" valign="top" style="font-size:12.5px; color:#6b756f; padding-bottom:5px;">Lokasi</td>
                                                <td valign="top" style="font-size:13px; color:#17402f; font-weight:600; padding-bottom:5px;">{{ $wilayah }}</td>
                                            </tr>
                                            <tr>
                                                <td width="96" valign="top" style="font-size:12.5px; color:#6b756f;">Tahun</td>
                                                <td valign="top" style="font-size:13px; color:#17402f; font-weight:600;">{{ $tahun }}</td>
                                            </tr>
                                        </table>

                                        @if (filled($deskripsi))
                                            <p style="margin:12px 0 0; font-size:12.5px; line-height:1.8; color:#3c4741;">
                                                {{ $deskripsi }}
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 18px; font-size:13px; line-height:1.8; color:#3c4741;">
                                Prestasi ini juga tampil di halaman publik pesantren. Mari terus mendoakan dan
                                mendukung ananda untuk terus berprestasi.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
                                <tr>
                                    <td align="center" style="padding-right:8px;">
                                        <a href="{{ $portalUrl }}" target="_blank" rel="noopener noreferrer"
                                           style="display:inline-block; padding:13px 24px; background-color:#12b886; border-radius:10px; font-size:13px; font-weight:700; color:#ffffff; text-decoration:none;">
                                            Buka Portal Wali
                                        </a>
                                    </td>
                                    <td align="center">
                                        <a href="{{ $publicUrl }}" target="_blank" rel="noopener noreferrer"
                                           style="display:inline-block; padding:12px 24px; background-color:#ffffff; border:1px solid #cfe9dd; border-radius:10px; font-size:13px; font-weight:700; color:#0e9f73; text-decoration:none;">
                                            Halaman Prestasi
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Penutup --}}
                    <tr>
                        <td style="padding:0 32px;">
                            <div style="border-top:1px solid #e4e0d4; line-height:1px; font-size:1px;">&nbsp;</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:22px 32px 30px;">
                            <p style="margin:0; font-size:14px; line-height:1.8; color:#1f2a24;">
                                Wassalamu&rsquo;alaikum warahmatullahi wabarakatuh,
                            </p>
                            <p style="margin:14px 0 2px; font-size:14px; font-weight:700; color:#17402f;">
                                Pondok Pesantren Babul Hasanah
                            </p>
                            <p style="margin:3px 0 0; font-size:11.5px; line-height:1.7; color:#8a948e;">
                                {{ $alamatPesantren }}<br>
                                <a href="mailto:{{ $emailPesantren }}" style="color:#0e9f73; text-decoration:none;">{{ $emailPesantren }}</a>
                            </p>
                        </td>
                    </tr>

                    {{-- Kaki --}}
                    <tr>
                        <td style="background-color:#fbfaf6; border-top:1px solid #e4e0d4; padding:16px 32px; text-align:center; font-size:11px; line-height:1.7; color:#8a948e;">
                            Email ini dikirim otomatis oleh sistem Portal Babul Hasanah, mohon tidak membalas.<br>
                            &copy; {{ date('Y') }} Pondok Pesantren Babul Hasanah. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
