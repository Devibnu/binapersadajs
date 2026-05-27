<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Lead Baru dari Website</title>
</head>
<body style="margin:0;padding:28px;background:#f3f5f7;color:#253347;font-family:Arial,sans-serif;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:8px;padding:28px;">
        <h2 style="color:#0c1e35;margin-top:0;">{{ $websiteSetting?->nama_perusahaan ?: 'PT. Bina Persada Jaya Sejahtera' }}</h2>
        <h3 style="margin-bottom:20px;">Lead baru dari website</h3>
        <p><strong>Sumber:</strong> {{ $lead->sourceLabel() }}</p>
        <p><strong>Nama:</strong> {{ $lead->name ?: '-' }}</p>
        <p><strong>Email:</strong> {{ $lead->email }}</p>
        <p><strong>Telepon:</strong> {{ $lead->phone ?: '-' }}</p>
        <p><strong>Perusahaan:</strong> {{ $lead->company ?: '-' }}</p>
        <p><strong>Kebutuhan:</strong><br>{{ $lead->message ?: '-' }}</p>
        <p><strong>Tanggal:</strong> {{ $lead->created_at->format('d/m/Y H:i') }}</p>
        <p style="margin-top:28px;">
            <a href="{{ url('/paneladmin/leads/' . $lead->id) }}" style="background:#1f8f5f;color:#ffffff;text-decoration:none;border-radius:4px;padding:12px 18px;">Buka di Admin Panel</a>
        </p>
    </div>
</body>
</html>
