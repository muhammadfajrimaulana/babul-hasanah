<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>Reset Kata Sandi</title>
</head>
<body style="margin:0; padding:0; background-color:#f1eee6; font-family:'Segoe UI', Tahoma, Arial, sans-serif; color:#1f2a24; -webkit-text-size-adjust:100%;">

    {{-- Pratinjau singkat di kotak masuk email --}}
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">
        Tautan reset kata sandi Portal Babul Hasanah, berlaku {{ $expiresInMinutes }} menit.
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
                            <p style="margin:0 0 16px; font-size:15px; line-height:1.7; color:#1f2a24;">
                                Assalamu&rsquo;alaikum warahmatullahi wabarakatuh,
                            </p>

                            <p style="margin:0 0 14px; font-size:14px; line-height:1.8; color:#3c4741;">
                                Kami menerima permintaan untuk mengatur ulang kata sandi akun
                                <strong style="color:#17402f;">Portal Pondok Pesantren Babul Hasanah</strong>
                                yang terdaftar dengan email
                                <strong style="color:#17402f;">{{ $email }}</strong>.
                            </p>

                            <p style="margin:0 0 24px; font-size:14px; line-height:1.8; color:#3c4741;">
                                Untuk membuat kata sandi baru, silakan klik tombol di bawah ini:
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 24px;">
                                <tr>
                                    <td align="center" bgcolor="#12b886" style="border-radius:10px;">
                                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                           style="display:inline-block; padding:14px 32px; font-family:'Segoe UI', Tahoma, Arial, sans-serif; font-size:14px; font-weight:700; color:#ffffff; text-decoration:none; border-radius:10px;">
                                            Buat Kata Sandi Baru
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                   style="background-color:#fbfaf6; border:1px solid #e4e0d4; border-radius:12px; margin-bottom:22px;">
                                <tr>
                                    <td style="padding:14px 16px; font-size:12.5px; line-height:1.7; color:#6b756f;">
                                        <strong style="color:#17402f;">Tautan berlaku {{ $expiresInMinutes }} menit</strong>
                                        dan hanya dapat digunakan satu kali. Jika sudah kedaluwarsa, silakan minta
                                        tautan baru melalui halaman lupa kata sandi.
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 8px; font-size:12px; line-height:1.7; color:#6b756f;">
                                Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut ke browser Anda:
                            </p>
                            <p style="margin:0 0 24px; font-size:11.5px; line-height:1.7; word-break:break-all; color:#3c4741;">
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                   style="color:#0e9f73; text-decoration:underline;">{{ $url }}</a>
                            </p>

                            <p style="margin:0 0 4px; font-size:13px; line-height:1.8; color:#3c4741;">
                                Jika Anda tidak merasa meminta pengaturan ulang kata sandi, abaikan email ini.
                                Kata sandi Anda tidak akan berubah dan akun tetap aman.
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
                                Admin Portal Pesantren
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
