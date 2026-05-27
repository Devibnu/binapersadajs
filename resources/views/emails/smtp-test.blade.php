<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Test SMTP Bina Persada JS</title>
</head>
<body style="margin:0; padding:30px; background:#f3f5f7; font-family:Arial, sans-serif; color:#263544;">
    <div style="max-width:620px; margin:0 auto; background:#ffffff; border:1px solid #e5e9ed;">
        <div style="background:#0c1e35; color:#ffffff; padding:22px 28px; font-size:18px; font-weight:bold;">
            PT. Bina Persada Jaya Sejahtera
        </div>
        <div style="padding:28px;">
            <h1 style="margin:0 0 16px; font-size:21px; color:#0c1e35;">SMTP Berhasil Digunakan</h1>
            <p style="margin:0 0 12px; line-height:1.7;">Email test ini berhasil dikirim menggunakan konfigurasi SMTP dari panel admin.</p>
            <p style="margin:0; color:#66727e; line-height:1.7;">
                Pengirim: {{ $emailSetting->from_name }} &lt;{{ $emailSetting->from_address }}&gt;
            </p>
        </div>
    </div>
</body>
</html>
