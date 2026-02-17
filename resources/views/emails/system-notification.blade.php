<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? config('app.name') }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f4f4f5; padding:20px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:8px;overflow:hidden;">
        <tr>
            <td style="background:#197a8c;color:#ffffff;padding:16px 20px;font-size:18px;font-weight:bold;">
                {{ config('app.name', 'Dwell Dermatology Center') }}
            </td>
        </tr>
        <tr>
            <td style="padding:20px;font-size:14px;color:#111827;">
                <h2 style="margin-top:0;font-size:18px;color:#111827;">{{ $title }}</h2>
                <p style="margin:12px 0;white-space:pre-line;">{{ $body }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding:16px 20px;font-size:12px;color:#6b7280;background:#f9fafb;border-top:1px solid #e5e7eb;">
                This email was automatically sent by {{ config('app.name', 'Dwell Dermatology Center') }}.
            </td>
        </tr>
    </table>
</body>
</html>

