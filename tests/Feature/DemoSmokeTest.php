<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// adminUser() is declared in CarRentalTest.php and shared across the suite.

it('renders the legacy store admin dashboard', function () {
    $this->actingAs(adminUser())->get('/admin')->assertOk();
});

it('renders the admin settings page', function () {
    $this->actingAs(adminUser())->get('/admin/settings')->assertOk();
});

it('renders legacy admin products, categories and orders', function () {
    $admin = adminUser();
    $this->actingAs($admin)->get('/admin/products')->assertOk();
    $this->actingAs($admin)->get('/admin/categories')->assertOk();
    $this->actingAs($admin)->get('/admin/orders')->assertOk();
});

it('renders the car rental storefront', function () {
    $this->get('/')->assertOk();
    $this->get('/cars')->assertOk();
    $this->get('/branches')->assertOk();
});

it('propagates the admin accent colour to the rental storefront', function () {
    Setting::set('rental_accent_color', '#123456');

    $this->get('/')
        ->assertOk()
        ->assertSee('#123456', false);
});

it('seeds a demo catalogue for the legacy dashboard', function () {
    $this->seed(\Database\Seeders\DemoStoreSeeder::class);

    expect(\App\Models\Category::count())->toBe(6)
        ->and(\App\Models\Product::count())->toBe(14)
        ->and(\App\Models\ProductVariant::count())->toBeGreaterThan(14)
        ->and(\App\Models\Order::count())->toBe(8)
        ->and(\App\Models\OrderItem::count())->toBeGreaterThan(8);

    // Every dashboard status tile should be non-zero.
    foreach (array_keys(\App\Models\Order::statuses()) as $status) {
        expect(\App\Models\Order::where('status', $status)->count())
            ->toBeGreaterThan(0, "no demo orders with status [{$status}]");
    }

    $this->actingAs(adminUser())->get('/admin')->assertOk()->assertSee('14');
    $this->actingAs(adminUser())->get('/admin/products')->assertOk()->assertSee('Linen Midi Dress');
    $this->actingAs(adminUser())->get('/admin/orders')->assertOk()->assertSee('DEMO-1001');
});

it('is safe to run the demo seeder twice', function () {
    $this->seed(\Database\Seeders\DemoStoreSeeder::class);
    $this->seed(\Database\Seeders\DemoStoreSeeder::class);

    expect(\App\Models\Product::count())->toBe(14)
        ->and(\App\Models\Order::count())->toBe(8)
        ->and(\App\Models\OrderItem::count())->toBe(13);
});

it('saves rental branding from the admin settings form', function () {
    $this->actingAs(adminUser())
        ->post('/admin/settings', [
            'rental_accent_color'  => '#abcdef',
            'rental_support_phone' => '0555000111',
            'rental_support_email' => 'demo@example.com',
        ])
        ->assertRedirect();

    expect(Setting::get('rental_accent_color'))->toBe('#abcdef')
        ->and(Setting::get('rental_support_phone'))->toBe('0555000111')
        ->and(Setting::get('rental_support_email'))->toBe('demo@example.com');

    $this->get('/')->assertOk()->assertSee('#abcdef', false);
});
