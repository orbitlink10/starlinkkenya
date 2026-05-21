<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your account is ready</title>
</head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,sans-serif;color:#142033;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f7fb;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border:1px solid #dce3ef;border-radius:8px;padding:28px;">
                    <tr>
                        <td>
                            <h1 style="margin:0 0 12px;font-size:24px;line-height:1.25;color:#111927;">Your account has been created</h1>
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#52627a;">
                                Hello {{ $user->name }},
                            </p>
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#52627a;">
                                Your Starlink Kenya Installers account is ready. You can now sign in, review your order, pay using M-Pesa, or confirm the order through WhatsApp.
                            </p>
                            <p style="margin:0 0 22px;font-size:15px;line-height:1.6;color:#52627a;">
                                Account email: <strong style="color:#111927;">{{ $user->email }}</strong>
                            </p>
                            <a href="{{ route('account.dashboard') }}" style="display:inline-block;background:#a15b0f;color:#ffffff;text-decoration:none;border-radius:999px;padding:12px 18px;font-size:15px;font-weight:700;">
                                Go to My Account
                            </a>
                            <p style="margin:24px 0 0;font-size:13px;line-height:1.5;color:#7b8798;">
                                If you did not create this account, please contact Starlink Kenya Installers.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
