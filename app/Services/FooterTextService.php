<?php

namespace App\Services;

use App\Models\FooterText;

/**
 * FooterTextService — business logic for the admin footer texts CRUD.
 * Never returns views/redirects.
 */
class FooterTextService
{
    public function getItems()
    {
        return FooterText::orderBy('sort_order')->latest()->get();
    }

    /**
     * $data keys: slug, text_ar, text_en, sort_order, is_active (bool).
     */
    public function create(array $data): FooterText
    {
        $item = FooterText::create([
            'slug'       => $data['slug'],
            'is_active'  => $data['is_active'],
            'sort_order' => $data['sort_order'] ?? 0,
            'text' => [
                'ar' => $data['text_ar'],
                'en' => $data['text_en'],
            ],
        ]);

        $item->save();

        return $item;
    }

    public function delete(FooterText $footerText): void
    {
        $footerText->delete();
    }
}
