@extends('layouts.admin')
@section('title', 'تعديل فرع')

@section('admin-content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.locations.index') }}" class="text-sm text-gray-500 hover:text-brand">
            <i class="fa-solid fa-arrow-right"></i> العودة للفروع
        </a>
        <h1 class="text-2xl font-bold mt-2">تعديل: {{ $location->name }}</h1>
    </div>

    <form method="POST" action="{{ route('admin.locations.update', $location) }}">
        @csrf @method('PUT')
        @include('admin.locations._form', ['location' => $location])
    </form>
</div>
@endsection
