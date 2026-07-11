<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use App\Services\HeroBannerService;
use Illuminate\Http\Request;

class HeroBannerController extends Controller
{
    public function __construct(
        private readonly HeroBannerService $banners,
    ) {}

    public function index()
    {
        $banners = $this->banners->getBanners();

        return view('admin.hero-banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $layout = $request->input('layout', 'text_image');

        $request->validate($this->rules($layout), [
            'title.ar.required'       => 'العنوان الرئيسي بالعربية مطلوب',
            'button_text.ar.required' => 'نص الزر بالعربية مطلوب',
            'image.required'          => 'الصورة مطلوبة عند اختيار صورة فقط',
        ]);

        try {
            $this->banners->create(
                attributes: [
                    'title'            => $request->input('title', []),
                    'subtitle'         => $request->input('subtitle', []),
                    'badge'            => $request->input('badge', []),
                    'description'      => $request->input('description', []),
                    'button_text'      => $request->input('button_text', []),
                    'button_url'       => $request->input('button_url'),
                    'position'         => $request->input('position', 'top'),
                    'layout'           => $layout,
                    'sort_order'       => $request->input('sort_order', 0),
                    'is_active'        => $request->boolean('is_active', true),
                    'background_color' => $request->input('background_color', '#0ea5e9'),
                    'text_color'       => $request->input('text_color', '#ffffff'),
                ],
                image: $request->hasFile('image') ? $request->file('image') : null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة البانر. يرجى المحاولة مرة أخرى.');
        }

        return back()->with('success', 'تم إضافة البانر بنجاح');
    }

    public function update(Request $request, HeroBanner $heroBanner)
    {
        $layout = $request->input('layout', $heroBanner->layout ?? 'text_image');

        $request->validate($this->rules($layout), [
            'title.ar.required'       => 'العنوان الرئيسي بالعربية مطلوب',
            'button_text.ar.required' => 'نص الزر بالعربية مطلوب',
            'image.required'          => 'الصورة مطلوبة عند اختيار صورة فقط',
        ]);

        try {
            $this->banners->update(
                banner: $heroBanner,
                attributes: [
                    'title'            => $request->input('title', []),
                    'subtitle'         => $request->input('subtitle', []),
                    'badge'            => $request->input('badge', []),
                    'description'      => $request->input('description', []),
                    'button_text'      => $request->input('button_text', []),
                    'button_url'       => $request->input('button_url'),
                    'position'         => $request->input('position', $heroBanner->position),
                    'layout'           => $layout,
                    'sort_order'       => $request->input('sort_order', $heroBanner->sort_order ?? 0),
                    'is_active'        => $request->boolean('is_active', false),
                    'background_color' => $request->input('background_color', '#0ea5e9'),
                    'text_color'       => $request->input('text_color', '#ffffff'),
                ],
                image: $request->hasFile('image') ? $request->file('image') : null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث البانر. يرجى المحاولة مرة أخرى.');
        }

        return back()->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(HeroBanner $heroBanner)
    {
        $this->banners->delete($heroBanner);

        return back()->with('success', 'تم حذف البانر بنجاح');
    }

    private function rules(string $layout): array
    {
        $isImageOnly = $layout === 'image_only';

        return [
            'layout' => 'required|in:text_image,text_only,image_only',

            'title.ar'       => $isImageOnly ? 'nullable|string|max:255' : 'required|string|max:255',
            'title.en'       => 'nullable|string|max:255',

            'subtitle.ar'    => 'nullable|string|max:255',
            'subtitle.en'    => 'nullable|string|max:255',

            'badge.ar'       => 'nullable|string|max:255',
            'badge.en'       => 'nullable|string|max:255',

            'description.ar' => 'nullable|string',
            'description.en' => 'nullable|string',

            'button_text.ar' => $isImageOnly ? 'nullable|string|max:100' : 'required|string|max:100',
            'button_text.en' => 'nullable|string|max:100',

            'button_url'       => 'nullable|string|max:500',
            'position'         => 'required|in:top,after_featured,after_products',
            'background_color' => 'nullable|string|max:20',
            'text_color'       => 'nullable|string|max:20',

            'image'     => $isImageOnly ? 'required|mimes:jpg,jpeg,png,webp|max:10240' : 'nullable|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'nullable|boolean',
            'sort_order'=> 'nullable|integer|min:0',
        ];
    }
}
