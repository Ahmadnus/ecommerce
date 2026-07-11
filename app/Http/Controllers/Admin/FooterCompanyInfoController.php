<?php
// app/Http/Controllers/Admin/FooterCompanyInfoController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FooterCompanyInfoRequest;
use App\Models\FooterCompanyInfo;
use App\Services\FooterCompanyInfoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FooterCompanyInfoController extends Controller
{
    public function __construct(
        private readonly FooterCompanyInfoService $companyInfo,
    ) {}

    public function index(): View
    {
        $items = $this->companyInfo->getItems();
        return view('admin.footer-company.index', compact('items'));
    }

    public function create(): View
    {
        $item = new FooterCompanyInfo();
        return view('admin.footer-company.form', compact('item'));
    }

    public function store(FooterCompanyInfoRequest $request): RedirectResponse
    {
        try {
            $this->companyInfo->create(
                $request->except(['flag_icon', '_token']),
                $request->hasFile('flag_icon') ? $request->file('flag_icon') : null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Could not create company info. Please try again.');
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
        try {
            $this->companyInfo->update(
                $footerCompanyInfo,
                $request->except(['flag_icon', '_token', '_method']),
                $request->hasFile('flag_icon') ? $request->file('flag_icon') : null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Could not update company info. Please try again.');
        }

        return redirect()
            ->route('admin.footer-company.index')
            ->with('success', 'Company info updated.');
    }

    public function destroy(FooterCompanyInfo $footerCompanyInfo): RedirectResponse
    {
        $this->companyInfo->delete($footerCompanyInfo);

        return redirect()
            ->route('admin.footer-company.index')
            ->with('success', 'Entry deleted.');
    }
}
