<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TopHeroMedia;
use App\Services\TopHeroMediaService;
use Illuminate\Http\Request;

class TopHeroMediaController extends Controller
{
    public function __construct(
        private readonly TopHeroMediaService $heroMedia,
    ) {}

    public function index()
    {
        $items = $this->heroMedia->getAll();

        return view('admin.top-hero-media.index', compact('items'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type', 'image');

        $request->validate($this->rules($type, true), [
            'media.required' => 'الملف مطلوب عند إضافة عنصر جديد',
        ]);

        try {
            $this->heroMedia->create(
                attributes: [
                    'type'       => $type,
                    'position'   => $request->input('position', 'top'),
                    'link_url'   => $request->input('link_url'),
                    'is_active'  => $request->boolean('is_active', true),
                    'sort_order' => $request->input('sort_order', 0),
                ],
                file: $request->hasFile('media') ? $request->file('media') : null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة العنصر. يرجى المحاولة مرة أخرى.');
        }

        return back()->with('success', 'تم إضافة عنصر الهيرو بنجاح');
    }

    public function update(Request $request, TopHeroMedia $topHeroMedium)
    {
        $type = $request->input('type', $topHeroMedium->type);

        $request->validate($this->rules($type, false));

        try {
            $this->heroMedia->update(
                hero: $topHeroMedium,
                attributes: [
                    'type'       => $type,
                    'position'   => $request->input('position', $topHeroMedium->position),
                    'link_url'   => $request->input('link_url'),
                    'is_active'  => $request->boolean('is_active', false),
                    'sort_order' => $request->input('sort_order', $topHeroMedium->sort_order ?? 0),
                ],
                file: $request->hasFile('media') ? $request->file('media') : null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث العنصر. يرجى المحاولة مرة أخرى.');
        }

        return back()->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(TopHeroMedia $topHeroMedium)
    {
        $this->heroMedia->delete($topHeroMedium);

        return back()->with('success', 'تم حذف العنصر بنجاح');
    }

    private function rules(string $type, bool $requireFile): array
    {
        $isVideo = $type === 'video';

        $fileRule = $isVideo
            ? 'mimes:mp4,webm|max:51200'
            : 'mimes:jpg,jpeg,png,webp|max:10240';

        return [
            'type'       => 'required|in:image,video',
            'position'   => 'nullable|in:top,middle,bottom',
            'link_url'   => 'nullable|url|max:500',
            'is_active'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'media'      => ($requireFile ? 'required|' : 'nullable|') . $fileRule,
        ];
    }
}
