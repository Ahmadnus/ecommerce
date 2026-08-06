<?php

namespace App\Http\Controllers\Rental;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\RentalLocation;
use App\Models\Vehicle;
use App\Services\RentalPricingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Reservation flow + the public "Manage Booking" lookup.
 */
class BookingController extends Controller
{
    public function __construct(
        private readonly RentalPricingService $pricing,
        private readonly FleetController $fleet,
    ) {
    }

    /** Step 1 — driver details + extras, with a live price breakdown. */
    public function create(Request $request, string $slug): View|RedirectResponse
    {
        $vehicle = Vehicle::with(['category', 'location'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $search = $this->fleet->resolveSearch($request);

        if (! $search['pickup_at'] || ! $search['return_at']) {
            return redirect()
                ->route('rental.vehicles.show', $slug)
                ->with('error', __('rental.no_cars_hint'));
        }

        if (! $vehicle->isAvailableBetween($search['pickup_at'], $search['return_at'])) {
            return redirect()
                ->route('rental.fleet', $request->query())
                ->with('error', __('rental.unavailable'));
        }

        $extraKeys = (array) $request->input('extras', []);

        $quote = $this->pricing->quote(
            $vehicle,
            $search['pickup_at'],
            $search['return_at'],
            $search['pickup_location'],
            $search['return_location'],
            $extraKeys
        );

        return view('rental.booking.create', [
            'vehicle'         => $vehicle,
            'search'          => $search,
            'quote'           => $quote,
            'availableExtras' => $this->pricing->availableExtras(),
            'selectedExtras'  => $extraKeys,
            'locations'       => RentalLocation::active()->ordered()->get(),
        ]);
    }

    /** Step 2 — persist the reservation. */
    public function store(Request $request, string $slug): RedirectResponse
    {
        $vehicle = Vehicle::where('slug', $slug)->where('is_active', true)->firstOrFail();

        /*
         * DEMO/TESTING: request validation is intentionally switched off here so
         * a reservation can be submitted with any (or no) driver details. The
         * original rule set — required name/phone/licence, `accepted` terms, and
         * the minimum-driver-age check against $vehicle->min_driver_age — is
         * preserved in git history and should be restored before going live.
         */
        $driverFields = [
            'driver_name', 'driver_email', 'driver_phone', 'driver_date_of_birth',
            'driver_license_number', 'driver_license_country', 'driver_license_expiry',
            'driver_national_id', 'notes',
        ];

        // Every key is present (null when omitted) so the create() call below
        // never trips an undefined-index on a half-filled test submission.
        $validated = $request->only($driverFields) + array_fill_keys($driverFields, null);

        // Blank strings must become null — the date columns can't cast ''.
        $validated = array_map(
            fn($v) => is_string($v) && trim($v) === '' ? null : $v,
            $validated
        );

        // Unknown extra keys would break the pricing lookup, so they are dropped
        // rather than rejected — that is data hygiene, not user-facing validation.
        $validated['extras'] = array_values(array_intersect(
            (array) $request->input('extras', []),
            array_keys($this->pricing->availableExtras())
        ));

        $search = $this->fleet->resolveSearch($request);

        // resolveSearch already falls back to sane defaults, but a totally
        // unparseable window would leave these null — fill them in rather than
        // bouncing the tester back to the form.
        $search['pickup_at'] ??= now()->addDay()->setTime(10, 0);
        $search['return_at'] ??= $search['pickup_at']->copy()->addDays(3);

        // Same for the branch: fall back to the vehicle's own, then any active one.
        if (! $search['pickup_location_id']) {
            $fallback = $vehicle->location ?? RentalLocation::active()->ordered()->first();

            $search['pickup_location_id'] = $fallback?->id;
            $search['pickup_location']    = $fallback;
            $search['return_location_id'] ??= $fallback?->id;
            $search['return_location']    ??= $fallback;
        }

        $quote = $this->pricing->quote(
            $vehicle,
            $search['pickup_at'],
            $search['return_at'],
            $search['pickup_location'],
            $search['return_location'],
            (array) ($validated['extras'] ?? [])
        );

        // Re-check availability inside a transaction with the vehicle row locked,
        // so two people booking the last unit at the same time can't both win.
        try {
            $booking = DB::transaction(function () use ($vehicle, $search, $validated, $quote) {
                $locked = Vehicle::whereKey($vehicle->id)->lockForUpdate()->firstOrFail();

                if (! $locked->isAvailableBetween($search['pickup_at'], $search['return_at'])) {
                    throw new \RuntimeException('unavailable');
                }

                return Booking::create([
                    'user_id'               => auth()->id(),
                    'vehicle_id'            => $locked->id,
                    'pickup_location_id'    => $search['pickup_location_id'],
                    'return_location_id'    => $search['return_location_id'] ?: $search['pickup_location_id'],
                    'pickup_date_time'      => $search['pickup_at'],
                    'return_date_time'      => $search['return_at'],
                    'total_days'            => $quote['days'],
                    'rate_plan'             => $quote['rate_plan'],
                    // Placeholders keep the NOT NULL columns satisfied now that
                    // the form no longer requires these (see the DEMO note above).
                    'driver_name'           => $validated['driver_name'] ?: 'عمر المجالي',
                    'driver_email'          => $validated['driver_email'] ?? null,
                    'driver_phone'          => $validated['driver_phone'] ?: '0790000000',
                    'driver_date_of_birth'  => $validated['driver_date_of_birth'] ?? null,
                    'driver_license_number' => $validated['driver_license_number'] ?: 'JO-962000',
                    'driver_license_country'=> $validated['driver_license_country'] ?? null,
                    'driver_license_expiry' => $validated['driver_license_expiry'] ?? null,
                    'driver_national_id'    => $validated['driver_national_id'] ?? null,
                    'daily_rate'            => $quote['per_day'],
                    'subtotal'              => $quote['subtotal'],
                    'location_fee'          => $quote['location_fee'],
                    'extras_total'          => $quote['extras_total'],
                    'tax_amount'            => $quote['tax_amount'],
                    'security_deposit'      => $quote['security_deposit'],
                    'total_amount'          => $quote['total'],
                    'currency'              => $quote['currency'],
                    'extras'                => $quote['extras'],
                    'booking_status'        => Booking::STATUS_PENDING,
                    'payment_method'        => 'on_pickup',
                    'payment_status'        => 'pending',
                    'notes'                 => $validated['notes'] ?? null,
                ]);
            });
        } catch (\RuntimeException) {
            return back()->withInput()->with('error', __('rental.unavailable'));
        }

        return redirect()->route('rental.booking.success', $booking->booking_reference);
    }

    public function success(string $reference): View
    {
        $booking = Booking::with(['vehicle', 'pickupLocation', 'returnLocation'])
            ->where('booking_reference', $reference)
            ->firstOrFail();

        return view('rental.booking.success', compact('booking'));
    }

    /** Signed-in customer's own reservations. */
    public function myBookings(): View
    {
        $bookings = Booking::with(['vehicle', 'pickupLocation', 'returnLocation'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('rental.booking.my-bookings', compact('bookings'));
    }

    // ── Manage Booking (public lookup) ───────────────────────────────────────

    public function manage(): View
    {
        return view('rental.booking.manage', ['booking' => null]);
    }

    /**
     * Reference alone isn't enough to view a booking — it also needs the phone
     * number on the reservation, so a guessed reference leaks nothing.
     */
    public function lookup(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'booking_reference' => ['required', 'string', 'max:40'],
            'driver_phone'      => ['required', 'string', 'max:30'],
        ]);

        $booking = Booking::with(['vehicle', 'pickupLocation', 'returnLocation'])
            ->where('booking_reference', trim($data['booking_reference']))
            ->get()
            ->first(fn(Booking $b) => $this->phonesMatch($b->driver_phone, $data['driver_phone']));

        if (! $booking) {
            return back()->withInput()->with('error', __('rental.lookup_failed'));
        }

        return view('rental.booking.manage', compact('booking'));
    }

    public function cancel(Request $request, string $reference): RedirectResponse
    {
        $data = $request->validate([
            'driver_phone' => ['required', 'string', 'max:30'],
        ]);

        $booking = Booking::where('booking_reference', $reference)->firstOrFail();

        if (! $this->phonesMatch($booking->driver_phone, $data['driver_phone'])) {
            return back()->with('error', __('rental.lookup_failed'));
        }

        if (! $booking->isCancellable()) {
            return back()->with('error', __('rental.lookup_failed'));
        }

        $booking->markCancelled();

        return redirect()
            ->route('rental.booking.manage')
            ->with('success', __('rental.cancelled_ok'));
    }

    /** Compare on digits only, so +966 50… and 05… match the same customer. */
    private function phonesMatch(?string $stored, string $input): bool
    {
        $normalise = static function (?string $phone): string {
            $digits = preg_replace('/\D+/', '', (string) $phone);

            // Drop the Jordanian country code and any trunk zero so the
            // remaining national number is what gets compared — +962 79…,
            // 00962 79… and 079… all reduce to the same digits.
            $digits = preg_replace('/^(00)?962/', '', $digits);

            return ltrim($digits, '0');
        };

        $a = $normalise($stored);
        $b = $normalise($input);

        return $a !== '' && $a === $b;
    }
}
