<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CountryController extends Controller
{
    public function index(): View
    {
        $countries = Country::withCount('zones')
            ->ordered()
            ->get();

        return view('admin.countries.index', compact('countries'));
    }

    public function create(): View
    {
        $currencies = Currency::active()->orderBy('name')->get();
        return view('admin.countries.create', compact('currencies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'name_en'          => 'nullable|string|max:100',
            'code'             => 'required|string|max:3|unique:countries,code',
            'calling_code'     => ['required', 'string', 'max:10', 'regex:/^\+?\d{1,10}$/'],
            'is_active'        => 'nullable|boolean',
            'sort_order'       => 'nullable|integer|min:0',
            'currencies'       => 'nullable|array',
            'currencies.*'     => 'exists:currencies,id',
            'default_currency' => 'nullable|exists:currencies,id',
        ], [
            'calling_code.required' => 'رمز الاتصال الدولي مطلوب.',
            'calling_code.regex'    => 'رمز الاتصال يجب أن يحتوي على أرقام فقط (مثال: 963 أو +963).',
        ]);

        $validated['is_active']    = $request->boolean('is_active', true);
        $validated['sort_order']   = $validated['sort_order'] ?? 0;
        $validated['code']         = strtoupper($validated['code']);
        // Normalise: strip leading "+" so we always store digits only (963 not +963)
        $validated['calling_code'] = ltrim($validated['calling_code'], '+');

        $country = Country::create($validated);

        if (!empty($validated['currencies'])) {
            $pivot = [];
            foreach ($validated['currencies'] as $currencyId) {
                $pivot[(int) $currencyId] = [
                    'is_default' => (int) $currencyId === (int) ($validated['default_currency'] ?? 0),
                ];
            }
            $country->currencies()->attach($pivot);
        }

        return redirect()
            ->route('admin.countries.index')
            ->with('success', 'تم إضافة الدولة "' . $country->name . '" بنجاح.');
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
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'name_en'          => 'nullable|string|max:100',
            'code'             => 'required|string|max:3|unique:countries,code,' . $country->id,
            'calling_code'     => ['required', 'string', 'max:10', 'regex:/^\+?\d{1,10}$/'],
            'is_active'        => 'nullable|boolean',
            'sort_order'       => 'nullable|integer|min:0',
            'currencies'       => 'nullable|array',
            'currencies.*'     => 'exists:currencies,id',
            'default_currency' => 'nullable|exists:currencies,id',
        ], [
            'calling_code.required' => 'رمز الاتصال الدولي مطلوب.',
            'calling_code.regex'    => 'رمز الاتصال يجب أن يحتوي على أرقام فقط (مثال: 963 أو +963).',
        ]);

        $validated['is_active']    = $request->boolean('is_active', true);
        $validated['sort_order']   = $validated['sort_order'] ?? 0;
        $validated['code']         = strtoupper($validated['code']);
        $validated['calling_code'] = ltrim($validated['calling_code'], '+');

        $country->update($validated);

        $pivot = [];
        foreach ($validated['currencies'] ?? [] as $currencyId) {
            $pivot[(int) $currencyId] = [
                'is_default' => (int) $currencyId === (int) ($validated['default_currency'] ?? 0),
            ];
        }
        $country->currencies()->sync($pivot);

        return redirect()
            ->route('admin.countries.index')
            ->with('success', 'تم تحديث الدولة بنجاح.');
    }

    public function destroy(Country $country): RedirectResponse
    {
        $country->delete();
        return redirect()
            ->route('admin.countries.index')
            ->with('success', 'تم حذف الدولة.');
    }
}