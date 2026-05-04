{{-- create.blade.php --}}
@extends('layouts.admin')
@section('title', 'إضافة ميزة')
@section('admin-content')
<form method="POST" action="{{ route('admin.site-features.store') }}" class="bg-white p-6 rounded-2xl">
    @csrf
    @include('admin.site-features._form')
    <button class="mt-4 bg-brand text-white px-4 py-2 rounded-xl">حفظ</button>
</form>
@endsection