<?php

namespace Database\Seeders;

use App\Models\HeroBanner;
use App\Models\Page;
use App\Models\SeoSetting;
use App\Models\Setting;
use App\Models\SocialLink;
use App\Support\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Everything the storefront needs to look finished on a fresh install:
 * brand colours, the logo, contact details, social links, hero slides,
 * the feature strip, the legal pages and default SEO.
 *
 * Written to be re-runnable — every row is matched on a stable key and
 * updated in place, so `db:seed` twice does not duplicate anything.
 *
 * URLs and phone numbers here are placeholders. The client replaces them from
 * the dashboard (Settings, Social Links, Pages); nothing below is referenced
 * by code, so changing any of it is safe.
 */
class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedLogo();
        $this->seedSocialLinks();
        $this->seedHeroBanners();
        $this->seedPages();
        $this->seedSeo();
    }

    /**
     * Brand palette + contact details. The two colours are what App\Support\Brand
     * derives the entire storefront theme from, so they are written explicitly
     * rather than left to fall back to the class defaults — that way they show
     * up in the admin colour pickers on a fresh install.
     */
    private function seedSettings(): void
    {
        $settings = [
            // ── Rental storefront identity ──────────────────────────────────
            'rental_accent_color'    => Brand::DEFAULT_ACCENT, // gold, from the WIND mark
            'rental_ink_color'       => Brand::DEFAULT_INK,    // graphite wordmark
            'rental_support_phone'   => '+962 6 500 0000',
            'rental_support_email'   => 'info@wind.jo',
            'rental_support_address' => 'عمان - شارع مكة، المملكة الأردنية الهاشمية',

            // ── Pricing (Jordan) ────────────────────────────────────────────
            'rental_currency'         => 'JOD',
            'rental_vat_rate'         => '16',
            'rental_min_driver_age'   => '21',
            'rental_extra_cdw'        => '8',
            'rental_extra_driver'     => '6',
            'rental_extra_gps'        => '4',
            'rental_extra_child_seat' => '5',

            // ── Legacy admin theme keys, aligned to the same palette so the
            //    dashboard does not look unrelated to the storefront ─────────
            'site_name'         => 'WIND Car Rental',
            'primary_color'     => Brand::DEFAULT_ACCENT,
            'nav_bg_color'      => '#ffffff',
            'bg_color'          => '#ffffff',
            'card_bg_color'     => '#ffffff',
            'footer_bg_color'   => Brand::DEFAULT_INK,
            'footer_text_color' => '#9ca3af',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    /**
     * Attach the bundled WIND mark to the Settings media library so it appears
     * in the admin logo uploader and can be swapped from there. Brand::logoUrl()
     * already falls back to the same file, so this is about making it editable
     * rather than making it visible.
     */
    private function seedLogo(): void
    {
        $source = public_path('images/brand/wind-logo.jpg');

        if (! file_exists($source)) {
            return;
        }

        $holder = Setting::mediaHolder();

        if ($holder->getFirstMedia('logo')) {
            return; // An admin-uploaded logo always wins — never overwrite it.
        }

        $holder->addMedia($source)->preservingOriginal()->toMediaCollection('logo');
    }

    /**
     * Placeholder handles. The floating WhatsApp button is driven by the
     * is_floating row; only one row may carry it at a time.
     */
    private function seedSocialLinks(): void
    {
        $links = [
            ['WhatsApp',  null,                              '0790000000', true,  'fa-brands fa-whatsapp',  1],
            ['Facebook',  'https://facebook.com/',           null,         false, 'fa-brands fa-facebook-f', 2],
            ['Instagram', 'https://instagram.com/',          null,         false, 'fa-brands fa-instagram',  3],
            ['TikTok',    'https://tiktok.com/',             null,         false, 'fa-brands fa-tiktok',     4],
            ['X',         'https://x.com/',                  null,         false, 'fa-brands fa-x-twitter',  5],
        ];

        foreach ($links as [$name, $url, $whatsapp, $floating, $icon, $order]) {
            SocialLink::updateOrCreate(
                ['platform_name' => $name],
                [
                    'url'             => $url,
                    'whatsapp_number' => $whatsapp,
                    'is_floating'     => $floating,
                    'icon_svg'        => $icon,
                    'is_active'       => true,
                    'sort_order'      => $order,
                ]
            );
        }
    }

    /**
     * Hero slides. With none of these the homepage falls back to the featured
     * vehicles, which already looks fine — these give the client something to
     * edit rather than something to create from scratch.
     */
    private function seedHeroBanners(): void
    {
        $rows = [
            [
                'badge'       => ['en' => 'Nationwide',        'ar' => 'في كل الأردن'],
                'title'       => ['en' => 'Rent a car in Jordan', 'ar' => 'استأجر سيارتك في الأردن'],
                'subtitle'    => ['en' => 'From 18 JOD / day',  'ar' => 'ابتداءً من 18 د.أ / يوم'],
                'description' => ['en' => 'Twelve branches, airport pickup, and no hidden fees.',
                                  'ar' => 'اثنا عشر فرعاً، استلام من المطار، وبدون رسوم مخفية.'],
                'button_text' => ['en' => 'Browse the fleet',   'ar' => 'تصفح الأسطول'],
                'button_url'  => '/cars',
            ],
            [
                'badge'       => ['en' => 'Airport',            'ar' => 'المطار'],
                'title'       => ['en' => 'Queen Alia pickup, 24/7', 'ar' => 'استلام من مطار الملكة علياء على مدار الساعة'],
                'subtitle'    => ['en' => 'Meet and greet on arrival', 'ar' => 'استقبال عند الوصول'],
                'description' => ['en' => 'Your car is ready the moment you land.',
                                  'ar' => 'سيارتك جاهزة لحظة وصولك.'],
                'button_text' => ['en' => 'Book now',           'ar' => 'احجز الآن'],
                'button_url'  => '/cars',
            ],
            [
                'badge'       => ['en' => 'Long stay',          'ar' => 'إقامة طويلة'],
                'title'       => ['en' => 'Monthly rentals',    'ar' => 'تأجير شهري'],
                'subtitle'    => ['en' => 'Better rates the longer you stay', 'ar' => 'كلما طالت المدة قلّ السعر'],
                'description' => ['en' => 'Weekly and monthly plans applied automatically at checkout.',
                                  'ar' => 'تُطبَّق الأسعار الأسبوعية والشهرية تلقائياً عند الحجز.'],
                'button_text' => ['en' => 'See rates',          'ar' => 'اطّلع على الأسعار'],
                'button_url'  => '/cars',
            ],
        ];

        foreach ($rows as $i => $row) {
            HeroBanner::updateOrCreate(
                ['button_url' => $row['button_url'], 'sort_order' => $i],
                $row + [
                    'background_color' => Brand::DEFAULT_INK,
                    'text_color'       => '#ffffff',
                    // 'position' is an enum: top | after_featured | after_products.
                    'position'         => 'top',
                    'layout'           => 'text_image',
                    'is_active'        => true,
                    'sort_order'       => $i,
                ]
            );
        }
    }

    /*
     * There is deliberately no site_features seeding here.
     *
     * The rental storefront never reads that table — the "why choose us" strip
     * on the homepage comes from the rental.why_* translation strings with the
     * icons written straight into the Blade. site_features is reachable only
     * from the admin CRUD, so seeding it changes nothing a customer sees.
     *
     * An earlier version of this seeder did populate it, and broke on MySQL:
     * the column is string('icon', 10), sized for the single emoji the
     * migration inserts, and a Font Awesome class does not fit. SQLite ignores
     * varchar limits, so local testing never caught it.
     */

    /**
     * Legal/info pages. Content is deliberately short placeholder prose — the
     * client is expected to replace it, and shipping fake detailed terms would
     * be worse than shipping an obvious stub.
     */
    private function seedPages(): void
    {
        $pages = [
            ['about', ['en' => 'About Us', 'ar' => 'من نحن'], [
                'en' => 'WIND Car Rental operates a modern fleet across Jordan, from Queen Alia International Airport to Aqaba, Petra and the Dead Sea. Replace this text from the dashboard.',
                'ar' => 'ويند لتأجير السيارات تدير أسطولاً حديثاً في مختلف أنحاء الأردن، من مطار الملكة علياء الدولي إلى العقبة والبتراء والبحر الميت. يمكنك تعديل هذا النص من لوحة التحكم.',
            ]],
            ['terms', ['en' => 'Terms & Conditions', 'ar' => 'الشروط والأحكام'], [
                'en' => 'Placeholder rental terms: minimum driver age, licence requirements, fuel policy, mileage limits and the security deposit. Replace with your own terms before going live.',
                'ar' => 'شروط تأجير مبدئية: الحد الأدنى لعمر السائق، متطلبات الرخصة، سياسة الوقود، حدود المسافة، ومبلغ التأمين. يُرجى استبدالها بشروطكم قبل الإطلاق.',
            ]],
            ['privacy', ['en' => 'Privacy Policy', 'ar' => 'سياسة الخصوصية'], [
                'en' => 'Placeholder privacy policy describing what booking data is collected and how long it is kept. Replace before going live.',
                'ar' => 'سياسة خصوصية مبدئية توضح البيانات التي تُجمع عند الحجز ومدة الاحتفاظ بها. يُرجى استبدالها قبل الإطلاق.',
            ]],
            ['faq', ['en' => 'FAQ', 'ar' => 'الأسئلة الشائعة'], [
                'en' => 'What do I need to rent? A valid licence, an ID or passport, and a driver aged 21 or over. Add your own questions from the dashboard.',
                'ar' => 'ما الذي أحتاجه للاستئجار؟ رخصة سارية، هوية أو جواز سفر، وسائق عمره 21 عاماً فأكثر. يمكنك إضافة أسئلتكم من لوحة التحكم.',
            ]],
        ];

        foreach ($pages as $i => [$slug, $name, $content]) {
            Page::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'content' => $content, 'sort_order' => $i, 'is_active' => true]
            );
        }
    }

    /** Default meta for the homepage. Editable under Admin → SEO. */
    private function seedSeo(): void
    {
        if (! Schema::hasTable('seo_settings') || ! class_exists(SeoSetting::class)) {
            return;
        }

        SeoSetting::updateOrCreate(
            ['type' => 'home'],
            [
                'seo_title'       => 'WIND Car Rental — Rent a car in Jordan',
                'seo_description' => 'Car rental across Jordan: Amman, Queen Alia Airport, Aqaba, Petra and the Dead Sea. Daily, weekly and monthly rates in JOD.',
                'seo_keywords'    => 'car rental jordan, تأجير سيارات الأردن, rent a car amman, queen alia airport car hire',
                'og_title'        => 'WIND Car Rental',
                'og_description'  => 'Modern fleet, twelve branches, no hidden fees.',
                'og_type'         => 'website',
                'twitter_card'    => 'summary_large_image',
                'robots'          => 'index,follow',
                'is_active'       => true,
            ]
        );
    }
}
