@extends('layouts.admin')
@section('title', 'إضافة بلوك جديد')

{{-- Delegate everything to the shared create/edit view --}}
@push('head')
@include('admin.home-sections.create-edit')
@endpush