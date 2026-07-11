<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Facades\DB;

/**
 * PageService
 *
 * Business logic for the admin static-pages CRUD, including slug
 * generation and featured-image media handling.
 * Never returns views/redirects; throws on failure.
 */
class PageService
{
    /**
     * Active page by slug for the public dynamic-page route.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException (404)
     */
    public function findActiveBySlug(string $slug): Page
    {
        return Page::active()->where('slug', $slug)->firstOrFail();
    }

    public function getOrderedPages()
    {
        return Page::ordered()->get();
    }

    /**
     * Create a page with optional featured image.
     *
     * $data: name, content, slug (null → auto), sort_order, is_active (bool),
     * image_alt. $featuredImage: UploadedFile|null.
     *
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function create(array $data, $featuredImage): Page
    {
        try {
            return DB::transaction(function () use ($data, $featuredImage) {
                $arName = $data['name']['ar'] ?? null;
                $enName = $data['name']['en'] ?? null;

                $slug = !empty($data['slug'])
                    ? $data['slug']
                    : Page::uniqueSlug($arName ?: $enName);

                $page = Page::create([
                    'name'       => $data['name'],
                    'content'    => $data['content'],
                    'slug'       => $slug,
                    'sort_order' => $data['sort_order'] ?? 0,
                    'is_active'  => $data['is_active'],
                ]);

                // ── Featured image ────────────────────────────────────────────
                if ($featuredImage) {
                    $page->addMedia($featuredImage)
                         ->withCustomProperties(['alt' => $data['image_alt'] ?? ''])
                         ->toMediaCollection('featured');
                }

                return $page;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * Update a page; the slug regenerates only when the Arabic name changed.
     * Handles featured-image replace/remove/alt-text update.
     *
     * $data additionally carries the resolved boolean remove_image.
     *
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function update(Page $page, array $data, $featuredImage): Page
    {
        try {
            return DB::transaction(function () use ($page, $data, $featuredImage) {
                $arName = $data['name']['ar'] ?? null;
                $enName = $data['name']['en'] ?? null;

                // Regenerate slug only if Arabic name changed
                $slug = $page->slug;
                if ($arName !== $page->getTranslation('name', 'ar', false)) {
                    $slug = !empty($data['slug'])
                        ? $data['slug']
                        : Page::uniqueSlug($arName ?: $enName, $page->id);
                }

                $page->update([
                    'name'       => $data['name'],
                    'content'    => $data['content'],
                    'slug'       => $slug,
                    'sort_order' => $data['sort_order'] ?? 0,
                    'is_active'  => $data['is_active'],
                ]);

                // ── Featured image ────────────────────────────────────────────
                if ($featuredImage) {
                    // Clear existing image before adding new one
                    $page->clearMediaCollection('featured');

                    $page->addMedia($featuredImage)
                         ->withCustomProperties(['alt' => $data['image_alt'] ?? ''])
                         ->toMediaCollection('featured');
                } elseif ($data['remove_image']) {
                    // Explicit removal via hidden checkbox
                    $page->clearMediaCollection('featured');
                } else {
                    // No new file — update alt text on existing media if it changed
                    $existing = $page->getFirstMedia('featured');
                    // trim-check mirrors $request->filled('image_alt')
                    if ($existing && trim((string) ($data['image_alt'] ?? '')) !== '') {
                        $existing->setCustomProperty('alt', $data['image_alt']);
                        $existing->save();
                    }
                }

                return $page;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    public function delete(Page $page): void
    {
        $page->delete();
    }
}
