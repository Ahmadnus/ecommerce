<?php

namespace App\Services;

use App\Models\Setting;

/**
 * LocaleModeService — business logic for the admin language-mode setting
 * (langsetting: ar / en / both). Never returns views/redirects.
 */
class LocaleModeService
{
    public function getMode(): string
    {
        return Setting::where('key', 'langsetting')->value('value') ?? 'both';
    }

    /**
     * Persist the mode when valid (ar/en/both) and clear its cache.
     * Invalid values are silently ignored, matching the original behavior.
     */
    public function setMode(?string $mode): void
    {
        if (in_array($mode, ['ar', 'en', 'both'])) {
            Setting::updateOrCreate(
                ['key' => 'langsetting'],
                ['value' => $mode]
            );
            cache()->forget('langsetting');
        }
    }
}
