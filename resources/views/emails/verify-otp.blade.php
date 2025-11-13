<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Momo+Trust+Display&display=swap" rel="stylesheet">
    <style>
        *{
            font-family: "Momo Trust Display", sans-serif;
        }
        body {
            background-color: #f4f4f4;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }

        .header {
            position: relative;
            height: 120px;
            overflow: hidden;
        }

        .header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .header .gradient-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.5), transparent);
        }

        .heading-text{
            font-style: normal;
            text-align: center;
            font-weight: bold;
            font-size: 2.2em;
            margin-bottom: 26px;
        }

        .body {
            padding: 30px;
        }

        .body h2 {
            margin-top: 0;
            font-size: 20px;
            color: #222;
        }

        .body p {
            margin: 15px 0;
            font-size: 14px;
            line-height: 1.6;
            color: #555;
        }

        .otp-box {
            text-align: center;
            margin: 30px 0;
        }

        .otp-code {
            display: inline-block;
            background: #0077ff;
            color: #ffffff;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 6px;
            padding: 15px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px #51a2ff;
        }
        .footer {
            background: #fafafa;
            text-align: center;
            font-size: 12px;
            color: #888;
            padding: 15px;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- <div class="header">
            <img src="{{ asset('images/app-main-bg-img.png') }}" alt="Header Background">
            <div class="gradient-overlay"></div>
        </div> --}}

        <div class="body">
            <div class="heading-text">Secure Email Verification</div>

            <h2>Hello {{ $user->name ?? 'John Doe' }},</h2>
            <p>
                Thank you for signing up!
                Use the One-Time Password (OTP) below to verify your email address and complete your registration.
            </p>

            <div class="otp-box">
                <span class="otp-code">{{ $otp ?? 'XXXXXX' }}</span>
            </div>

            <p style="text-align: center; font-size: 13px; color: #666666; line-height: 1.5; margin: 15px 0;">
                This code is valid for <strong>10 minutes</strong>. Please enter it promptly to verify your account.
            </p>

            <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 25px 0;">

            <p style="text-align: center; font-size: 11px; color: #999999; line-height: 1.4; margin: 15px 0;">
                If you didn’t request this, you can safely ignore this email. Your account will remain secure.
            </p>

        </div>

        <div class="footer">
            &copy; {{ date('Y') }} <strong>{{ config('app.name') }}</strong>. All rights reserved.
        </div>
    </div>
</body>
</html>
