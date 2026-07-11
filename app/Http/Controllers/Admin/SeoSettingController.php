<?php
// app/Http/Controllers/Admin/SeoSettingController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SeoSettingRequest;
use App\Services\SeoSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SeoSettingController extends Controller
{
    public function __construct(
        private readonly SeoSettingService $seo,
    ) {}

    public function index(): View
    {
        $settings = $this->seo->getAllByType();

        return view('admin.seo.index', compact('settings'));
    }

    public function edit(string $type): View
    {
        abort_unless($this->seo->isValidType($type), 404);

        $seo = $this->seo->getForType($type);

        return view('admin.seo.edit', compact('seo', 'type'));
    }

    public function update(SeoSettingRequest $request, string $type): RedirectResponse
    {
        abort_unless($this->seo->isValidType($type), 404);

        try {
            $this->seo->save(
                $type,
                $request->except(['og_image', 'favicon', '_token', '_method']),
                $request->hasFile('og_image') ? $request->file('og_image') : null,
                $request->hasFile('favicon') ? $request->file('favicon') : null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Could not save SEO settings. Please try again.');
        }

        return redirect()
            ->route('admin.seo.edit', $type)
            ->with('success', 'SEO settings saved successfully.');
    }
}
