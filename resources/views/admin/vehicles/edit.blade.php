@extends('layouts.admin')
@section('title', 'تعديل سيارة')

@section('admin-content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.vehicles.index') }}" class="text-sm text-gray-500 hover:text-brand">
            <i class="fa-solid fa-arrow-right"></i> العودة للأسطول
        </a>
        <h1 class="text-2xl font-bold mt-2">تعديل: {{ $vehicle->full_title }}</h1>
    </div>

    <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.vehicles._form', ['vehicle' => $vehicle])
    </form>
</div>
@endsection
