<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $mailSubject }}</title>
</head>
<body style="margin:0; padding:0; background:#f3f5f7; font-family:Arial, sans-serif; color:{{ $emailTemplate?->text_color ?? '#263544' }};">
    @if(! $wrapTemplate)
        <div style="padding:24px; font-size:15px; line-height:1.8; color:#263544;">
            {!! $body !!}
        </div>
    @else
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f5f7; padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px; background:#ffffff; border:1px solid #e5e9ed; border-radius:10px; overflow:hidden;">
                    <tr>
                        <td style="background-color:{{ $emailTemplate?->header_color ?? '#0c1e35' }}; {{ $emailTemplate?->headerBackgroundUrl() ? 'background-image:url(' . $emailTemplate->headerBackgroundUrl() . ');background-size:cover;background-position:center;' : '' }} padding:26px 30px; color:#ffffff;">
                            {!! $renderedHeaderHtml !!}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 30px; font-size:15px; line-height:1.8; color:{{ $emailTemplate?->text_color ?? '#263544' }};">
                            {!! $body !!}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:{{ $emailTemplate?->footer_color ?? '#0c1e35' }}; {{ $emailTemplate?->footerBackgroundUrl() ? 'background-image:url(' . $emailTemplate->footerBackgroundUrl() . ');background-size:cover;background-position:center;' : '' }} padding:24px 30px; color:#ffffff; font-size:13px; line-height:1.7;">
                            {!! $renderedFooterHtml !!}
                        </td>
                    </tr>
                    @if(filled($emailTemplate?->disclaimer_html))
                        <tr>
                            <td style="padding:16px 30px; background:#f8f9fb; color:#66727e; font-size:12px; line-height:1.6;">
                                {!! $renderedDisclaimerHtml !!}
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
    @endif
</body>
</html>
