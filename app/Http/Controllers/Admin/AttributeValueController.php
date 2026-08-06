<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttributeValue;
use App\Services\AttributeService;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
    public function __construct(
        private readonly AttributeService $attributes,
    ) {}

    public function index()
    {
        $values = $this->attributes->getValuesWithAttributes();
        return view('admin.attribute-values.index', compact('values'));
    }

    public function create()
    {
        $attributes = $this->attributes->getAttributesOrdered();
        return view('admin.attribute-values.create', compact('attributes'));
    }

    public function edit(AttributeValue $attributeValue)
    {
        $attributes = $this->attributes->getAttributesOrdered();
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

        $this->attributes->createValue($data);

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

        $this->attributes->updateValue($attributeValue, $data);

        return back()->with('success', 'تم التعديل');
    }

    public function destroy(AttributeValue $attributeValue)
    {
        $this->attributes->deleteValue($attributeValue);
        return back()->with('success', 'تم الحذف');
    }
}
