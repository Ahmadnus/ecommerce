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

    public function update(SocialLink $link, array $data): SocialLink
    {
        $link->update($data);

        return $link;
    }

    /**
     * Flip a boolean column on a link. Used by the enable/disable and
     * "show as floating button" switches in the admin list.
     */
    public function toggle(SocialLink $link, string $column): SocialLink
    {
        abort_unless(in_array($column, ['is_active', 'is_floating'], true), 400);

        /*
         * Only one link can drive the floating button, otherwise several would
         * stack in the same corner. Turning one on turns the others off.
         */
        if ($column === 'is_floating' && ! $link->is_floating) {
            SocialLink::where('id', '!=', $link->id)->update(['is_floating' => false]);
        }

        $link->update([$column => ! $link->{$column}]);

        return $link;
    }

    public function delete(SocialLink $socialLink): void
    {
        $socialLink->delete();
    }
}
