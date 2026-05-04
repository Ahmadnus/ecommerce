<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Show a dynamic page by its slug.
     * Returns 404 if the page doesn't exist or is inactive.
     */
public function show(string $slug): View
{
    $page = Page::active()->where('slug', $slug)->firstOrFail();
    return view('pages.dynamic', compact('page'));
}
}
