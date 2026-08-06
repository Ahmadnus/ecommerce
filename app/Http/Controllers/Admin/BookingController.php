<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\RentalLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Bookings & Reservations Manager — إدارة الحجوزات
 */
class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['vehicle', 'pickupLocation', 'returnLocation', 'user'])
            ->when($request->filled('search'), fn($q) => $q->search($request->string('search')))
            ->when($request->filled('status'), fn($q) => $q->where('booking_status', $request->string('status')))
            ->when($request->filled('location'), fn($q) => $q->where('pickup_location_id', $request->integer('location')))
            ->when($request->filled('from'), fn($q) => $q->whereDate('pickup_date_time', '>=', $request->date('from')))
            ->when($request->filled('to'), fn($q) => $q->whereDate('pickup_date_time', '<=', $request->date('to')))
            ->latest('pickup_date_time')
            ->paginate(20)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings'  => $bookings,
            'locations' => RentalLocation::ordered()->get(),
            'statuses'  => Booking::statuses(),
            'stats'     => [
                'pending'   => Booking::status(Booking::STATUS_PENDING)->count(),
                'confirmed' => Booking::status(Booking::STATUS_CONFIRMED)->count(),
                'active'    => Booking::status(Booking::STATUS_ACTIVE)->count(),
                'completed' => Booking::status(Booking::STATUS_COMPLETED)->count(),
                'revenue'   => (float) Booking::whereIn('booking_status', [
                                    Booking::STATUS_CONFIRMED,
                                    Booking::STATUS_ACTIVE,
                                    Booking::STATUS_COMPLETED,
                                ])->sum('total_amount'),
            ],
        ]);
    }

    public function show(Booking $booking)
    {
        $booking->load(['vehicle', 'pickupLocation', 'returnLocation', 'user']);

        return view('admin.bookings.show', [
            'booking'  => $booking,
            'statuses' => Booking::statuses(),
        ]);
    }

    /**
     * Status changes go through the model's transition helpers so the
     * matching timestamp (confirmed_at / picked_up_at / …) is always set.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'booking_status' => ['required', Rule::in(array_keys(Booking::statuses()))],
        ]);

        match ($request->string('booking_status')->toString()) {
            Booking::STATUS_CONFIRMED => $booking->markConfirmed(),
            Booking::STATUS_ACTIVE    => $booking->markPickedUp(),
            Booking::STATUS_COMPLETED => $booking->markCompleted(),
            Booking::STATUS_CANCELLED => $booking->markCancelled(),
            default                   => $booking->update(['booking_status' => Booking::STATUS_PENDING]),
        };

        return back()->with('success', 'تم تحديث حالة الحجز');
    }

    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'admin_notes'    => ['nullable', 'string', 'max:2000'],
            'payment_status' => ['required', 'string', 'max:30'],
        ]);

        $booking->update($data);

        return back()->with('success', 'تم حفظ التعديلات');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'تم حذف الحجز');
    }
}
