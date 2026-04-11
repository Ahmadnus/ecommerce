@extends('layouts.admin') {{-- تأكد من اسم ملف الليوت عندك --}}
@section('title', 'إدارة الطلبات')

@section('admin-content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">قائمة الطلبات الأخيرة</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-6 py-4">رقم الطلب</th>
                    <th class="px-6 py-4">العميل</th>
                    <th class="px-6 py-4">الإجمالي</th>
                    <th class="px-6 py-4">الحالة</th>
                    <th class="px-6 py-4">التاريخ</th>
                    <th class="px-6 py-4">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($orders as $order)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 font-bold text-brand">#{{ $order->order_number }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $order->shipping_name }}</div>
                        <div class="text-xs text-gray-400">{{ $order->shipping_phone }}</div>
                    </td>
                    <td class="px-6 py-4 font-bold">{{ number_format($order->total_amount, 2) }} ر.س</td>
                    <td class="px-6 py-4">
                        <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="inline-block">
                            @csrf @method('PATCH')
                            <select onchange="this.form.submit()" name="status" 
                                class="text-xs rounded-full px-3 py-1 font-bold border-0 shadow-sm
                                {{ $order->status == 'pending' ? 'bg-amber-100 text-amber-600' : '' }}
                                {{ $order->status == 'processing' ? 'bg-blue-100 text-blue-600' : '' }}
                                {{ $order->status == 'shipped' ? 'bg-purple-100 text-purple-600' : '' }}
                                {{ $order->status == 'completed' ? 'bg-green-100 text-green-600' : '' }}
                                {{ $order->status == 'cancelled' ? 'bg-red-100 text-red-600' : '' }}">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>جاري التجهيز</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-gray-400 hover:text-brand transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-6">
        {{ $orders->links() }}
    </div>
</div>
@endsection