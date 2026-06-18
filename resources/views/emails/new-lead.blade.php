<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('admin.mail.new_lead_title') }}</title>
</head>
<body style="margin:0;background:#f8fafc;font-family:Arial,Tahoma,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f8fafc;padding:24px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="padding:22px 24px;border-bottom:1px solid #e2e8f0;">
                            <strong style="font-size:18px;">{{ __('admin.mail.new_lead_title') }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;line-height:1.8;">
                            <p style="margin:0 0 14px;color:#475569;">{{ __('admin.mail.new_lead_body') }}</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                @foreach ([
                                    __('admin.fields.company') => $lead->company?->name,
                                    __('admin.fields.site') => $lead->site?->name,
                                    __('admin.fields.form') => $lead->form?->name ?: $lead->form_name ?: $lead->form_key,
                                    __('admin.fields.name') => $lead->name,
                                    __('admin.fields.email') => $lead->email,
                                    __('admin.fields.phone') => $lead->phone,
                                    __('admin.fields.page') => $lead->page_url,
                                    __('admin.fields.message') => $lead->message,
                                ] as $label => $value)
                                    <tr>
                                        <td style="width:150px;padding:8px;border-top:1px solid #e2e8f0;color:#64748b;">{{ $label }}</td>
                                        <td style="padding:8px;border-top:1px solid #e2e8f0;">{{ $value ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
