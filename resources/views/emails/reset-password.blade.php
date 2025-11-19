<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Momo+Trust+Display&display=swap" rel="stylesheet">
    <style>
        *{
            font-family: "Momo Trust Display", sans-serif;
        }
        body { background-color: #f4f4f4; margin:0; padding:20px; color:#333; }
        .container { max-width:600px; margin:auto; background:#fff; border-radius:10px; overflow:hidden; border:1px solid #e0e0e0; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
        .body { padding:30px; }
        .heading-text{ font-style: normal; text-align:center; font-weight:bold; font-size:2.2em; margin-bottom:26px; }
        .body h2 { margin-top:0; font-size:20px; color:#222; }
        .body p { margin:15px 0; font-size:14px; line-height:1.6; color:#555; }
        .btn { display:inline-block; padding:12px 25px; background:#0077ff; color:#fff; font-weight:bold; text-decoration:none; border-radius:8px; margin:20px 0; text-align:center; letter-spacing:2px; }
        .plain-link { font-size:13px; color:#0077ff; word-break: break-all; text-align:center; display:block; margin:10px 0; }
        .footer { background:#fafafa; text-align:center; font-size:12px; color:#888; padding:15px; border-top:1px solid #e0e0e0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="body">
            <div class="heading-text">Reset Your Password</div>

            <h2>Hello {{ $user->name ?? 'John Doe' }},</h2>
            <p>
                We received a request to reset your account password. Click the button below to set a new password. This link is valid for <strong>60 minutes</strong>.
            </p>

            <div style="text-align: center;">
                <a href="{{ $url ?? '#' }}" class="btn">Reset Password</a>
            </div>

            <p style="text-align:center; font-size:13px; color:#666666; line-height:1.5; margin:15px 0;">
                If the button above does not work, copy and paste the link below into your browser:
            </p>

            <p class="plain-link">{{ $url ?? '#' }}</p>

            <p style="text-align:center; font-size:13px; color:#666666; line-height:1.5; margin:15px 0;">
                If you did not request a password reset, you can safely ignore this email.
            </p>

            <hr style="border:none; border-top:1px solid #e0e0e0; margin:25px 0;">

            <p style="text-align:center; font-size:11px; color:#999999; line-height:1.4; margin:15px 0;">
                Your account will remain secure.
            </p>

        </div>

        <div class="footer">
            &copy; {{ date('Y') }} <strong>{{ config('app.name') }}</strong>. All rights reserved.
        </div>
    </div>
</body>
</html>
