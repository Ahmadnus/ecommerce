<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TypographySettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TypographySettingsController extends Controller
{
    public function __construct(
        private readonly TypographySettingsService $typography,
    ) {}

    public function index(): View
    {
        return view('admin.settings.typography', $this->typography->getIndexData());
    }

    public function update(Request $request): RedirectResponse
    {
        $allFontKeys  = $this->typography->fontKeys();
        $allColorKeys = $this->typography->colorKeys();

        // Build validation rules
        $rules = [];
        foreach ($allFontKeys as $key) {
            $rules[$key] = ['nullable', 'string', 'max:20',
                'regex:/^\d+(\.\d+)?(px|rem|em|vw|vh|%)?$/'];
        }
        foreach ($allColorKeys as $key) {
            $rules[$key] = ['nullable', 'string', 'max:50'];
        }

        $request->validate($rules);

        $this->typography->saveSettings(
            $request->only(array_merge($allFontKeys, $allColorKeys))
        );

        return redirect()
            ->route('admin.settings.typography')
            ->with('success', 'Typography settings saved successfully.');
    }
}
