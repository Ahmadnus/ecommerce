{{-- edit.blade.php --}}
@extends('layouts.admin')
@section('title', 'تعديل ميزة')
@section('admin-content')
<form method="POST" action="{{ route('admin.site-features.update', $site_feature) }}" class="bg-white p-6 rounded-2xl">
    @csrf @method('PUT')
    @include('admin.site-features._form')
    <button class="mt-4 bg-brand text-white px-4 py-2 rounded-xl">تحديث</button>
</form>
@endsection