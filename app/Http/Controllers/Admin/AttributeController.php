<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Services\AttributeService;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function __construct(
        private readonly AttributeService $attributes,
    ) {}

    public function index()
    {
        $attributes = $this->attributes->getAttributesWithValueCounts();

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

        $this->attributes->createAttribute($data);

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

        $this->attributes->updateAttribute($attribute, $data);

        return back()->with('success', 'تم التحديث');
    }

    public function destroy(Attribute $attribute)
    {
        $this->attributes->deleteAttribute($attribute);
        return back()->with('success', 'تم الحذف');
    }
}
