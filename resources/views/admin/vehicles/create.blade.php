@extends('layouts.admin')
@section('title', 'إضافة سيارة')

@section('admin-content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.vehicles.index') }}" class="text-sm text-gray-500 hover:text-brand">
            <i class="fa-solid fa-arrow-right"></i> العودة للأسطول
        </a>
        <h1 class="text-2xl font-bold mt-2">إضافة سيارة جديدة</h1>
    </div>

    <form method="POST" action="{{ route('admin.vehicles.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.vehicles._form', ['vehicle' => null])
    </form>
</div>
@endsection
