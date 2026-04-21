<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtpSetting; // تم تغيير الموديل
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmsSettingsController extends Controller
{
    public function __construct(private SmsService $sms) {}

    /**
     * عرض صفحة الإعدادات في الداشبورد
     */
    public function show(): View
    {
        // جلب الإعدادات من جدول otpsettings وتجميعها بالمفتاح
        $settings  = OtpSetting::where('group', 'sms')->get()->keyBy('key');
        
        // جلب القيم الفعالة (من القاعدة أو من الـ config كـ fallback)
        $effective = [
            'sms_url'         => get_otp_setting('sms_url'),
            'sms_user'        => get_otp_setting('sms_user'),
            'sms_pass'        => get_otp_setting('sms_pass'),
            'sms_sid'         => get_otp_setting('sms_sid'),
            'sms_type'        => get_otp_setting('sms_type', 4),
            'otp_ttl_minutes' => get_otp_setting('otp_ttl_minutes', 5),
            'otp_length'      => get_otp_setting('otp_length', 6),
        ];

        return view('admin.settings.sms', compact('settings', 'effective'));
    }

    /**
     * تحديث الإعدادات في قاعدة البيانات
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sms_url'         => 'nullable|url|max:500',
            'sms_user'        => 'nullable|string|max:100',
            'sms_pass'        => 'nullable|string|max:100',
            'sms_sid'         => 'nullable|string|max:60',
            'sms_type'        => 'nullable|integer|in:1,2,3,4',
            'otp_ttl_minutes' => 'nullable|integer|min:1|max:60',
            'otp_length'      => 'nullable|integer|min:4|max:8',
        ]);

        foreach ($validated as $key => $value) {
            // استخدام ميثود set الموجودة في موديل OtpSetting لمسح الكاش تلقائياً
            OtpSetting::set($key, ($value !== '' && $value !== null) ? $value : null);
        }

        return redirect()->route('admin.settings.sms')
                         ->with('success', 'تم حفظ إعدادات الرسائل النصية بنجاح ✓');
    }

    /**
     * اختبار الاتصال بالـ API من الداشبورد
     */
    public function test(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['test_phone' => 'required|string|max:20']);

        try {
            // سيستخدم الـ Service الإعدادات الجديدة فوراً لأن ميثود set قامت بمسح الكاش
            $result = $this->sms->testConnection($request->input('test_phone'));
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        return response()->json([
            'success' => $result['success'],
            'message' => $result['success'] ? 'تم إرسال الرسالة التجريبية بنجاح ✓' : 'فشل: ' . $result['response'],
            'raw'     => $result['response'],
        ]);
    }
}