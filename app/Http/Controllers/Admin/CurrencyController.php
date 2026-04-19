<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    public function index(): View
    {
        $currencies = Currency::orderBy('is_base', 'desc')->orderBy('name')->get();
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

        $validated['code']      = strtoupper($validated['code']);
        $validated['is_base']   = $request->boolean('is_base', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        // Only one base currency allowed
        if ($validated['is_base']) {
            Currency::where('is_base', true)->update(['is_base' => false]);
        }

        Currency::create($validated);

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
    ]);

    $validated['code']      = strtoupper($validated['code']);
    $validated['is_base']   = $request->boolean('is_base', false);
    $validated['is_active'] = $request->boolean('is_active', true);

    // المنطق الجديد: إذا أصبحت هذه العملة هي الأساسية
    if ($validated['is_base'] && !$currency->is_base) {
        // نجعل العملة الأساسية القديمة غير أساسية وغير مفعلة أيضاً
        Currency::where('is_base', true)->update([
            'is_base' => false,
            'is_active' => false // السطر الذي طلبته
        ]);
    }

    $currency->update($validated);

    return redirect()
        ->route('admin.currencies.index')
        ->with('success', 'تم تحديث العملة بنجاح.');
}

    public function destroy(Currency $currency): RedirectResponse
    {
        if ($currency->is_base) {
            return back()->with('error', 'لا يمكن حذف العملة الأساسية.');
        }
        $currency->delete();
        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'تم حذف العملة.');
    }

    // داخل CurrencyController.php
public function setCurrency(Request $request)
{
    $request->validate(['currency_code' => 'required|exists:currencies,code']);
    
    // حفظ رمز العملة في السيشين
    session(['currency_code' => $request->currency_code]);
    
    return back();
}
}