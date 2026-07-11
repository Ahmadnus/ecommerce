<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * LocaleModeService — business logic for the admin language-mode setting
 * (langsetting: ar / en / both). Never returns views/redirects.
 */
class LocaleModeService
{
    public function getMode(): string
    {
        return DB::table('settings')->where('key', 'langsetting')->value('value') ?? 'both';
    }

    /**
     * Persist the mode when valid (ar/en/both) and clear its cache.
     * Invalid values are silently ignored, matching the original behavior.
     */
    public function setMode(?string $mode): void
    {
        if (in_array($mode, ['ar', 'en', 'both'])) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'langsetting'],
                ['value' => $mode]
            );
            cache()->forget('langsetting');
        }
    }
}
