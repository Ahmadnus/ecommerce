<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LocaleModeService;
use Illuminate\Http\Request;

class LocaleModeController extends Controller
{
    public function __construct(
        private readonly LocaleModeService $localeMode,
    ) {}

    public function index()
    {
        $mode = $this->localeMode->getMode();
        return view('admin.locale-mode', compact('mode'));
    }

    public function update(Request $request)
    {
        $this->localeMode->setMode($request->input('mode'));

        return back()->with('success', 'تم حفظ إعداد اللغة بنجاح ✓');
    }
}
