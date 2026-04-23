<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز التحقق</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            background: #f4f7fb;
            color: #1a1917;
            padding: 32px 16px;
            direction: rtl;
        }
        .card {
            max-width: 520px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 32px rgba(0,0,0,.08);
        }
        .header {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            padding: 36px 32px;
            text-align: center;
        }
        .header h1 {
            color: #fff;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -.5px;
        }
        .header p {
            color: rgba(255,255,255,.8);
            font-size: 13px;
            margin-top: 6px;
        }
        .body {
            padding: 36px 32px;
        }
        .greeting {
            font-size: 15px;
            color: #374151;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .otp-box {
            background: #f0f9ff;
            border: 2px dashed #38bdf8;
            border-radius: 16px;
            padding: 28px;
            text-align: center;
            margin: 24px 0;
        }
        .otp-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 10px;
        }
        .otp-code {
            font-size: 52px;
            font-weight: 900;
            color: #0ea5e9;
            letter-spacing: 12px;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }
        .ttl-note {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 10px;
        }
        .warning {
            background: #fffbeb;
            border-right: 3px solid #f59e0b;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 12px;
            color: #78350f;
            margin-top: 20px;
            line-height: 1.6;
        }
        .footer {
            padding: 20px 32px;
            border-top: 1px solid #f0ede8;
            text-align: center;
        }
        .footer p {
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.7;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>{{ $appName }}</h1>
            <p>رمز التحقق الخاص بك</p>
        </div>

        <div class="body">
            <p class="greeting">
                مرحباً <strong>{{ $name }}</strong>،<br>
                لقد طلبت رمز التحقق لإتمام عملية التسجيل أو تسجيل الدخول.
            </p>

            <div class="otp-box">
                <p class="otp-label">رمز التحقق</p>
                <div class="otp-code">{{ $code }}</div>
                <p class="ttl-note">⏱ صالح لمدة {{ $ttl }} دقائق</p>
            </div>

            <div class="warning">
                ⚠️ لا تشارك هذا الرمز مع أي شخص. لن يطلب منك أي موظف في {{ $appName }} هذا الرمز أبداً.
            </div>

            <p style="font-size:13px;color:#6b7280;margin-top:20px;line-height:1.7;">
                إذا لم تطلب هذا الرمز، يمكنك تجاهل هذه الرسالة بأمان.
            </p>
        </div>

        <div class="footer">
            <p>
                © {{ date('Y') }} {{ $appName }} — جميع الحقوق محفوظة<br>
                هذه رسالة آلية، الرجاء عدم الرد عليها.
            </p>
        </div>
    </div>
</body>
</html>