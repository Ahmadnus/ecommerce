<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::withCount('values')
            ->orderBy('sort_order')
            ->get();

        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.attributes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name.ar'    => 'required|string|max:100',
            'name.en'    => 'required|string|max:100',
            'type'       => 'nullable|string|in:select,color,text',
            'sort_order' => 'nullable|integer',
        ], [
            'name.ar.required' => 'الاسم بالعربية مطلوب.',
            'name.en.required' => 'The English name is required.',
        ]);

        Attribute::create([
            'name'       => ['ar' => $data['name']['ar'], 'en' => $data['name']['en']],
            'type'       => $data['type'] ?? 'select',
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'تم إنشاء الخاصية');
    }

    public function edit(Attribute $attribute)
    {
        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $data = $request->validate([
            'name.ar'    => 'required|string|max:100',
            'name.en'    => 'required|string|max:100',
            'type'       => 'nullable|string|in:select,color,text',
            'sort_order' => 'nullable|integer',
        ], [
            'name.ar.required' => 'الاسم بالعربية مطلوب.',
            'name.en.required' => 'The English name is required.',
        ]);

        $attribute->update([
            'name'       => ['ar' => $data['name']['ar'], 'en' => $data['name']['en']],
            'type'       => $data['type'] ?? $attribute->type,
            'sort_order' => $data['sort_order'] ?? $attribute->sort_order,
        ]);

        return back()->with('success', 'تم التحديث');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return back()->with('success', 'تم الحذف');
    }
}