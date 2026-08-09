@extends('layouts.admin')
@section('title', 'إضافة خدمة')

@section('admin-content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.extras.index') }}" class="text-sm text-gray-500 hover:text-brand">
            <i class="fa-solid fa-arrow-right"></i> العودة للإضافات
        </a>
        <h1 class="text-2xl font-bold mt-2">إضافة خدمة جديدة</h1>
    </div>

    <form method="POST" action="{{ route('admin.extras.store') }}">
        @csrf
        @include('admin.extras._form', ['extra' => null])
    </form>
</div>
@endsection
