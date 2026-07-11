<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CountryController extends Controller
{
    public function __construct(
        private readonly CountryService $countries,
    ) {}

    public function index(): View
    {
        $countries = $this->countries->getCountriesWithZoneCounts();

        return view('admin.countries.index', compact('countries'));
    }

    public function create(): View
    {
        $currencies = $this->countries->getActiveCurrencies();

        return view('admin.countries.create', compact('currencies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCountry($request);

        try {
            $country = $this->countries->create($validated);
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الدولة. يرجى المحاولة مرة أخرى.');
        }

        return redirect()
            ->route('admin.countries.index')
            ->with('success', __('admin.countries.created', ['name' => $country->name]));
    }

    public function edit(Country $country): View
    {
        return view('admin.countries.edit', $this->countries->getEditData($country));
    }

    public function update(Request $request, Country $country): RedirectResponse
    {
        $validated = $this->validateCountry($request, $country);

        try {
            $this->countries->update($country, $validated);
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الدولة. يرجى المحاولة مرة أخرى.');
        }

        return redirect()
            ->route('admin.countries.index')
            ->with('success', __('admin.countries.updated'));
    }

    public function destroy(Country $country): RedirectResponse
    {
        try {
            $this->countries->delete($country);
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
}
