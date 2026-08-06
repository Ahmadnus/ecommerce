<?php

namespace App\Services;

use App\Models\Category;
use App\Models\HomeSection;

/**
 * HomeSectionService — business logic for the dynamic home page sections
 * (admin CRUD + drag-drop reorder). Never returns views/redirects.
 */
class HomeSectionService
{
    public function getSections()
    {
        return HomeSection::with('category')
            ->ordered()
            ->get();
    }

    public function getFormData(): array
    {
        $categories = Category::active()->orderBy('sort_order')->get();
        $typeLabels = HomeSection::typeLabels();

        return compact('categories', 'typeLabels');
    }

    public function create(array $validated): HomeSection
    {
        return HomeSection::create($validated);
    }

    public function update(HomeSection $homeSection, array $validated): HomeSection
    {
        $homeSection->update($validated);

        return $homeSection;
    }

    public function delete(HomeSection $homeSection): void
    {
        $homeSection->delete();
    }

    /**
     * Persist a new display order. $order: array of IDs in new order.
     */
    public function reorder(array $order): void
    {
        foreach ($order as $position => $id) {
            HomeSection::where('id', $id)->update(['sort_order' => $position + 1]);
        }
    }
}
