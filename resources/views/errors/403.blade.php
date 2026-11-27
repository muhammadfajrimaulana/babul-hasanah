<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak — Pondok Pesantren Babul Hasanah</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f1eee6;
            color: #1f2a24;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            padding: 24px;
        }

        .card {
            max-width: 520px;
            width: 100%;
            background: #fff;
            border: 1px solid #e4e0d4;
            border-radius: 24px;
            padding: 40px 32px;
            text-align: center;
            box-shadow: 0 22px 50px rgba(23, 64, 47, .12);
        }

        .icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            display: grid;
            place-items: center;
            border-radius: 18px;
            background: #17402f;
            color: #fff;
            font-size: 28px;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 22px;
            color: #17402f;
        }

        p {
            margin: 0 0 24px;
            font-size: 14px;
            line-height: 1.7;
            color: #6b756f;
        }

        a {
            display: inline-block;
            background: #17402f;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            padding: 12px 22px;
            border-radius: 12px;
        }
    </style>
</head>

<body>
    <main class="card">
        <div class="icon" aria-hidden="true">🔒</div>
        <h1>Akses Ditolak</h1>
        <p>
            Anda tidak memiliki izin untuk membuka halaman ini. Jika Anda merasa ada kesalahan,
            silakan hubungi administrator pesantren.
        </p>
        <a href="{{ route('home') }}">Kembali ke Beranda</a>
    </main>
</body>

</html>
