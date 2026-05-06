<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Store the user's chosen currency in the session and redirect back.
     *
     * POST /currency/switch
     * body: { code: "USD" }
     */
    public function switch(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|exists:currencies,code',
        ]);

        $currency = Currency::where('code', $request->code)
                            ->where('is_active', true)
                            ->firstOrFail();

        session(['currency_code' => $currency->code]);

        return back();
    }
}