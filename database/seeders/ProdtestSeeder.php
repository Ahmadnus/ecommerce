<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * ProdtestSeeder — a minimal, purchasable catalogue for testing checkout.
 *
 * Two categories with two products each. Deliberately small so a full
 * purchase can be walked by hand: browse → add to cart → checkout → order.
 *
 * Everything the purchase flow needs is set up, not just the products:
 *
 *   • Each product gets one active variant carrying the stock. Stock lives on
 *     variants in this application (Product::$total_stock sums them and
 *     CartService::validateAdd refuses anything at zero), so a product with no
 *     variant can never be bought.
 *   • One variant per product means nothing has to be chosen, so the cards and
 *     the quick-view popup offer a direct "add to cart".
 *   • Shipping zones are created if the store has none — the checkout page
 *     lists countries that have active zones, and without one there is no way
 *     to complete an order.
 *
 * Safe to re-run: it removes only its own rows, matched by the SKU prefix.
 *
 *     php artisan db:seed --class=ProdtestSeeder
 */
class ProdtestSeeder extends Seeder
{
    private const SKU_PREFIX = 'PRODTEST-';

    public function run(): void
    {
        $this->reset();

        $categories = $this->categories();
        $this->products($categories);
        $this->shippingZones();
        $this->testCustomer();

        $this->report();
    }

    // ── Reset ──────────────────────────────────────────────────────────────

    private function reset(): void
    {
        $ids = Product::withTrashed()
            ->where('sku', 'like', self::SKU_PREFIX . '%')
            ->pluck('id');

        if ($ids->isNotEmpty()) {
            ProductVariant::whereIn('product_id', $ids)->delete();
            Product::withTrashed()->whereIn('id', $ids)->forceDelete();
        }
    }

    // ── Catalogue ──────────────────────────────────────────────────────────

    /** @return array<string, Category> */
    private function categories(): array
    {
        $defs = [
            'test-accessories' => ['en' => 'Test Accessories', 'ar' => 'إكسسوارات تجريبية'],
            'test-home'        => ['en' => 'Test Home',        'ar' => 'منزل تجريبي'],
        ];

        $out   = [];
        $order = 0;

        foreach ($defs as $slug => $name) {
            $out[$slug] = Category::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'is_active' => true, 'sort_order' => 90 + $order++],
            );
        }

        return $out;
    }

    /** @param array<string, Category> $categories */
    private function products(array $categories): void
    {
        //  slug-safe name (en), name (ar), category, price, discount, stock
        $rows = [
            ['Test Leather Wallet', 'محفظة جلد تجريبية',  'test-accessories', 25.00, null,  30],
            ['Test Sunglasses',     'نظارة شمسية تجريبية', 'test-accessories', 40.00, 28.00, 15],
            ['Test Table Lamp',     'مصباح طاولة تجريبي',  'test-home',        60.00, null,  20],
            ['Test Wall Clock',     'ساعة حائط تجريبية',   'test-home',        35.00, 24.50, 12],
        ];

        foreach ($rows as $i => [$en, $ar, $categorySlug, $price, $discount, $stock]) {
            $product = Product::create([
                'name'              => ['en' => $en, 'ar' => $ar],
                'slug'              => 'prodtest-' . \Illuminate\Support\Str::slug($en),
                'short_description' => [
                    'en' => "Test product for the checkout scenario: {$en}.",
                    'ar' => "منتج اختبار لسيناريو الشراء: {$ar}.",
                ],
                'description' => [
                    'en' => "Created by ProdtestSeeder so the purchase flow can be tested end to end.",
                    'ar' => "أُنشئ بواسطة ProdtestSeeder لاختبار عملية الشراء كاملة.",
                ],
                'base_price'     => $price,
                'discount_price' => $discount,
                'sku'            => self::SKU_PREFIX . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
                'status'         => 'active',
                'is_featured'    => $i === 0,
                'sort_order'     => $i,
            ]);

            $product->categories()->sync([$categories[$categorySlug]->id]);

            // One variant, no attributes: nothing to choose, so the product can
            // be added straight from the card or the quick-view popup.
            ProductVariant::create([
                'product_id'     => $product->id,
                'sku'            => $product->sku . '-V1',
                'price_override' => null,
                'stock_quantity' => $stock,
                'is_active'      => true,
            ]);
        }
    }

    // ── Shipping ───────────────────────────────────────────────────────────

    /**
     * The checkout page only lists countries that have active zones, so a
     * store with none cannot complete an order. Add two if the store has no
     * zones at all; an existing shipping setup is left untouched.
     */
    private function shippingZones(): void
    {
        if (Zone::where('is_active', true)->exists()) {
            $this->command?->info('Shipping zones already configured — left as they are.');
            return;
        }

        $country = Country::where('is_active', true)->first() ?? Country::first();

        if (! $country) {
            $this->command?->warn('No country found — add one before testing checkout.');
            return;
        }

        // Note: the zones table has no name_en column, even though the Zone
        // model lists one in $fillable — zone names are single-language here.
        $zones = [
            ['name' => 'وسط المدينة', 'shipping_price' => 2.00, 'delivery_days' => 1],
            ['name' => 'الضواحي',     'shipping_price' => 3.50, 'delivery_days' => 2],
        ];

        foreach ($zones as $i => $zone) {
            Zone::create($zone + [
                'country_id' => $country->id,
                'is_active'  => true,
                'sort_order' => $i,
            ]);
        }

        $this->command?->info("Created 2 shipping zones for {$country->name}.");
    }

    // ── Test customer ──────────────────────────────────────────────────────

    /**
     * Checkout redirects to login unless guest checkout is switched on, so a
     * ready-made customer keeps the scenario walkable without this seeder
     * changing the store's guest-checkout policy. The phone is marked verified
     * so the OTP step does not block the login.
     */
    private function testCustomer(): void
    {
        User::updateOrCreate(
            ['phone' => '+962790001234'],
            [
                'name'              => 'Prodtest Customer',
                'email'             => 'prodtest@example.com',
                'password'          => Hash::make('prodtest123'),
                'phone_verified_at' => now(),
                'country_id'        => Country::where('is_active', true)->value('id'),
            ],
        );

        $this->command?->info('Test customer: prodtest@example.com / +962790001234 — password: prodtest123');
    }

    // ── Summary ────────────────────────────────────────────────────────────

    private function report(): void
    {
        $products = Product::where('sku', 'like', self::SKU_PREFIX . '%')->get();

        $this->command?->info('ProdtestSeeder: '
            . $products->count() . ' products across 2 categories, '
            . $products->sum(fn (Product $p) => $p->total_stock) . ' units in stock.');

        foreach ($products as $p) {
            $this->command?->line('  /products/' . $p->slug);
        }
    }
}
