@extends('layouts.admin')
@section('title', 'المستخدمين')

@section('admin-content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">إدارة المستخدمين</h1>
</div>

<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-right">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase">المستخدم</th>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase">البريد الإلكتروني</th>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase">تاريخ الانضمام</th>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase">الحالة</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($users as $user)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                <td class="px-6 py-4 text-gray-500 text-sm">{{ $user->created_at->format('Y-m-d') }}</td>
                <td class="px-6 py-4">
                    @if($user->email_verified_at)
                        <span class="text-blue-600 bg-blue-50 px-3 py-1 rounded-full text-xs font-bold">مفعل</span>
                    @else
                        <span class="text-amber-600 bg-amber-50 px-3 py-1 rounded-full text-xs font-bold">بانتظار التأكيد</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection