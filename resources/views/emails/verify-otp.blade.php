<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <style>
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
            background: #51a2ff;
            color: #ffffff;
            text-align: center;
            padding: 30px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.9;
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
            background: #51a2ff;
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

        <!-- Header -->
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Secure Email Verification</p>
        </div>

        <!-- Body -->
        <div class="body">
            <h2>Hello {{ $user->name }},</h2>
            <p>
                Thank you for signing up!
                Use the One-Time Password (OTP) below to verify your email address and complete your registration.
            </p>

            <!-- OTP Code -->
            <div class="otp-box">
                <span class="otp-code">{{ $otp }}</span>
            </div>

            <p style="text-align:center; font-size:13px; color:#666;">
                This code is valid for <strong>10 minutes</strong>.
                Please enter it promptly to verify your account.
            </p>

            <hr style="border:none; border-top:1px solid #e0e0e0; margin:25px 0;">

            <p style="text-align:center; font-size:12px; color:#999;">
                If you didn’t request this, you can safely ignore this email.
                Your account will remain secure.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} <strong>{{ config('app.name') }}</strong>. All rights reserved.
        </div>
    </div>
</body>
</html>
