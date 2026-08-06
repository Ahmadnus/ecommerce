@extends('layouts.admin')
@section('title', 'تفاصيل الحجز')

@section('content')
<div class="p-6 space-y-6">

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('admin.bookings.index') }}" class="text-sm text-gray-500 hover:text-brand">
                <i class="fa-solid fa-arrow-right"></i> العودة للحجوزات
            </a>
            <h1 class="text-2xl font-bold mt-2 font-mono" dir="ltr">{{ $booking->booking_reference }}</h1>
            <p class="text-sm text-gray-500 mt-1">أُنشئ في {{ $booking->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <span class="inline-block text-sm font-semibold px-4 py-2 rounded-full
                     bg-{{ $booking->status_color }}-50 text-{{ $booking->status_color }}-700">
            {{ $booking->status_label }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ══ LEFT ════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Vehicle --}}
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="font-bold text-lg mb-4"><i class="fa-solid fa-car text-brand"></i> السيارة</h2>
                @if($booking->vehicle)
                    <div class="flex gap-4">
                        <div class="w-32 h-24 bg-gray-50 rounded-lg shrink-0 flex items-center justify-center overflow-hidden">
                            @if($booking->vehicle->main_image_url)
                                <img src="{{ $booking->vehicle->main_image_url }}" alt="" class="w-full h-full object-contain p-1">
                            @else
                                <i class="fa-solid fa-car text-3xl text-gray-300"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-bold text-lg">{{ $booking->vehicle->full_title }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $booking->vehicle->transmission_label }} · {{ $booking->vehicle->fuel_label }} ·
                                {{ $booking->vehicle->seats }} مقاعد
                            </p>
                            @if($booking->vehicle->registration_number)
                                <p class="text-sm text-gray-500 mt-1" dir="ltr">{{ $booking->vehicle->registration_number }}</p>
                            @endif
                            <a href="{{ route('admin.vehicles.edit', $booking->vehicle) }}"
                               class="inline-block mt-2 text-sm text-brand font-semibold hover:underline">عرض في الأسطول</a>
                        </div>
                    </div>
                @else
                    <p class="text-gray-400">— السيارة محذوفة —</p>
                @endif
            </section>

            {{-- Itinerary --}}
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="font-bold text-lg mb-4"><i class="fa-solid fa-route text-brand"></i> تفاصيل الإيجار</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">الاستلام</p>
                        <p class="font-bold">{{ $booking->pickupLocation?->display_label ?? '—' }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $booking->pickup_date_time->format('d/m/Y — H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">التسليم</p>
                        <p class="font-bold">{{ $booking->returnLocation?->display_label ?? '—' }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $booking->return_date_time->format('d/m/Y — H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">المدة</p>
                        <p class="font-bold">{{ $booking->total_days }} يوم ({{ $booking->rate_plan }})</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">تسليم في موقع مختلف</p>
                        <p class="font-bold">{{ $booking->is_one_way ? 'نعم' : 'لا' }}</p>
                    </div>
                </div>
            </section>

            {{-- Driver --}}
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="font-bold text-lg mb-4"><i class="fa-solid fa-id-card text-brand"></i> بيانات السائق</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    @foreach(array_filter([
                        ['الاسم',             $booking->driver_name],
                        ['الجوال',            $booking->driver_phone],
                        ['البريد',            $booking->driver_email],
                        ['تاريخ الميلاد',      $booking->driver_date_of_birth?->format('d/m/Y')],
                        ['رقم الرخصة',        $booking->driver_license_number],
                        ['دولة الإصدار',      $booking->driver_license_country],
                        ['انتهاء الرخصة',     $booking->driver_license_expiry?->format('d/m/Y')],
                        ['الهوية / الإقامة',  $booking->driver_national_id],
                    ], fn($row) => filled($row[1])) as [$label, $value])
                        <div class="flex justify-between gap-4 border-b border-gray-50 pb-2">
                            <span class="text-gray-500">{{ $label }}</span>
                            <span class="font-semibold text-end">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>

                @if($booking->notes)
                    <div class="mt-5 bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-400 mb-1">طلبات العميل</p>
                        <p class="text-sm">{{ $booking->notes }}</p>
                    </div>
                @endif
            </section>

            {{-- Money --}}
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="font-bold text-lg mb-4"><i class="fa-solid fa-receipt text-brand"></i> التكلفة</h2>
                <dl class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">الإيجار ({{ number_format((float) $booking->daily_rate, 2) }} × {{ $booking->total_days }})</dt>
                        <dd class="font-semibold">{{ number_format((float) $booking->subtotal, 2) }}</dd>
                    </div>
                    @if((float) $booking->location_fee > 0)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">رسوم الموقع</dt>
                            <dd class="font-semibold">{{ number_format((float) $booking->location_fee, 2) }}</dd>
                        </div>
                    @endif
                    @foreach(($booking->extras ?? []) as $extra)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">{{ $extra['label_ar'] ?? $extra['label'] }}</dt>
                            <dd class="font-semibold">{{ number_format((float) $extra['total'], 2) }}</dd>
                        </div>
                    @endforeach
                    <div class="flex justify-between">
                        <dt class="text-gray-500">ضريبة القيمة المضافة</dt>
                        <dd class="font-semibold">{{ number_format((float) $booking->tax_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between pt-3 border-t border-gray-200">
                        <dt class="font-bold">الإجمالي</dt>
                        <dd class="font-bold text-xl text-brand">
                            {{ number_format((float) $booking->total_amount, 2) }} {{ $booking->currency }}
                        </dd>
                    </div>
                    @if((float) $booking->security_deposit > 0)
                        <div class="flex justify-between text-xs text-gray-500">
                            <dt>مبلغ التأمين (مسترد)</dt>
                            <dd>{{ number_format((float) $booking->security_deposit, 2) }}</dd>
                        </div>
                    @endif
                </dl>
            </section>
        </div>

        {{-- ══ SIDEBAR ═════════════════════════════════════════════════════ --}}
        <div class="space-y-6">

            {{-- Status transitions --}}
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="font-bold text-lg mb-4">حالة الحجز</h2>
                <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                    @csrf @method('PATCH')
                    <select name="booking_status" class="w-full rounded-lg border-gray-300 text-sm mb-3">
                        @foreach($statuses as $key => $status)
                            <option value="{{ $key }}" @selected($booking->booking_status === $key)>{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full bg-brand text-white font-bold py-2.5 rounded-lg hover:opacity-90 transition">
                        تحديث الحالة
                    </button>
                </form>

                <div class="mt-5 pt-5 border-t border-gray-100 space-y-2 text-xs text-gray-500">
                    @foreach(array_filter([
                        ['تم التأكيد',  $booking->confirmed_at],
                        ['تم الاستلام', $booking->picked_up_at],
                        ['تم التسليم',  $booking->returned_at],
                        ['تم الإلغاء',  $booking->cancelled_at],
                    ], fn($row) => $row[1]) as [$label, $at])
                        <div class="flex justify-between">
                            <span>{{ $label }}</span>
                            <span class="font-semibold">{{ $at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Admin notes / payment --}}
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="font-bold text-lg mb-4">ملاحظات إدارية</h2>
                <form method="POST" action="{{ route('admin.bookings.update', $booking) }}">
                    @csrf @method('PUT')

                    <label for="payment_status" class="block text-sm font-semibold mb-2">حالة الدفع</label>
                    <select id="payment_status" name="payment_status" class="w-full rounded-lg border-gray-300 text-sm mb-4">
                        @foreach(['pending' => 'بانتظار الدفع', 'paid' => 'مدفوع', 'refunded' => 'مسترد'] as $key => $label)
                            <option value="{{ $key }}" @selected($booking->payment_status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <label for="admin_notes" class="block text-sm font-semibold mb-2">ملاحظات</label>
                    <textarea id="admin_notes" name="admin_notes" rows="4"
                              class="w-full rounded-lg border-gray-300 text-sm">{{ old('admin_notes', $booking->admin_notes) }}</textarea>

                    <button type="submit" class="mt-3 w-full bg-gray-800 text-white font-bold py-2.5 rounded-lg hover:opacity-90 transition">
                        حفظ
                    </button>
                </form>
            </section>

            <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}"
                  onsubmit="return confirm('حذف هذا الحجز نهائياً؟')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full border border-red-300 text-red-600 font-bold py-2.5 rounded-lg hover:bg-red-50 transition">
                    <i class="fa-solid fa-trash"></i> حذف الحجز
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
