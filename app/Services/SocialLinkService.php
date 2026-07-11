<?php

namespace App\Services;

use App\Models\SocialLink;

/**
 * SocialLinkService — business logic for the admin social links CRUD.
 * Never returns views/redirects.
 */
class SocialLinkService
{
    public function getLinks()
    {
        return SocialLink::orderBy('sort_order')->get();
    }

    public function create(array $data): SocialLink
    {
        return SocialLink::create($data);
    }

    /**
     * Create a link with an optional icon image (storefront-admin variant).
     */
    public function createWithIcon(array $data, $icon): SocialLink
    {
        $link = SocialLink::create($data);

        if ($icon) {
            $link->addMedia($icon)->toMediaCollection('icons');
        }

        return $link;
    }

    public function delete(SocialLink $socialLink): void
    {
        $socialLink->delete();
    }
}
