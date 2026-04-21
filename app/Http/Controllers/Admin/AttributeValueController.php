<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
    public function index()
    {
        $values = AttributeValue::with('attribute')->latest()->get();
        return view('admin.attribute-values.index', compact('values'));
    }

    public function create()
    {
        $attributes = Attribute::all();
        return view('admin.attribute-values.create', compact('attributes'));
    }

  

    public function edit(AttributeValue $attributeValue)
    {
        $attributes = Attribute::all();
        return view('admin.attribute-values.edit', compact('attributeValue', 'attributes'));
    }

       public function store(Request $request)
    {
        $data = $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string',
            'label' => 'nullable|string',
            'color_hex' => 'nullable|string|max:7',
        ]);

        AttributeValue::create($data);

        // 👇 خليها بنفس الصفحة
        return back()->with('success', 'تمت الإضافة');
    }

    public function update(Request $request, AttributeValue $attributeValue)
    {
        $data = $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string',
            'label' => 'nullable|string',
            'color_hex' => 'nullable|string|max:7',
        ]);

        $attributeValue->update($data);

        return back()->with('success', 'تم التعديل');
    }

    public function destroy(AttributeValue $attributeValue)
    {
        $attributeValue->delete();
        return back()->with('success', 'تم الحذف');
    }
}