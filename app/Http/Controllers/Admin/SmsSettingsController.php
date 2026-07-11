<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SmsService;
use App\Services\SmsSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmsSettingsController extends Controller
{
    public function __construct(
        private SmsService $sms,
        private readonly SmsSettingsService $settings,
    ) {}

    /**
     * عرض صفحة الإعدادات في الداشبورد
     */
    public function show(): View
    {
        return view('admin.settings.sms', $this->settings->getSettingsData());
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

        $this->settings->saveSettings($validated);

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
