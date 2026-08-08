<?php

use App\Models\Booking;
use App\Models\RentalLocation;
use App\Models\SocialLink;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Services\RentalPricingService;
use Spatie\Permission\Models\Role;

/**
 * Smoke + behaviour coverage for the car-rental platform: storefront pages,
 * admin panels, availability rules and the pricing breakdown.
 */

function makeLocation(array $attributes = []): RentalLocation
{
    return RentalLocation::create(array_merge([
        'name'        => ['en' => 'Queen Alia Airport', 'ar' => 'مطار الملكة علياء'],
        'city'        => ['en' => 'Amman', 'ar' => 'عمان'],
        'type'        => RentalLocation::TYPE_AIRPORT,
        'pickup_fee'  => 50,
        'one_way_fee' => 120,
        'is_active'   => true,
    ], $attributes));
}

function makeVehicle(array $attributes = []): Vehicle
{
    $category = VehicleCategory::create([
        'name'      => ['en' => 'Sedan', 'ar' => 'سيدان'],
        'is_active' => true,
    ]);

    return Vehicle::create(array_merge([
        'vehicle_category_id' => $category->id,
        'brand'               => 'Toyota',
        'model'               => 'Camry',
        'year'                => 2024,
        'transmission'        => 'automatic',
        'fuel_type'           => 'petrol',
        'seats'               => 5,
        'doors'               => 4,
        'bags'                => 4,
        'daily_rate'          => 200,
        'weekly_rate'         => 1260,
        'monthly_rate'        => 4800,
        'security_deposit'    => 1000,
        'units_available'     => 1,
        'availability_status' => Vehicle::STATUS_AVAILABLE,
        'is_active'           => true,
        'is_featured'         => true,
    ], $attributes));
}

function adminUser(): User
{
    Role::findOrCreate('admin', 'web');

    // Built directly rather than via User::factory(): the factory still sets
    // 'email_verified_at', a column this project's users table doesn't have.
    $user = User::create([
        'name'      => 'Admin Tester',
        'email'     => 'admin' . uniqid() . '@example.com',
        'password'  => bcrypt('password'),
        'is_admin'  => true,
    ]);

    $user->assignRole('admin');

    return $user;
}

// ── Storefront ───────────────────────────────────────────────────────────────

it('renders the rental homepage', function () {
    makeVehicle();
    makeLocation();

    // The header shows the brand mark — the uploaded logo when there is one,
    // otherwise the bundled WIND image. The old 'KEY' wordmark is only the
    // last-resort fallback when neither exists.
    $this->get('/')->assertOk()->assertSee(\App\Support\Brand::logoUrl() ?? 'KEY', false);
});

it('renders the fleet listing and the vehicle detail page', function () {
    $vehicle = makeVehicle();
    makeLocation();

    $this->get('/cars')->assertOk()->assertSee('Toyota Camry');
    $this->get("/cars/{$vehicle->slug}")->assertOk()->assertSee('Toyota Camry');
});

it('renders branches and the manage-booking page', function () {
    makeLocation();

    $this->get('/branches')->assertOk();
    $this->get('/manage-booking')->assertOk();
});

// ── Availability ─────────────────────────────────────────────────────────────

it('excludes a vehicle whose only unit is already booked for the window', function () {
    $vehicle  = makeVehicle(['units_available' => 1]);
    $location = makeLocation();

    Booking::create([
        'vehicle_id'            => $vehicle->id,
        'pickup_location_id'    => $location->id,
        'return_location_id'    => $location->id,
        'pickup_date_time'      => '2030-01-10 10:00',
        'return_date_time'      => '2030-01-15 10:00',
        'total_days'            => 5,
        'driver_name'           => 'Booked Already',
        'driver_phone'          => '0500000000',
        'driver_license_number' => 'L1',
        'booking_status'        => Booking::STATUS_CONFIRMED,
    ]);

    // Overlapping window → unavailable.
    expect($vehicle->isAvailableBetween('2030-01-12 10:00', '2030-01-14 10:00'))->toBeFalse();

    // Window that starts exactly when the other ends → still available.
    expect($vehicle->isAvailableBetween('2030-01-15 10:00', '2030-01-17 10:00'))->toBeTrue();

    // A second unit frees it up again.
    $vehicle->update(['units_available' => 2]);
    expect($vehicle->isAvailableBetween('2030-01-12 10:00', '2030-01-14 10:00'))->toBeTrue();
});

// ── Pricing ──────────────────────────────────────────────────────────────────

it('builds a price breakdown with fees, extras and VAT', function () {
    $vehicle = makeVehicle();
    $pickup  = makeLocation();
    $return  = makeLocation(['name' => ['en' => 'Abdali', 'ar' => 'العبدلي'], 'pickup_fee' => 0, 'one_way_fee' => 120]);

    $quote = app(RentalPricingService::class)->quote(
        $vehicle,
        Carbon\Carbon::parse('2030-03-01 10:00'),
        Carbon\Carbon::parse('2030-03-04 10:00'),
        $pickup,
        $return,
        ['cdw']
    );

    expect($quote['days'])->toBe(3)
        ->and($quote['subtotal'])->toBe(600.0)          // 200 × 3
        ->and($quote['location_fee'])->toBe(170.0)      // 50 pickup + 120 one-way
        ->and($quote['extras_total'])->toBe(24.0)       // CDW 8 × 3
        ->and($quote['tax_amount'])->toBe(127.04)      // Jordan GST 16% of 794
        ->and($quote['total'])->toBe(921.04);
});

it('steps down to the weekly and monthly rate plans', function () {
    $vehicle = makeVehicle();

    expect($vehicle->rateForDays(3)['plan'])->toBe('daily')
        ->and($vehicle->rateForDays(7)['plan'])->toBe('weekly')
        ->and($vehicle->rateForDays(30)['plan'])->toBe('monthly');
});

// ── Booking flow ─────────────────────────────────────────────────────────────

it('creates a booking and shows the confirmation', function () {
    $vehicle  = makeVehicle();
    $location = makeLocation();

    $response = $this->post("/book/{$vehicle->slug}", [
        'pickup_location_id'    => $location->id,
        'return_location_id'    => $location->id,
        'pickup_date'           => '2030-05-01',
        'pickup_time'           => '10:00',
        'return_date'           => '2030-05-04',
        'return_time'           => '10:00',
        'driver_name'           => 'Ahmad Test',
        'driver_phone'          => '0791234567',
        'driver_license_number' => 'LIC-1',
        'terms'                 => 1,
    ]);

    $booking = Booking::first();

    expect($booking)->not->toBeNull()
        ->and($booking->total_days)->toBe(3)
        ->and($booking->booking_status)->toBe(Booking::STATUS_PENDING);

    $response->assertRedirect(route('rental.booking.success', $booking->booking_reference));
    $this->get("/booking/confirmed/{$booking->booking_reference}")->assertOk()->assertSee($booking->booking_reference);
});

it('refuses to double-book the last available unit', function () {
    $vehicle  = makeVehicle(['units_available' => 1]);
    $location = makeLocation();

    $payload = [
        'pickup_location_id'    => $location->id,
        'return_location_id'    => $location->id,
        'pickup_date'           => '2030-06-01',
        'pickup_time'           => '10:00',
        'return_date'           => '2030-06-04',
        'return_time'           => '10:00',
        'driver_name'           => 'First Customer',
        'driver_phone'          => '0501111111',
        'driver_license_number' => 'LIC-1',
        'terms'                 => 1,
    ];

    $this->post("/book/{$vehicle->slug}", $payload);

    $this->post("/book/{$vehicle->slug}", array_merge($payload, [
        'driver_name'  => 'Second Customer',
        'driver_phone' => '0502222222',
    ]));

    expect(Booking::count())->toBe(1);
});

it('finds a booking by reference and phone, in any Jordanian phone format', function () {
    $vehicle  = makeVehicle();
    $location = makeLocation();

    $this->post("/book/{$vehicle->slug}", [
        'pickup_location_id'    => $location->id,
        'return_location_id'    => $location->id,
        'pickup_date'           => '2030-07-01',
        'pickup_time'           => '10:00',
        'return_date'           => '2030-07-03',
        'return_time'           => '10:00',
        'driver_name'           => 'Lookup Customer',
        'driver_phone'          => '0791234567',
        'driver_license_number' => 'LIC-9',
        'terms'                 => 1,
    ]);

    $reference = Booking::first()->booking_reference;

    foreach (['0791234567', '+962791234567', '962791234567'] as $phone) {
        $this->post('/manage-booking', [
            'booking_reference' => $reference,
            'driver_phone'      => $phone,
        ])->assertOk()->assertSee('Lookup Customer');
    }

    // A wrong phone must not expose the booking.
    $this->post('/manage-booking', [
        'booking_reference' => $reference,
        'driver_phone'      => '0799999999',
    ])->assertRedirect();
});

// ── Admin ────────────────────────────────────────────────────────────────────

it('renders every car-rental admin page', function () {
    $admin    = adminUser();
    $vehicle  = makeVehicle();
    $location = makeLocation();
    $category = VehicleCategory::first();

    $booking = Booking::create([
        'vehicle_id'            => $vehicle->id,
        'pickup_location_id'    => $location->id,
        'return_location_id'    => $location->id,
        'pickup_date_time'      => '2030-01-10 10:00',
        'return_date_time'      => '2030-01-15 10:00',
        'total_days'            => 5,
        'driver_name'           => 'Admin View',
        'driver_phone'          => '0503333333',
        'driver_license_number' => 'L2',
    ]);

    // Each page is asserted on a string from its own body, not just a 200 —
    // these views @extend the admin layout, so a wrong @section name renders
    // the chrome with an empty content area and still returns 200.
    $pages = [
        '/admin/vehicles'                              => 'إدارة أسطول السيارات',
        '/admin/vehicles/create'                       => 'إضافة سيارة جديدة',
        "/admin/vehicles/{$vehicle->id}/edit"          => 'بيانات السيارة',
        '/admin/bookings'                              => 'إدارة الحجوزات والتأجير',
        "/admin/bookings/{$booking->id}"               => 'بيانات السائق',
        '/admin/locations'                             => 'الفروع ومواقع الاستلام',
        '/admin/locations/create'                      => 'إضافة فرع جديد',
        "/admin/locations/{$location->id}/edit"        => 'رسوم الاستلام',
        '/admin/vehicle-categories'                    => 'فئات السيارات',
        '/admin/vehicle-categories/create'             => 'إضافة فئة جديدة',
        "/admin/vehicle-categories/{$category->id}/edit" => 'ترتيب العرض',
    ];

    foreach ($pages as $url => $expected) {
        $this->actingAs($admin)->get($url)->assertOk()->assertSee($expected, false);
    }
});

it('lets an admin advance a booking through its lifecycle', function () {
    $admin    = adminUser();
    $vehicle  = makeVehicle();
    $location = makeLocation();

    $booking = Booking::create([
        'vehicle_id'            => $vehicle->id,
        'pickup_location_id'    => $location->id,
        'return_location_id'    => $location->id,
        'pickup_date_time'      => '2030-01-10 10:00',
        'return_date_time'      => '2030-01-15 10:00',
        'total_days'            => 5,
        'driver_name'           => 'Lifecycle',
        'driver_phone'          => '0504444444',
        'driver_license_number' => 'L3',
    ]);

    $this->actingAs($admin)
        ->patch("/admin/bookings/{$booking->id}/status", ['booking_status' => Booking::STATUS_CONFIRMED]);

    expect($booking->fresh()->booking_status)->toBe(Booking::STATUS_CONFIRMED)
        ->and($booking->fresh()->confirmed_at)->not->toBeNull();

    $this->actingAs($admin)
        ->patch("/admin/bookings/{$booking->id}/status", ['booking_status' => Booking::STATUS_ACTIVE]);

    expect($booking->fresh()->picked_up_at)->not->toBeNull();

    $this->actingAs($admin)
        ->patch("/admin/bookings/{$booking->id}/status", ['booking_status' => Booking::STATUS_COMPLETED]);

    expect($booking->fresh()->returned_at)->not->toBeNull();
});

it('creates a vehicle from the admin fleet form', function () {
    $admin = adminUser();
    makeLocation();

    $this->actingAs($admin)->post('/admin/vehicles', [
        'brand'               => 'Hyundai',
        'model'               => 'Sonata',
        'year'                => 2025,
        'transmission'        => 'automatic',
        'fuel_type'           => 'petrol',
        'seats'               => 5,
        'doors'               => 4,
        'bags'                => 3,
        'daily_rate'          => 150,
        'min_driver_age'      => 21,
        'min_rental_days'     => 1,
        'availability_status' => Vehicle::STATUS_AVAILABLE,
        'units_available'     => 4,
        'features'            => ['Bluetooth', '', 'Cruise Control'],
    ])->assertRedirect(route('admin.vehicles.index'));

    $vehicle = Vehicle::where('model', 'Sonata')->first();

    expect($vehicle)->not->toBeNull()
        ->and($vehicle->slug)->toBe('hyundai-sonata-2025')
        // Empty rows from the dynamic feature list are dropped.
        ->and($vehicle->features)->toBe(['Bluetooth', 'Cruise Control']);
});

/*
 * The floating contact button is a data decision. Enabling it must add the
 * wa.me link to every page; disabling it must remove the markup entirely,
 * rather than leaving it in the DOM behind a CSS rule.
 */
it('hides the floating contact button until an admin enables one', function () {
    makeVehicle();
    makeLocation();

    $this->get('/')->assertOk()->assertDontSee('wa.me', false);
});

it('shows the floating contact button on every page once enabled', function () {
    makeVehicle();
    makeLocation();

    $link = SocialLink::create([
        'platform_name'   => 'WhatsApp',
        'whatsapp_number' => '0790000000',
        'is_floating'     => true,
        'is_active'       => true,
    ]);

    // The national number is normalised to the +962 form wa.me expects.
    foreach (['/', '/cars', '/branches'] as $page) {
        $this->get($page)->assertOk()->assertSee('wa.me/962790000000', false);
    }

    // Deactivating the row must take the markup away, not just hide it.
    $link->update(['is_active' => false]);
    $this->get('/')->assertOk()->assertDontSee('wa.me', false);
});

it('lets an admin toggle a social link and promote it to the floating button', function () {
    $this->actingAs(adminUser());

    $facebook = SocialLink::create([
        'platform_name' => 'Facebook',
        'url'           => 'https://facebook.com/wind',
        'is_active'     => true,
    ]);

    $this->patch(route('admin.social-links.toggle', $facebook), ['column' => 'is_active'])
        ->assertRedirect();
    expect($facebook->fresh()->is_active)->toBeFalse();

    // Only one link may drive the floating button at a time.
    $whatsapp = SocialLink::create([
        'platform_name'   => 'WhatsApp',
        'whatsapp_number' => '0791111111',
        'is_floating'     => true,
        'is_active'       => true,
    ]);

    $this->patch(route('admin.social-links.toggle', $facebook), ['column' => 'is_floating'])
        ->assertRedirect();

    expect($facebook->fresh()->is_floating)->toBeTrue()
        ->and($whatsapp->fresh()->is_floating)->toBeFalse();
});

it('updates a social link from the dashboard', function () {
    $this->actingAs(adminUser());

    $link = SocialLink::create([
        'platform_name' => 'Insta',
        'url'           => 'https://instagram.com/old',
        'is_active'     => true,
    ]);

    $this->put(route('admin.social-links.update', $link), [
        'platform_name' => 'Instagram',
        'url'           => 'https://instagram.com/wind',
        'icon_svg'      => 'fa-brands fa-instagram',
        'is_active'     => '1',
    ])->assertRedirect();

    $link->refresh();

    expect($link->platform_name)->toBe('Instagram')
        ->and($link->url)->toBe('https://instagram.com/wind')
        ->and($link->is_active)->toBeTrue()
        // Absent checkbox means off, not unchanged.
        ->and($link->is_floating)->toBeFalse();
});
