<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function __construct(
        private readonly CurrencyService $currencies,
    ) {}

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

        $this->currencies->switchToOrFail($request->code);

        return back();
    }
}
