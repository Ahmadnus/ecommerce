<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocaleModeController extends Controller
{
    public function index()
    {
        $mode = DB::table('settings')->where('key', 'langsetting')->value('value') ?? 'both';
        return view('admin.locale-mode', compact('mode'));
    }

    public function update(Request $request)
    {
        $mode = $request->input('mode');
        if (in_array($mode, ['ar', 'en', 'both'])) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'langsetting'],
                ['value' => $mode]
            );
            cache()->forget('langsetting');
        }
        return back()->with('success', 'تم حفظ إعداد اللغة بنجاح ✓');
    }
}