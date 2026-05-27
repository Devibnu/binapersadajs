<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $replySubject }}</title>
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
                            <p style="margin:0 0 20px; line-height:1.6;">Yth. {{ $contactMessage->name }},</p>
                            <div style="font-size:15px; line-height:1.7; color:#263544;">
                                {!! nl2br(e($replyBody)) !!}
                            </div>
                            <p style="margin:28px 0 0; border-top:1px solid #e6eaed; padding-top:18px; color:#66727e; font-size:13px; line-height:1.6;">
                                Hormat kami,<br>
                                <strong>PT. Bina Persada Jaya Sejahtera</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
