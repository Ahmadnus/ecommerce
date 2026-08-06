@extends('layouts.admin')
@section('title', 'إضافة فئة')

@section('admin-content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.vehicle-categories.index') }}" class="text-sm text-gray-500 hover:text-brand">
            <i class="fa-solid fa-arrow-right"></i> العودة للفئات
        </a>
        <h1 class="text-2xl font-bold mt-2">إضافة فئة جديدة</h1>
    </div>

    <form method="POST" action="{{ route('admin.vehicle-categories.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.vehicle-categories._form', ['category' => null])
    </form>
</div>
@endsection
