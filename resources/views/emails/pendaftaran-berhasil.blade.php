<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>Pendaftaran Berhasil</title>
</head>
<body style="margin:0; padding:0; background-color:#f1eee6; font-family:'Segoe UI', Tahoma, Arial, sans-serif; color:#1f2a24; -webkit-text-size-adjust:100%;">

    <div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">
        Pendaftaran PPDB berhasil. Akun Portal Wali Santri Anda sudah aktif.
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

                    {{-- Isi surat --}}
                    <tr>
                        <td style="padding:34px 32px 8px;">
                            <span style="display:inline-block; padding:6px 12px; border-radius:999px; background-color:#eaf7f1; color:#0b6b4d; font-size:11px; font-weight:700; letter-spacing:.04em;">
                                PENDAFTARAN BERHASIL
                            </span>

                            <p style="margin:18px 0 16px; font-size:15px; line-height:1.7; color:#1f2a24;">
                                Assalamu&rsquo;alaikum warahmatullahi wabarakatuh,
                            </p>

                            <p style="margin:0 0 14px; font-size:14px; line-height:1.8; color:#3c4741;">
                                Pendaftaran santri atas nama
                                <strong style="color:#17402f;">{{ $namaSantri }}</strong>
                                untuk jenjang <strong style="color:#17402f;">{{ $jenjang }}</strong>
                                telah kami terima dengan nomor pendaftaran
                                <strong style="color:#17402f;">{{ $noPendaftaran }}</strong>.
                            </p>

                            @if ($akunBaru)
                                <p style="margin:0 0 20px; font-size:14px; line-height:1.8; color:#3c4741;">
                                    Akun <strong style="color:#17402f;">Portal Wali Santri</strong> Anda sudah aktif.
                                    Berikut username dan kata sandi untuk masuk:
                                </p>
                            @else
                                <p style="margin:0 0 20px; font-size:14px; line-height:1.8; color:#3c4741;">
                                    Email ini sudah terdaftar pada akun Portal Wali Santri Anda. Silakan masuk
                                    menggunakan kata sandi akun yang sudah ada — data santri baru otomatis
                                    tertaut setelah pendaftaran diverifikasi.
                                </p>
                            @endif

                            {{-- Kotak kredensial --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                   style="background-color:#f7fbf9; border:1px solid #cfe9dd; border-radius:14px; margin-bottom:22px;">
                                <tr>
                                    <td style="padding:18px 18px 6px;">
                                        <div style="font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#0e9f73; margin-bottom:12px;">
                                            Kredensial Login
                                        </div>

                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:10px;">
                                            <tr>
                                                <td width="110" valign="top" style="font-size:12.5px; color:#6b756f; padding-bottom:6px;">Username</td>
                                                <td valign="top" style="font-size:13px; color:#17402f; font-weight:600; padding-bottom:6px; word-break:break-all;">
                                                    {{ $email }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="110" valign="top" style="font-size:12.5px; color:#6b756f;">Kata Sandi</td>
                                                <td valign="top" style="font-size:13px; color:#17402f; font-weight:600;">
                                                    @if ($akunBaru && $kataSandi)
                                                        <span style="display:inline-block; padding:6px 12px; border-radius:8px; background-color:#ffffff; border:1px dashed #a7d9c6; font-family:'Courier New', Courier, monospace; font-size:14px; letter-spacing:.12em; color:#17402f;">
                                                            {{ $kataSandi }}
                                                        </span>
                                                    @else
                                                        Gunakan kata sandi akun Anda yang sudah ada.
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding:8px 18px 20px;">
                                        <a href="{{ $loginUrl }}" target="_blank" rel="noopener noreferrer"
                                           style="display:inline-block; padding:13px 28px; background-color:#12b886; border-radius:10px; font-size:13.5px; font-weight:700; color:#ffffff; text-decoration:none;">
                                            Masuk ke Portal Wali Santri
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            @if ($akunBaru)
                                <p style="margin:0 0 20px; font-size:13px; line-height:1.8; color:#3c4741;">
                                    Demi keamanan, segera ubah kata sandi setelah masuk melalui menu
                                    <strong>Profil</strong>. Jangan bagikan kata sandi kepada siapa pun.
                                </p>
                            @else
                                <p style="margin:0 0 20px; font-size:13px; line-height:1.8; color:#3c4741;">
                                    Lupa kata sandi? Atur ulang melalui
                                    <a href="{{ $lupaSandiUrl }}" target="_blank" rel="noopener noreferrer"
                                       style="color:#0e9f73; text-decoration:underline; font-weight:600;">halaman lupa kata sandi</a>.
                                </p>
                            @endif

                            <p style="margin:0 0 4px; font-size:13px; line-height:1.8; color:#3c4741;">
                                Pendaftaran Anda saat ini <strong>menunggu verifikasi</strong> dari pihak pesantren.
                                Kami akan menghubungi Anda melalui nomor orang tua/wali setelah proses verifikasi selesai.
                            </p>
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
                                Panitia PPDB Pesantren
                            </p>
                            <p style="margin:0; font-size:12.5px; color:#3c4741;">
                                Pondok Pesantren Babul Hasanah
                            </p>
                            <p style="margin:3px 0 0; font-size:11.5px; line-height:1.7; color:#8a948e;">
                                {{ $alamatPesantren }}<br>
                                <a href="mailto:{{ $emailPesantren }}" style="color:#0e9f73; text-decoration:none;">{{ $emailPesantren }}</a>
                            </p>
                        </td>
                    </tr>

                    {{-- Kaki surat --}}
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
