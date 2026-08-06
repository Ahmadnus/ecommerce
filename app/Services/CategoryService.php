<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * CategoryService
 *
 * Business logic for the admin category CRUD: tree listing, parent options,
 * create/update with Spatie media handling, and delete.
 * Never returns views/redirects; throws on failure.
 */
class CategoryService
{
    /**
     * Root categories tree for the admin index.
     */
    public function getCategoryTree()
    {
        return Category::withCount('products')
            ->with(['allChildren.products', 'media'])
            ->roots()
            ->orderBy('sort_order')
            ->orderBy('name')   // Spatie sorts by current locale automatically
            ->get();
    }

    /**
     * Parent options for the create form (all active categories).
     */
    public function getParentOptions()
    {
        return Category::active()
            ->with('media')
            ->orderBy('depth')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Parent options for the edit form — excludes the category itself
     * and all of its descendants.
     */
    public function getParentOptionsForEdit(Category $category)
    {
        $descendantIds = $category->getAllDescendants()->pluck('id')->push($category->id);

        return Category::active()
            ->with('media')
            ->whereNotIn('id', $descendantIds)
            ->orderBy('depth')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Would assigning $parentId as parent of $category create a cycle?
     */
    public function wouldCreateCycle(Category $category, $parentId): bool
    {
        $descendants = $category->getAllDescendants()->pluck('id');

        return $descendants->contains($parentId) || $parentId == $category->id;
    }

    /**
     * Create a category with optional image + banner.
     *
     * $data: the validated request data (name, description, slug, parent_id,
     * sort_order) plus resolved booleans is_active / banner_is_active.
     *
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function create(array $data, $image, $bannerImage): Category
    {
        try {
            return DB::transaction(function () use ($data, $image, $bannerImage) {
                $arName = $data['name']['ar'] ?? null;
                $enName = $data['name']['en'] ?? null;

                $category = Category::create([
                    // Pass the full array — Spatie stores it as {"ar": "...", "en": "..."}
                    'name'             => $data['name'],
                    'description'      => $data['description'] ?? null,
                    'slug'             => !empty($data['slug'])
                                            ? $data['slug']
                                            : Str::slug($arName ?: $enName),
                    'parent_id'        => $data['parent_id'] ?? null,
                    'sort_order'       => $data['sort_order'] ?? 0,
                    'is_active'        => $data['is_active'],
                    'banner_is_active' => $data['banner_is_active'],
                ]);

                if ($image) {
                    $category
                        ->addMedia($image)
                        ->usingName($arName ?: $enName)
                        ->usingFileName(Str::slug($arName ?: $enName) . '.' . $image->extension())
                        ->toMediaCollection('category_images');
                }

                if ($bannerImage) {
                    $category
                        ->addMedia($bannerImage)
                        ->usingName(($arName ?: $enName) . ' Banner')
                        ->usingFileName(Str::slug($arName ?: $enName) . '-banner.' . $bannerImage->extension())
                        ->toMediaCollection('category_banner');
                }

                return $category;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * Update a category, its slug (regenerated only when the Arabic name
     * changed), and its image/banner media.
     *
     * $data additionally carries resolved booleans remove_image /
     * remove_banner_image.
     *
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function update(Category $category, array $data, $image, $bannerImage): Category
    {
        try {
            return DB::transaction(function () use ($category, $data, $image, $bannerImage) {
                $arName = $data['name']['ar'] ?? null;
                $enName = $data['name']['en'] ?? null;

                // Regenerate slug only if Arabic name changed
                $slug = $category->slug;
                if ($arName !== $category->getTranslation('name', 'ar', false)) {
                    $slug = !empty($data['slug'])
                        ? $data['slug']
                        : Str::slug($arName ?: $enName);
                }

                $category->update([
                    'name'             => $data['name'],
                    'description'      => $data['description'] ?? null,
                    'slug'             => $slug,
                    'parent_id'        => $data['parent_id'] ?? null,
                    'sort_order'       => $data['sort_order'] ?? 0,
                    'is_active'        => $data['is_active'],
                    'banner_is_active' => $data['banner_is_active'],
                ]);

                // ── Image handling ─────────────────────────────────────────────
                if ($data['remove_image']) {
                    $category->clearMediaCollection('category_images');
                    $category->clearMediaCollection('categories'); // legacy
                }

                if ($image) {
                    $category->clearMediaCollection('category_images');
                    $category
                        ->addMedia($image)
                        ->usingName($arName ?: $enName)
                        ->usingFileName(Str::slug($arName ?: $enName) . '.' . $image->extension())
                        ->toMediaCollection('category_images');
                }

                if ($data['remove_banner_image']) {
                    $category->clearMediaCollection('category_banner');
                }

                if ($bannerImage) {
                    $category->clearMediaCollection('category_banner');
                    $category
                        ->addMedia($bannerImage)
                        ->usingName(($arName ?: $enName) . ' Banner')
                        ->usingFileName(Str::slug($arName ?: $enName) . '-banner.' . $bannerImage->extension())
                        ->toMediaCollection('category_banner');
                }

                return $category;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
