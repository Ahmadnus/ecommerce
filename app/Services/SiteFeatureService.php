<?php

namespace App\Services;

use App\Models\SiteFeature;

/**
 * SiteFeatureService — business logic for the admin site-features CRUD.
 * Never returns views/redirects; throws on failure.
 */
class SiteFeatureService
{
    public function getFeatures()
    {
        return SiteFeature::orderBy('sort_order')->get();
    }

    /**
     * Create a feature from raw input (manual translation assignment,
     * matching the original controller behavior).
     *
     * $data keys: icon, title (ar/en), description (ar/en), sort_order,
     * is_active (bool).
     *
     * @throws \Exception on failure
     */
    public function create(array $data): SiteFeature
    {
        $feature = new SiteFeature();
        $feature->icon = $data['icon'] ?? null;
        $feature->setTranslation('title', 'ar', $data['title']['ar'] ?? null);
        $feature->setTranslation('title', 'en', $data['title']['en'] ?? null);
        $feature->setTranslation('description', 'ar', $data['description']['ar'] ?? null);
        $feature->setTranslation('description', 'en', $data['description']['en'] ?? null);
        $feature->sort_order = $data['sort_order'] ?? 0;
        $feature->is_active = $data['is_active'];
        $feature->save();

        return $feature;
    }

    public function update(SiteFeature $feature, array $data): SiteFeature
    {
        $feature->update([
            'icon'        => $data['icon'],
            'title'       => $data['title'],
            'description' => $data['description'],
            'sort_order'  => $data['sort_order'] ?? 0,
            'is_active'   => $data['is_active'],
        ]);

        return $feature;
    }

    public function delete(SiteFeature $feature): void
    {
        $feature->delete();
    }
}
