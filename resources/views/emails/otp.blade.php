<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <style>
        .container { font-family: sans-serif; text-align: center; padding: 20px; color: #333; }
        .code { font-size: 32px; font-bold; letter-spacing: 5px; color: #4F46E5; background: #F3F4F6; padding: 10px 20px; border-radius: 10px; display: inline-block; margin: 20px 0; }
        .footer { font-size: 12px; color: #999; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>أهلاً {{ $name }}!</h2>
        <p>شكراً لتسجيلك في متجرنا. يرجى استخدام الرمز التالي لتفعيل حسابك:</p>
        <div class="code">{{ $code }}</div>
        <p>هذا الرمز صالح لمدة 10 دقائق فقط.</p>
        <div class="footer">
            إذا لم تقم بإنشاء حساب، يرجى تجاهل هذا الإيميل.
        </div>
    </div>
</body>
</html>