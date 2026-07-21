<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Services\HomepageSectionService;
use Illuminate\Http\Request;

class HomepageSectionController extends Controller
{
    public function __construct(
        private readonly HomepageSectionService $sections,
    ) {}

    public function index()
    {
        $items = $this->sections->getAll();

        return view('admin.homepage-sections.index', compact('items'));
    }

    public function store(Request $request)
    {
        $mediaType = $request->input('media_type', 'none');

        $request->validate($this->rules($mediaType, true));

        try {
            $this->sections->create(
                attributes: [
                    'title'       => $request->input('title'),
                    'paragraph'   => $request->input('paragraph'),
                    'media_type'  => $mediaType,
                    'position'    => $request->input('position'),
                    'button_text' => $request->input('button_text'),
                    'button_url'  => $request->input('button_url'),
                    'link_text'   => $request->input('link_text'),
                    'link_url'    => $request->input('link_url'),
                    'link_color'  => $request->input('link_color'),
                    'link_font_family' => $request->input('link_font_family', 'default'),
                    'link_style'  => $request->input('link_style', 'underline'),
                    'section_title_accent_color' => $request->input('section_title_accent_color'),
                    'text_color'         => $request->input('text_color'),
                    'title_font_family'     => $request->input('title_font_family', 'default'),
                    'paragraph_font_family' => $request->input('paragraph_font_family', 'default'),
                    'button_bg_color'    => $request->input('button_bg_color'),
                    'button_text_color'  => $request->input('button_text_color'),
                    'text_alignment'     => $request->input('text_alignment'),
                    'show_text_below_media' => $request->boolean('show_text_below_media', true),
                    'is_active'   => $request->boolean('is_active', true),
                    'sort_order'  => $request->input('sort_order', 0),
                ],
                file: $request->hasFile('media') ? $request->file('media') : null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة القسم. يرجى المحاولة مرة أخرى.');
        }

        return back()->with('success', 'تم إضافة القسم بنجاح');
    }

    public function update(Request $request, HomepageSection $homepageSection)
    {
        $mediaType = $request->input('media_type', $homepageSection->media_type);

        $request->validate($this->rules($mediaType, false));

        try {
            $this->sections->update(
                section: $homepageSection,
                attributes: [
                    'title'       => $request->input('title'),
                    'paragraph'   => $request->input('paragraph'),
                    'media_type'  => $mediaType,
                    'position'    => $request->input('position', $homepageSection->position),
                    'button_text' => $request->input('button_text'),
                    'button_url'  => $request->input('button_url'),
                    'link_text'   => $request->input('link_text'),
                    'link_url'    => $request->input('link_url'),
                    'link_color'  => $request->input('link_color'),
                    'link_font_family' => $request->input('link_font_family', 'default'),
                    'link_style'  => $request->input('link_style', 'underline'),
                    'section_title_accent_color' => $request->input('section_title_accent_color'),
                    'text_color'         => $request->input('text_color'),
                    'title_font_family'     => $request->input('title_font_family', 'default'),
                    'paragraph_font_family' => $request->input('paragraph_font_family', 'default'),
                    'button_bg_color'    => $request->input('button_bg_color'),
                    'button_text_color'  => $request->input('button_text_color'),
                    'text_alignment'     => $request->input('text_alignment'),
                    'show_text_below_media' => $request->boolean('show_text_below_media', true),
                    'is_active'   => $request->boolean('is_active', false),
                    'sort_order'  => $request->input('sort_order', $homepageSection->sort_order ?? 0),
                ],
                file: $request->hasFile('media') ? $request->file('media') : null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث القسم. يرجى المحاولة مرة أخرى.');
        }

        return back()->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(HomepageSection $homepageSection)
    {
        $this->sections->delete($homepageSection);

        return back()->with('success', 'تم حذف القسم بنجاح');
    }

    private function rules(string $mediaType, bool $requireFile): array
    {
        $needsFile = $requireFile && in_array($mediaType, ['image', 'video'], true);

        $fileRule = $mediaType === 'video'
            ? 'mimes:mp4,webm|max:51200'
            : 'mimes:jpg,jpeg,png,webp|max:10240';

        return [
            'title'       => 'nullable|string|max:255',
            'paragraph'   => 'nullable|string',
            'media_type'  => 'required|in:image,video,none',
            'position'    => 'required|in:top_hero,below_categories,above_footer',
            'button_text' => 'nullable|string|max:100',
            'button_url'  => 'nullable|string|max:500',
            'link_text'   => 'nullable|string|max:100',
            'link_url'    => 'nullable|string|max:500',
            // Array syntax (not a pipe-string) — the regex itself contains a
            // "|" alternation, which a pipe-delimited rule string would
            // split on and mangle into an unterminated pattern.
            'section_title_accent_color' => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'text_color'        => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'button_bg_color'   => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'button_text_color' => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'link_color'        => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'title_font_family'     => 'nullable|in:default,serif,serif_italic,sans_serif',
            'paragraph_font_family' => 'nullable|in:default,serif,serif_italic,sans_serif',
            'link_font_family'      => 'nullable|in:default,serif,serif_italic,sans_serif',
            'link_style'            => 'nullable|in:underline,button',
            'text_alignment' => 'nullable|in:left,center,right',
            'show_text_below_media' => 'nullable|boolean',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer|min:0',
            'media'       => ($needsFile ? 'required|' : 'nullable|') . $fileRule,
        ];
    }
}
