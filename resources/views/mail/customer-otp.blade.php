@php($companyProfile = \App\Models\CompanyProfile::query()->first())
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('كود التحقق') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#0f172a;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#0f172a;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;background-color:#1e293b;border-radius:16px;border:1px solid #334155;overflow:hidden;">
                    <tr>
                        <td style="padding:28px 32px;background:linear-gradient(135deg,#4f46e5,#7c3aed);text-align:center;">
                            <div style="font-size:22px;font-weight:bold;color:#ffffff;">
                                {{ $companyProfile?->name ?? 'Venecia Developments' }}
                            </div>
                            <div style="font-size:13px;color:#c7d2fe;margin-top:4px;">{{ __('تأكيد البريد الإلكتروني') }} · Email Verification</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <div style="font-size:16px;color:#e2e8f0;line-height:1.7;" dir="auto">
                                <p style="margin:0 0 16px;">{{ __('مرحباً :name،', ['name' => $customerName]) }}</p>
                                <p style="margin:0 0 16px;">{{ __('استخدم الكود التالي لإتمام تسجيل حسابك في الموقع:') }}</p>
                            </div>

                            <div dir="ltr" style="text-align:center;margin:24px 0;padding:20px;background-color:#0f172a;border:1px dashed #6366f1;border-radius:12px;">
                                <span style="font-size:38px;font-weight:bold;letter-spacing:10px;color:#a5b4fc;">{{ $otpCode }}</span>
                            </div>

                            <div style="font-size:14px;color:#94a3b8;line-height:1.7;" dir="auto">
                                <p style="margin:0 0 8px;">{{ __('هذا الكود صالح لمدة :minutes دقائق. إذا لم تكن قد حاولت التسجيل، تجاهل هذه الرسالة.', ['minutes' => $expiresInMinutes]) }}</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px;border-top:1px solid #334155;text-align:center;">
                            <div style="font-size:12px;color:#64748b;line-height:1.7;" dir="auto">
                                © {{ date('Y') }} {{ $companyProfile?->name ?? 'Venecia Developments' }} — {{ __('جميع الحقوق محفوظة.') }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
