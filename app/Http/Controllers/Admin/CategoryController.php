<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categories,
    ) {}

    public function index()
    {
        $categories = $this->categories->getCategoryTree();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(Request $request)
    {
        $parentOptions = $this->categories->getParentOptions();

        $preselectedParentId = $request->query('parent_id');

        return view('admin.categories.create', compact('parentOptions', 'preselectedParentId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // ── Translatable ────────────────────────────────────────────────
            'name.ar'         => 'required|string|max:255',
            'name.en'         => 'nullable|string|max:255',
            'description.ar'  => 'nullable|string',
            'description.en'  => 'nullable|string',
            // ────────────────────────────────────────────────────────────────
            'slug'            => 'nullable|string|unique:categories,slug',
            'parent_id'       => 'nullable|exists:categories,id',
            'sort_order'      => 'nullable|integer|min:0',
            'is_active'       => 'nullable|boolean',
            'image'           => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp,gif',
            'banner_image'    => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp,gif',
            'banner_is_active'=> 'nullable|boolean',
        ], [
            'name.ar.required' => 'اسم التصنيف بالعربية مطلوب',
            'image.max'        => 'حجم الصورة يجب أن لا يتجاوز 4 ميجابايت',
        ]);

        try {
            $this->categories->create(
                data: [
                    'name'             => $request->input('name'),
                    'description'      => $request->input('description'),
                    'slug'             => $request->filled('slug') ? $request->slug : null,
                    'parent_id'        => $request->parent_id,
                    'sort_order'       => $request->input('sort_order', 0),
                    'is_active'        => $request->boolean('is_active', true),
                    'banner_is_active' => $request->boolean('banner_is_active', false),
                ],
                image: $request->hasFile('image') ? $request->file('image') : null,
                bannerImage: $request->hasFile('banner_image') ? $request->file('banner_image') : null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة التصنيف. يرجى المحاولة مرة أخرى.');
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم إضافة التصنيف "' . $request->input('name.ar') . '" بنجاح');
    }

    public function edit(Category $category)
    {
        $parentOptions = $this->categories->getParentOptionsForEdit($category);

        return view('admin.categories.edit', compact('category', 'parentOptions'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            // ── Translatable ────────────────────────────────────────────────
            'name.ar'         => 'required|string|max:255',
            'name.en'         => 'nullable|string|max:255',
            'description.ar'  => 'nullable|string',
            'description.en'  => 'nullable|string',
            // ────────────────────────────────────────────────────────────────
            'slug'            => 'nullable|string|unique:categories,slug,' . $category->id,

            'parent_id'       => 'nullable|exists:categories,id',
            'sort_order'      => 'nullable|integer|min:0',
            'is_active'       => 'nullable|boolean',
            'image'           => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp,gif',
            'remove_image'    => 'nullable|boolean',
            'banner_image'    => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp,gif',
            'remove_banner_image' => 'nullable|boolean',
            'banner_is_active'=> 'nullable|boolean',
        ]);

        // Prevent circular parent
        if ($request->filled('parent_id')
            && $this->categories->wouldCreateCycle($category, $request->parent_id)) {
            return back()->withErrors(['parent_id' => 'لا يمكن تعيين تصنيف فرعي كأب.']);
        }

        try {
            $this->categories->update(
                category: $category,
                data: [
                    'name'                => $request->input('name'),
                    'description'         => $request->input('description'),
                    'slug'                => $request->filled('slug') ? $request->slug : null,
                    'parent_id'           => $request->parent_id,
                    'sort_order'          => $request->input('sort_order', 0),
                    'is_active'           => $request->boolean('is_active', true),
                    'banner_is_active'    => $request->boolean('banner_is_active', false),
                    'remove_image'        => $request->boolean('remove_image'),
                    'remove_banner_image' => $request->boolean('remove_banner_image'),
                ],
                image: $request->hasFile('image') ? $request->file('image') : null,
                bannerImage: $request->hasFile('banner_image') ? $request->file('banner_image') : null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث التصنيف. يرجى المحاولة مرة أخرى.');
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم تحديث التصنيف "' . $request->input('name.ar') . '" بنجاح');
    }

    public function destroy(Category $category)
    {
        $this->categories->delete($category);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم حذف التصنيف');
    }
}
