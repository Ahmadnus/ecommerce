<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;

/**
 * Entry point for the merchant's COD shipping matrix.
 *
 * The Jordanian market is the store's primary target, so this jumps the
 * owner straight into the Jordan governorate price list (the countries →
 * zones screen), creating the Jordan record if missing.
 */
class ShippingMatrixController extends Controller
{
    public function index(): RedirectResponse
    {
        $jordan = Country::where('code', Country::JORDAN_CODE)->first()
            ?? Country::create([
                'name'         => 'الأردن',
                'name_en'      => 'Jordan',
                'code'         => Country::JORDAN_CODE,
                'calling_code' => '+962',
                'is_active'    => true,
                'is_system'    => true,
            ]);

        return redirect()->route('admin.countries.zones.index', $jordan);
    }
}
