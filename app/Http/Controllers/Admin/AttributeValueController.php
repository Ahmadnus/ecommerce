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
        $attributes = Attribute::orderBy('sort_order')->get();
        return view('admin.attribute-values.create', compact('attributes'));
    }

    public function edit(AttributeValue $attributeValue)
    {
        $attributes = Attribute::orderBy('sort_order')->get();
        return view('admin.attribute-values.edit', compact('attributeValue', 'attributes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value.ar'     => 'required|string',
            'value.en'     => 'required|string',
            'label.ar'     => 'nullable|string',
            'label.en'     => 'nullable|string',
            'color_hex'    => 'nullable|string|max:7',
        ], [
            'value.ar.required' => 'القيمة بالعربية مطلوبة.',
            'value.en.required' => 'The English value is required.',
        ]);

        AttributeValue::create([
            'attribute_id' => $data['attribute_id'],
            'value'        => ['ar' => $data['value']['ar'],  'en' => $data['value']['en']],
            'label'        => ['ar' => $data['label']['ar'] ?? '', 'en' => $data['label']['en'] ?? ''],
            'color_hex'    => $data['color_hex'] ?? null,
        ]);

        return back()->with('success', 'تمت الإضافة');
    }

    public function update(Request $request, AttributeValue $attributeValue)
    {
        $data = $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value.ar'     => 'required|string',
            'value.en'     => 'required|string',
            'label.ar'     => 'nullable|string',
            'label.en'     => 'nullable|string',
            'color_hex'    => 'nullable|string|max:7',
        ], [
            'value.ar.required' => 'القيمة بالعربية مطلوبة.',
            'value.en.required' => 'The English value is required.',
        ]);

        $attributeValue->update([
            'attribute_id' => $data['attribute_id'],
            'value'        => ['ar' => $data['value']['ar'],  'en' => $data['value']['en']],
            'label'        => ['ar' => $data['label']['ar'] ?? '', 'en' => $data['label']['en'] ?? ''],
            'color_hex'    => $data['color_hex'] ?? null,
        ]);

        return back()->with('success', 'تم التعديل');
    }

    public function destroy(AttributeValue $attributeValue)
    {
        $attributeValue->delete();
        return back()->with('success', 'تم الحذف');
    }
}