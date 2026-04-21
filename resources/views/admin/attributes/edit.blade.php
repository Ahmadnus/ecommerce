@extends('layouts.admin')
@section('title', 'تعديل خاصية')

@section('admin-content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- تعديل الخاصية --}}
    <div class="bg-white p-6 rounded-2xl border">
        <form method="POST" action="{{ route('admin.attributes.update', $attribute) }}">
            @csrf @method('PUT')

            <input type="text" name="name"
                   value="{{ $attribute->name }}"
                   class="w-full border p-2 rounded-xl mb-3">

            <button class="bg-brand text-white px-4 py-2 rounded-xl">
                حفظ
            </button>
        </form>
    </div>

    {{-- القيم --}}
    <div class="bg-white p-6 rounded-2xl border">

        <h3 class="font-bold mb-4">القيم</h3>

        {{-- إضافة قيمة --}}
        <form method="POST" action="{{ route('admin.attribute-values.store') }}"
              class="flex gap-2 mb-4">
            @csrf
            <input type="hidden" name="attribute_id" value="{{ $attribute->id }}">

            <input type="text" name="value" placeholder="القيمة"
                   class="border p-2 rounded-xl flex-1">

            <input type="text" name="label" placeholder="اسم العرض"
                   class="border p-2 rounded-xl flex-1">

            <input type="color" name="color_hex" class="w-12 h-10">

            <button class="bg-green-500 text-white px-4 rounded-xl">
                +
            </button>
        </form>

        {{-- عرض القيم --}}
        <div class="space-y-2">
            @foreach($attribute->values as $val)
            <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl">

                <div class="flex items-center gap-2">
                    @if($val->color_hex)
                        <span class="w-4 h-4 rounded-full"
                              style="background:{{ $val->color_hex }}"></span>
                    @endif

                    <span>{{ $val->display_label }}</span>
                </div>

                <form method="POST"
                      action="{{ route('admin.attribute-values.destroy', $val) }}">
                    @csrf @method('DELETE')
                    <button class="text-red-500 text-sm">حذف</button>
                </form>
            </div>
            @endforeach
        </div>

    </div>

</div>
@endsection