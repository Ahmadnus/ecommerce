@extends('layouts.admin')
@section('title', 'تعديل فئة')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.vehicle-categories.index') }}" class="text-sm text-gray-500 hover:text-brand">
            <i class="fa-solid fa-arrow-right"></i> العودة للفئات
        </a>
        <h1 class="text-2xl font-bold mt-2">تعديل: {{ $category->name }}</h1>
    </div>

    <form method="POST" action="{{ route('admin.vehicle-categories.update', $category) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.vehicle-categories._form', ['category' => $category])
    </form>
</div>
@endsection
