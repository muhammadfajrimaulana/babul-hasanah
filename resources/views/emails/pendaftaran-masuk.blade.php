<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Diterima</title>
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
                                PENDAFTARAN BERHASIL DIKIRIM
                            </span>

                            <p style="margin:18px 0 14px;font-size:14px;line-height:1.8;">Assalamu&rsquo;alaikum warahmatullahi wabarakatuh,</p>

                            <p style="margin:0 0 14px;font-size:14px;line-height:1.8;">
                                Pendaftaran santri atas nama <strong>{{ $namaSantri }}</strong>
                                untuk jenjang <strong>{{ $jenjang }}</strong> telah kami terima dengan nomor
                                <strong>{{ $noPendaftaran }}</strong>.
                            </p>

                            <p style="margin:0 0 14px;font-size:13px;line-height:1.8;color:#3c4741;">
                                Pendaftaran Anda saat ini <strong>menunggu verifikasi Admin Jenjang {{ $jenjang }}</strong>.
                                Setelah diterima, sistem akan membuat akun Portal Wali Santri dan mengirimkan
                                username serta password sementara ke email ini.
                            </p>

                            <p style="margin:0 0 14px;font-size:13px;line-height:1.8;color:#3c4741;">
                                Password sementara hanya berlaku 24 jam dan wajib diganti saat login pertama.
                                Admin tidak dapat melihat password Anda.
                            </p>

                            <p style="margin:22px 0 0;font-size:14px;line-height:1.8;">Wassalamu&rsquo;alaikum warahmatullahi wabarakatuh,</p>
                            <p style="margin:12px 0 0;font-size:14px;font-weight:700;color:#17402f;">Panitia PPDB Pesantren</p>
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
