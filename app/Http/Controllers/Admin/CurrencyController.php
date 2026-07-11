<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Services\CurrencyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    public function __construct(
        private readonly CurrencyService $currencies,
    ) {}

    public function index(): View
    {
        $currencies = $this->currencies->getAllForAdmin();

        return view('admin.currencies.index', compact('currencies'));
    }

    public function create(): View
    {
        return view('admin.currencies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'code'          => 'required|string|max:10|unique:currencies,code',
            'symbol'        => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001',
            'is_base'       => 'nullable|boolean',
            'is_active'     => 'nullable|boolean',
        ], [
            'name.required'          => 'اسم العملة مطلوب.',
            'code.required'          => 'رمز العملة مطلوب.',
            'code.unique'            => 'رمز العملة هذا موجود مسبقاً.',
            'symbol.required'        => 'رمز العرض مطلوب (مثل: $، ل.س).',
            'exchange_rate.required' => 'سعر الصرف مطلوب.',
        ]);

        $validated['code'] = strtoupper($validated['code']);

        $validated['is_base'] = $request->has('is_base')
            ? $request->boolean('is_base')
            : false;

        $validated['is_active'] = $request->has('is_active')
            ? $request->boolean('is_active')
            : true;

        try {
            $this->currencies->createCurrency($validated);
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة العملة. يرجى المحاولة مرة أخرى.');
        }

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'تمت إضافة العملة بنجاح.');
    }

    public function edit(Currency $currency): View
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'code'          => 'required|string|max:10|unique:currencies,code,' . $currency->id,
            'symbol'        => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001',
            'is_base'       => 'nullable|boolean',
            'is_active'     => 'nullable|boolean',
        ], [
            'name.required'          => 'اسم العملة مطلوب.',
            'code.required'          => 'رمز العملة مطلوب.',
            'code.unique'            => 'رمز العملة هذا موجود مسبقاً.',
            'symbol.required'        => 'رمز العرض مطلوب (مثل: $، ل.س).',
            'exchange_rate.required' => 'سعر الصرف مطلوب.',
        ]);

        $validated['code'] = strtoupper($validated['code']);

        $validated['is_base'] = $request->has('is_base')
            ? $request->boolean('is_base')
            : $currency->is_base;

        $validated['is_active'] = $request->has('is_active')
            ? $request->boolean('is_active')
            : $currency->is_active;

        try {
            $this->currencies->updateCurrency($currency, $validated);
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث العملة. يرجى المحاولة مرة أخرى.');
        }

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'تم تحديث العملة بنجاح.');
    }

    public function destroy(Currency $currency): RedirectResponse
    {
        if ($currency->is_base) {
            return back()->with('error', 'لا يمكن حذف العملة الأساسية.');
        }

        $this->currencies->deleteCurrency($currency);

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'تم حذف العملة.');
    }

    public function setCurrency(Request $request): RedirectResponse
    {
        $request->validate([
            'currency_code' => 'required|exists:currencies,code',
        ]);

        session(['currency_code' => $request->currency_code]);

        return back();
    }
}
