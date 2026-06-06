<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\TypographySettingsHelper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TypographySettingsController extends Controller
{
    public function index(): View
    {
        $current     = TypographySettingsHelper::all();
        $fontKeys    = TypographySettingsHelper::fontSizeKeys();
        $colorKeys   = TypographySettingsHelper::colorKeys();
        $fontLabels  = $this->fontLabels();
        $colorLabels = $this->colorLabels();

        return view('admin.settings.typography', compact(
            'current', 'fontKeys', 'colorKeys', 'fontLabels', 'colorLabels'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $allFontKeys  = array_keys(TypographySettingsHelper::fontSizeKeys());
        $allColorKeys = array_keys(TypographySettingsHelper::colorKeys());

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

        // Save each setting — Setting::set() uses updateOrCreate internally
        foreach (array_merge($allFontKeys, $allColorKeys) as $key) {
            $value = $request->input($key);
            if ($value !== null && $value !== '') {
                Setting::set($key, trim($value));
            }
        }

        return redirect()
            ->route('admin.settings.typography')
            ->with('success', 'Typography settings saved successfully.');
    }

    private function fontLabels(): array
    {
        return [
            'base_font_size'          => 'Base / Body Font Size',
            'navbar_font_size'        => 'Navbar Font Size',
            'card_font_size'          => 'Card Text Font Size',
            'heading_font_size'       => 'Heading (H1 / H2) Font Size',
            'subheading_font_size'    => 'Sub-heading (H3 / H4) Font Size',
            'footer_font_size'        => 'Footer Font Size',
            'button_font_size'        => 'Button Font Size',
            'product_title_font_size' => 'Product Card Title Font Size',
            'product_price_font_size' => 'Product Price Font Size',
        ];
    }

    private function colorLabels(): array
    {
        return [
            'body_text_color'                => 'Body / Default Text Color',
            'heading_text_color'             => 'Heading Text Color',
            'muted_text_color'               => 'Muted / Secondary Text Color',
            'navbar_text_color'              => 'Navbar Text Color',
            'card_text_color'                => 'Card Text Color',
            'footer_text_color'              => 'Footer Text Color',
            'button_text_color'              => 'Button Text Color',
            'badge_text_color'               => 'Badge Text Color',
            'price_text_color'               => 'Price Text Color',
            'input_text_color'               => 'Input / Form Text Color',
            'product_title_text_color'       => 'Product Title Text Color',
            'product_description_text_color' => 'Product Description Text Color',
        ];
    }
}