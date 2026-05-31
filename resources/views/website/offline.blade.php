<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0c1e35">
    <title>PT. Bina Persada Jaya Sejahtera - Offline</title>
    @php
        $pwaIconVersion = file_exists(public_path('icons/icon-512x512.png'))
            ? filemtime(public_path('icons/icon-512x512.png'))
            : time();
    @endphp
    <link rel="manifest" href="/manifest.json?v={{ $pwaIconVersion }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32x32.png') }}?v={{ $pwaIconVersion }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ $pwaIconVersion }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}?v={{ $pwaIconVersion }}">
    <style>
        body {
            align-items: center;
            background: #f7f9fc;
            color: #0c1e35;
            display: flex;
            font-family: Arial, sans-serif;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 24px;
            text-align: center;
        }
        .offline-card {
            background: #fff;
            border-top: 4px solid #ffb600;
            box-shadow: 0 18px 45px rgba(12, 30, 53, .12);
            max-width: 460px;
            padding: 36px 28px;
        }
        img {
            height: 72px;
            margin-bottom: 20px;
            width: 72px;
        }
        h1 {
            font-size: 26px;
            margin: 0 0 12px;
        }
        p {
            color: #5f6f82;
            line-height: 1.6;
            margin: 0;
        }
    </style>
</head>
<body>
    <main class="offline-card">
        <img src="/icons/icon-192x192.png" alt="PT. Bina Persada Jaya Sejahtera" width="192" height="192" decoding="async">
        <h1>Anda sedang offline</h1>
        <p>Koneksi internet tidak tersedia. Beberapa halaman yang pernah dibuka masih dapat diakses dari cache aplikasi.</p>
    </main>
</body>
</html>
