<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('admin.mail.test_subject') }}</title>
</head>
<body style="margin:0;background:#f8fafc;font-family:Arial,Tahoma,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f8fafc;padding:24px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="padding:24px;border-bottom:1px solid #e2e8f0;">
                            <strong style="font-size:18px;">{{ config('app.name') }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;line-height:1.8;">
                            <h1 style="margin:0 0 12px;font-size:22px;line-height:1.5;">{{ __('admin.mail.test_title') }}</h1>
                            <p style="margin:0;color:#475569;">{{ __('admin.mail.test_body') }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
