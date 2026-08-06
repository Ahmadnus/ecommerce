<?php

namespace App\Http\Controllers\Rental;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use App\Models\RentalLocation;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Services\RentalPricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public storefront: homepage, fleet listing, vehicle detail.
 */
class FleetController extends Controller
{
    public function __construct(private readonly RentalPricingService $pricing)
    {
    }

    public function home(): View
    {
        return view('rental.home', [
            'banners'    => HeroBanner::where('is_active', true)
                                ->orderBy('sort_order')
                                ->orderBy('id')
                                ->get(),
            'categories' => VehicleCategory::active()->ordered()->get(),
            'featured'   => Vehicle::with(['category', 'location'])
                                ->bookable()
                                ->featured()
                                ->orderBy('sort_order')
                                ->take(8)
                                ->get(),
            'locations'  => $this->locations(),
            'search'     => $this->searchDefaults(),
        ]);
    }

    public function index(Request $request): View
    {
        $search = $this->resolveSearch($request);

        $query = Vehicle::with(['category', 'location'])->bookable();

        // Only constrain by availability once we have a real date window.
        if ($search['pickup_at'] && $search['return_at']) {
            $query->availableBetween($search['pickup_at'], $search['return_at']);
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->string('category')));
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->string('brand'));
        }

        if ($request->filled('transmission')) {
            $query->where('transmission', $request->string('transmission'));
        }

        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->string('fuel_type'));
        }

        if ($request->filled('seats')) {
            $query->where('seats', '>=', $request->integer('seats'));
        }

        // Price filters compare against the rate actually charged.
        $rateExpr = 'COALESCE(discount_daily_rate, daily_rate)';

        if ($request->filled('min_price')) {
            $query->whereRaw("{$rateExpr} >= ?", [$request->float('min_price')]);
        }

        if ($request->filled('max_price')) {
            $query->whereRaw("{$rateExpr} <= ?", [$request->float('max_price')]);
        }

        match ($request->string('sort')->toString()) {
            'price_asc'  => $query->orderByRaw("{$rateExpr} asc"),
            'price_desc' => $query->orderByRaw("{$rateExpr} desc"),
            'newest'     => $query->orderByDesc('year'),
            default      => $query->orderByDesc('is_featured')->orderBy('sort_order'),
        };

        $vehicles = $query->paginate(12)->withQueryString();

        return view('rental.fleet', [
            'vehicles'   => $vehicles,
            'categories' => VehicleCategory::active()->ordered()->get(),
            'brands'     => Vehicle::bookable()->distinct()->orderBy('brand')->pluck('brand'),
            'locations'  => $this->locations(),
            'search'     => $search,
            'days'       => $search['pickup_at'] && $search['return_at']
                                ? $this->pricing->billableDays($search['pickup_at'], $search['return_at'])
                                : null,
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        $vehicle = Vehicle::with(['category', 'location'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $search = $this->resolveSearch($request);

        $quote = null;

        if ($search['pickup_at'] && $search['return_at']) {
            $quote = $this->pricing->quote(
                $vehicle,
                $search['pickup_at'],
                $search['return_at'],
                $search['pickup_location'],
                $search['return_location']
            );
        }

        $similar = Vehicle::with('category')
            ->bookable()
            ->where('id', '!=', $vehicle->id)
            ->when(
                $vehicle->vehicle_category_id,
                fn($q) => $q->where('vehicle_category_id', $vehicle->vehicle_category_id)
            )
            ->take(4)
            ->get();

        return view('rental.vehicle', compact('vehicle', 'search', 'quote', 'similar'));
    }

    public function branches(): View
    {
        return view('rental.branches', [
            'locations' => RentalLocation::active()->ordered()->get(),
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function locations()
    {
        return RentalLocation::active()->ordered()->get();
    }

    /** The branch every search falls back to when the URL names none. */
    private function primaryLocationId(): ?int
    {
        return RentalLocation::active()->ordered()->value('id');
    }

    /**
     * Sensible defaults for the search widget on a cold visit: pick up
     * tomorrow at 10:00, return three days later.
     */
    private function searchDefaults(): array
    {
        // Preselect the primary branch so the homepage widget never opens on a
        // blank location.
        $primary = $this->primaryLocationId();

        return [
            'pickup_location_id' => $primary,
            'return_location_id' => $primary,
            'pickup_location'    => null,
            'return_location'    => null,
            'pickup_at'          => null,
            'return_at'          => null,
            'pickup_date'        => now()->addDay()->format('Y-m-d'),
            'pickup_time'        => '10:00',
            'return_date'        => now()->addDays(4)->format('Y-m-d'),
            'return_time'        => '10:00',
            'different_location' => false,
            'plan'               => 'daily',
        ];
    }

    /**
     * Reads the search widget's inputs into a normalised array. Invalid or
     * missing dates fall back to defaults rather than erroring, so a
     * hand-edited URL can never 500 the fleet page.
     */
    public function resolveSearch(Request $request): array
    {
        $search = $this->searchDefaults();

        $search['pickup_date'] = $request->input('pickup_date', $search['pickup_date']);
        $search['pickup_time'] = $request->input('pickup_time', $search['pickup_time']);
        $search['return_date'] = $request->input('return_date', $search['return_date']);
        $search['return_time'] = $request->input('return_time', $search['return_time']);
        $search['plan']        = $request->input('plan', 'daily');

        $pickupAt = $this->parseDateTime($search['pickup_date'], $search['pickup_time']);
        $returnAt = $this->parseDateTime($search['return_date'], $search['return_time']);

        // A return before pickup is nonsense — push it to pickup + 1 day.
        if ($pickupAt && $returnAt && $returnAt->lessThanOrEqualTo($pickupAt)) {
            $returnAt = $pickupAt->copy()->addDay();
            $search['return_date'] = $returnAt->format('Y-m-d');
            $search['return_time'] = $returnAt->format('H:i');
        }

        $search['pickup_at'] = $pickupAt;
        $search['return_at'] = $returnAt;

        $search['pickup_location_id'] = $request->integer('pickup_location_id') ?: null;
        $search['return_location_id'] = $request->integer('return_location_id') ?: null;
        $search['different_location'] = $request->boolean('different_location');

        /*
         * A URL that carries dates but no branch used to leave both locations
         * null, which rendered as "—" on the booking page. Fall back to the
         * primary branch (first by sort order — Queen Alia International
         * Airport) so a location is always resolved.
         */
        $search['pickup_location_id'] ??= $this->primaryLocationId();

        if (! $search['different_location']) {
            $search['return_location_id'] = $search['pickup_location_id'];
        }

        $search['return_location_id'] ??= $search['pickup_location_id'];

        $search['pickup_location'] = $search['pickup_location_id']
            ? RentalLocation::find($search['pickup_location_id'])
            : null;

        $search['return_location'] = $search['return_location_id']
            ? RentalLocation::find($search['return_location_id'])
            : null;

        return $search;
    }

    private function parseDateTime(?string $date, ?string $time): ?Carbon
    {
        if (! $date) {
            return null;
        }

        try {
            return Carbon::parse(trim($date . ' ' . ($time ?: '10:00')));
        } catch (\Throwable) {
            return null;
        }
    }
}
