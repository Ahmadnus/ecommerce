<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

/**
 * CountryService
 *
 * Business logic for the admin countries CRUD, including the
 * country↔currency pivot sync and system-record field protection.
 * Never returns views/redirects; throws on failure.
 */
class CountryService
{
    public function getCountriesWithZoneCounts()
    {
        return Country::withCount('zones')->ordered()->get();
    }

    public function getActiveCurrencies()
    {
        return Currency::active()->orderBy('name')->get();
    }

    /**
     * Data the edit form needs for a country.
     */
    public function getEditData(Country $country): array
    {
        $country->load('currencies');
        $currencies        = $this->getActiveCurrencies();
        $attachedIds       = $country->currencies->pluck('id')->toArray();
        $defaultCurrencyId = $country->currencies
            ->where('pivot.is_default', true)
            ->first()?->id;

        return compact('country', 'currencies', 'attachedIds', 'defaultCurrencyId');
    }

    /**
     * Create a country and sync its currencies.
     *
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function create(array $validated): Country
    {
        try {
            return DB::transaction(function () use ($validated) {
                $country = Country::create($validated);

                $this->syncCurrencies($country, $validated);

                return $country;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * Update a country and sync its currencies. For system records,
     * immutable fields are silently discarded before the update — double
     * protection layer on top of the model's own guard.
     *
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function update(Country $country, array $validated): Country
    {
        if ($country->is_system) {
            foreach (Country::IMMUTABLE_SYSTEM_FIELDs as $field) {
                unset($validated[$field]);
            }
        }

        try {
            return DB::transaction(function () use ($country, $validated) {
                $country->update($validated);

                $this->syncCurrencies($country, $validated);

                return $country;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * Delete a country.
     *
     * @throws \Illuminate\Validation\ValidationException when the model
     *         forbids deletion (propagated to the controller).
     */
    public function delete(Country $country): void
    {
        $country->delete();
    }

    private function syncCurrencies(Country $country, array $validated): void
    {
        $pivot = [];

        foreach ($validated['currencies'] ?? [] as $currencyId) {
            $pivot[(int) $currencyId] = [
                'is_default' => (int) $currencyId === (int) ($validated['default_currency'] ?? 0),
            ];
        }

        $country->currencies()->sync($pivot);
    }
}
