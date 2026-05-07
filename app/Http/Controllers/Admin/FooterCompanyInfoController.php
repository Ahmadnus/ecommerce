<?php
// app/Http/Controllers/Admin/FooterCompanyInfoController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FooterCompanyInfoRequest;
use App\Models\FooterCompanyInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FooterCompanyInfoController extends Controller
{
    public function index(): View
    {
        $items = FooterCompanyInfo::orderBy('sort_order')->get();
        return view('admin.footer-company.index', compact('items'));
    }

    public function create(): View
    {
        $item = new FooterCompanyInfo();
        return view('admin.footer-company.form', compact('item'));
    }

    public function store(FooterCompanyInfoRequest $request): RedirectResponse
    {
        $item = FooterCompanyInfo::create(
            $request->except(['flag_icon', '_token'])
        );

        if ($request->hasFile('flag_icon')) {
            $item->addMediaFromRequest('flag_icon')
                ->toMediaCollection('flag_icon');
        }

        return redirect()
            ->route('admin.footer-company.index')
            ->with('success', 'Company info created.');
    }

    public function edit(FooterCompanyInfo $footerCompanyInfo): View
    {
        return view('admin.footer-company.form', ['item' => $footerCompanyInfo]);
    }

    public function update(FooterCompanyInfoRequest $request, FooterCompanyInfo $footerCompanyInfo): RedirectResponse
    {
        $footerCompanyInfo->update(
            $request->except(['flag_icon', '_token', '_method'])
        );

        if ($request->hasFile('flag_icon')) {
            $footerCompanyInfo->addMediaFromRequest('flag_icon')
                ->toMediaCollection('flag_icon');
        }

        return redirect()
            ->route('admin.footer-company.index')
            ->with('success', 'Company info updated.');
    }

    public function destroy(FooterCompanyInfo $footerCompanyInfo): RedirectResponse
    {
        $footerCompanyInfo->delete();

        return redirect()
            ->route('admin.footer-company.index')
            ->with('success', 'Entry deleted.');
    }
}