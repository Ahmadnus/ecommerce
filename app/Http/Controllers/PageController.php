<?php

namespace App\Http\Controllers;

use App\Services\PageService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        private readonly PageService $pages,
    ) {}

    /**
     * Show a dynamic page by its slug.
     * Returns 404 if the page doesn't exist or is inactive.
     */
    public function show(string $slug): View
    {
        $page = $this->pages->findActiveBySlug($slug);
        return view('pages.dynamic', compact('page'));
    }
}
