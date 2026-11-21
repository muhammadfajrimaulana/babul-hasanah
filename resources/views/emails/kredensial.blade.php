<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judul }}</title>
</head>
<body style="margin:0;padding:0;background:#f1eee6;font-family:'Segoe UI',Tahoma,Arial,sans-serif;color:#1f2a24;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1eee6;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:600px;background:#fff;border-radius:18px;overflow:hidden;border:1px solid #e4e0d4;">
                    <tr>
                        <td style="background:#17402f;padding:26px 32px;">
                            <div style="font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;color:#a9c4b6;">Pondok Pesantren</div>
                            <div style="font-size:18px;font-weight:700;color:#fff;">Babul Hasanah</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:34px 32px;">
                            <span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#eaf7f1;color:#0b6b4d;font-size:11px;font-weight:700;">
                                AKUN {{ strtoupper($peran) }} AKTIF
                            </span>

                            <p style="margin:18px 0 14px;font-size:14px;line-height:1.8;">Assalamu&rsquo;alaikum warahmatullahi wabarakatuh,</p>

                            <p style="margin:0 0 14px;font-size:14px;line-height:1.8;">
                                Halo <strong>{{ $nama }}</strong>, akun <strong>{{ $peran }}</strong> Anda telah dibuat.
                                Berikut kredensial login:
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f7fbf9;border:1px solid #cfe9dd;border-radius:14px;margin-bottom:18px;">
                                <tr>
                                    <td style="padding:18px;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="110" style="font-size:12.5px;color:#6b756f;padding-bottom:8px;">Username</td>
                                                <td style="font-size:13px;font-weight:600;color:#17402f;padding-bottom:8px;word-break:break-all;">{{ $email }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:12.5px;color:#6b756f;">Password sementara</td>
                                                <td>
                                                    <span style="display:inline-block;padding:6px 12px;border-radius:8px;background:#fff;border:1px dashed #a7d9c6;font-family:'Courier New',monospace;font-size:14px;letter-spacing:.1em;color:#17402f;">
                                                        {{ $passwordSementara }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding:0 18px 20px;">
                                        <a href="{{ $loginUrl }}" target="_blank" rel="noopener noreferrer"
                                            style="display:inline-block;padding:13px 28px;background:#12b886;border-radius:10px;font-size:13.5px;font-weight:700;color:#fff;text-decoration:none;">
                                            Masuk ke Sistem
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 12px;font-size:13px;line-height:1.8;color:#3c4741;">
                                Password sementara hanya berlaku <strong>24 jam</strong>. Saat login pertama, Anda
                                <strong>wajib mengganti password</strong> dan tidak bisa melewati halaman tersebut.
                                Admin tidak dapat melihat password Anda.
                            </p>

                            @if ($catatan)
                                <p style="margin:0 0 12px;font-size:13px;line-height:1.8;color:#3c4741;">{{ $catatan }}</p>
                            @endif

                            <p style="margin:22px 0 0;font-size:14px;line-height:1.8;">Wassalamu&rsquo;alaikum warahmatullahi wabarakatuh,</p>
                            <p style="margin:12px 0 0;font-size:14px;font-weight:700;color:#17402f;">Sekretariat Pesantren</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fbfaf6;border-top:1px solid #e4e0d4;padding:16px 32px;text-align:center;font-size:11px;color:#8a948e;">
                            Email ini dikirim otomatis oleh sistem, mohon tidak membalas.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
