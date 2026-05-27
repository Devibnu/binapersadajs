<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pesan Kontak Baru dari Website</title>
</head>
<body style="margin:0; padding:0; background:#f3f5f7; font-family:Arial, sans-serif; color:#263544;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f5f7; padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; background:#ffffff; border:1px solid #e5e9ed;">
                    <tr>
                        <td style="background:#0c1e35; padding:22px 28px; color:#ffffff;">
                            @if(!empty($websiteSetting?->logo))
                                <img src="{{ asset('storage/' . $websiteSetting->logo) }}" alt="{{ $websiteSetting->nama_perusahaan ?? 'Bina Persada JS' }}" style="display:block; max-height:54px; max-width:200px; background:#ffffff; padding:6px; margin-bottom:14px;">
                            @endif
                            <div style="font-size:18px; font-weight:bold;">{{ $websiteSetting?->nama_perusahaan ?? 'PT. Bina Persada Jaya Sejahtera' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 28px;">
                            <h1 style="margin:0 0 8px; font-size:22px; color:#0c1e35;">Pesan Kontak Baru</h1>
                            <p style="margin:0 0 26px; color:#5f6c78; line-height:1.5;">Ada pesan baru yang dikirim melalui formulir kontak website.</p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:14px; line-height:1.5;">
                                <tr><td style="width:150px; padding:8px 0; color:#66727e;">Nama</td><td style="padding:8px 0; font-weight:bold;">{{ $contactMessage->name }}</td></tr>
                                <tr><td style="padding:8px 0; color:#66727e;">Email</td><td style="padding:8px 0;"><a href="mailto:{{ $contactMessage->email }}" style="color:#1f8f5f;">{{ $contactMessage->email }}</a></td></tr>
                                <tr><td style="padding:8px 0; color:#66727e;">Telepon / WhatsApp</td><td style="padding:8px 0;">{{ $contactMessage->phone ?: '-' }}</td></tr>
                                <tr><td style="padding:8px 0; color:#66727e;">Subjek</td><td style="padding:8px 0;">{{ $contactMessage->subject ?: '-' }}</td></tr>
                                <tr><td style="padding:8px 0; color:#66727e;">Tanggal masuk</td><td style="padding:8px 0;">{{ $contactMessage->created_at?->format('d/m/Y H:i') }}</td></tr>
                            </table>

                            <div style="margin-top:24px; padding:18px; background:#f6f8f9; border-left:4px solid #1f8f5f; line-height:1.65;">
                                {!! nl2br(e($contactMessage->message)) !!}
                            </div>

                            <p style="margin:30px 0 0;">
                                <a href="{{ url('/paneladmin/contact-messages/' . $contactMessage->id) }}" style="display:inline-block; padding:13px 22px; background:#1f8f5f; color:#ffffff; text-decoration:none; font-weight:bold;">Buka di Admin Panel</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
