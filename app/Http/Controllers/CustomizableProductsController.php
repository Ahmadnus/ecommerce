<?php

namespace App\Http\Controllers;

use App\Services\CustomizationService;
use Illuminate\View\View;

class CustomizableProductsController extends Controller
{
    public function __construct(
        private readonly CustomizationService $customization,
    ) {}

    public function index(): View
    {
        return view('customize.index', $this->customization->getLandingData());
    }
}
