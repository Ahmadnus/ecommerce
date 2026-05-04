<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CountryController extends Controller
{
    public function index(): View
    {
        $countries = Country::withCount('zones')->ordered()->get();

        return view('admin.countries.index', compact('countries'));
    }

    public function create(): View
    {
        $currencies = Currency::active()->orderBy('name')->get();

        return view('admin.countries.create', compact('currencies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCountry($request);

        $country = Country::create($validated);

        $this->syncCurrencies($country, $validated);

        return redirect()
            ->route('admin.countries.index')
            ->with('success', __('admin.countries.created', ['name' => $country->name]));
    }

    public function edit(Country $country): View
    {
        $country->load('currencies');
        $currencies        = Currency::active()->orderBy('name')->get();
        $attachedIds       = $country->currencies->pluck('id')->toArray();
        $defaultCurrencyId = $country->currencies
            ->where('pivot.is_default', true)
            ->first()?->id;

        return view('admin.countries.edit', compact(
            'country', 'currencies', 'attachedIds', 'defaultCurrencyId'
        ));
    }

    public function update(Request $request, Country $country): RedirectResponse
    {
        $validated = $this->validateCountry($request, $country);

        // For system records, silently discard any attempt to change immutable
        // fields before we even reach the model — double protection layer.
        if ($country->is_system) {
            foreach (Country::IMMUTABLE_SYSTEM_FIELDs as $field) {
                unset($validated[$field]);
            }
        }

        $country->update($validated);

        $this->syncCurrencies($country, $validated);

        return redirect()
            ->route('admin.countries.index')
            ->with('success', __('admin.countries.updated'));
    }

    public function destroy(Country $country): RedirectResponse
    {
        try {
            $country->delete();
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.countries.index')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.countries.index')
            ->with('success', __('admin.countries.deleted'));
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function validateCountry(Request $request, ?Country $country = null): array
    {
        $uniqueCode = 'required|string|max:3|unique:countries,code' .
                      ($country ? ',' . $country->id : '');

        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'name_en'          => 'nullable|string|max:100',
            'code'             => $uniqueCode,
            'calling_code'     => ['required', 'string', 'max:10', 'regex:/^\+?\d{1,10}$/'],
            'is_active'        => 'nullable|boolean',
            'sort_order'       => 'nullable|integer|min:0',
            'currencies'       => 'nullable|array',
            'currencies.*'     => 'exists:currencies,id',
            'default_currency' => 'nullable|exists:currencies,id',
        ], [
            'calling_code.required' => __('admin.countries.calling_code_required'),
            'calling_code.regex'    => __('admin.countries.calling_code_regex'),
        ]);

        $validated['is_active']    = $request->boolean('is_active', true);
        $validated['sort_order']   = $validated['sort_order'] ?? 0;
        $validated['code']         = strtoupper($validated['code']);
        $validated['calling_code'] = ltrim($validated['calling_code'], '+');

        return $validated;
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