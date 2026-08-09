@extends('layouts.admin')
@section('title', 'تعديل خدمة')

@section('admin-content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.extras.index') }}" class="text-sm text-gray-500 hover:text-brand">
            <i class="fa-solid fa-arrow-right"></i> العودة للإضافات
        </a>
        <h1 class="text-2xl font-bold mt-2">تعديل: {{ $extra->getTranslation('name', 'ar', false) }}</h1>
    </div>

    <form method="POST" action="{{ route('admin.extras.update', $extra) }}">
        @csrf
        @method('PUT')
        @include('admin.extras._form', ['extra' => $extra])
    </form>
</div>
@endsection
