<?php
// app/Http/Controllers/Admin/SeoSettingController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SeoSettingRequest;
use App\Models\SeoSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SeoSettingController extends Controller
{
    private const TYPES = ['main', 'splash'];

    public function index(): View
    {
        $settings = collect(self::TYPES)->mapWithKeys(
            fn($type) => [$type => SeoSetting::firstOrNew(['type' => $type])]
        );

        return view('admin.seo.index', compact('settings'));
    }

    public function edit(string $type): View
    {
        abort_unless(in_array($type, self::TYPES), 404);

        $seo = SeoSetting::firstOrNew(['type' => $type]);

        return view('admin.seo.edit', compact('seo', 'type'));
    }

    public function update(SeoSettingRequest $request, string $type): RedirectResponse
    {
        abort_unless(in_array($type, self::TYPES), 404);

        $seo = SeoSetting::firstOrNew(['type' => $type]);
        $seo->fill($request->except(['og_image', 'favicon', '_token', '_method']));
        $seo->save();

        if ($request->hasFile('og_image')) {
            $seo->addMediaFromRequest('og_image')
                ->toMediaCollection('og_image');
        }

        if ($request->hasFile('favicon')) {
            $seo->addMediaFromRequest('favicon')
                ->toMediaCollection('favicon');
        }

        return redirect()
            ->route('admin.seo.edit', $type)
            ->with('success', 'SEO settings saved successfully.');
    }
}