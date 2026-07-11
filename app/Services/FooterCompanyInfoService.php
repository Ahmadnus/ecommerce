<?php

namespace App\Services;

use App\Models\FooterCompanyInfo;
use Illuminate\Support\Facades\DB;

/**
 * FooterCompanyInfoService — business logic for the admin footer company
 * info CRUD, including the flag icon media. Never returns views/redirects.
 */
class FooterCompanyInfoService
{
    public function getItems()
    {
        return FooterCompanyInfo::orderBy('sort_order')->get();
    }

    /**
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function create(array $attributes, $flagIcon): FooterCompanyInfo
    {
        try {
            return DB::transaction(function () use ($attributes, $flagIcon) {
                $item = FooterCompanyInfo::create($attributes);

                if ($flagIcon) {
                    $item->addMedia($flagIcon)->toMediaCollection('flag_icon');
                }

                return $item;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function update(FooterCompanyInfo $item, array $attributes, $flagIcon): FooterCompanyInfo
    {
        try {
            return DB::transaction(function () use ($item, $attributes, $flagIcon) {
                $item->update($attributes);

                if ($flagIcon) {
                    $item->addMedia($flagIcon)->toMediaCollection('flag_icon');
                }

                return $item;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    public function delete(FooterCompanyInfo $item): void
    {
        $item->delete();
    }
}
