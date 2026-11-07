<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Setting;
use App\Support\Brand;
use Illuminate\Support\Facades\Storage;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;

/**
 * Builds the printable rental contract pack for a booking (A4, three pages):
 *
 *   1. The rental agreement  — renter, vehicle, period, money, conditions, signatures
 *   2. The 20 contract terms — Arabic / English side by side
 *   3. Vehicle inspection    — condition sheet filled with the car and dates
 *
 * The layout lives in resources/views/contracts/pack.blade.php; this class only
 * gathers the booking's data and hands the HTML to mPDF. Anything the booking
 * doesn't know (place of birth, odometer, fuel level, signatures …) is left
 * blank for the counter staff to complete by hand.
 */
class RentalContractService
{
    private const STORAGE_DIR = 'contracts';
    private const ASSET_DIR   = 'resources/contracts';
    private const FONT_DIR    = 'resources/fonts/contracts';

    /** Company rules printed under the money table — edit here, not in the view. */
    private const RULES = [
        'deductible_accident' => 500,   // JD, in case of accident
        'deductible_total'    => 3000,  // JD, in case of total damage
        'km_per_day'          => 200,   // used when the vehicle has no limit of its own
        'extra_km_fee'        => 15,    // qirsh per extra km
        'late_hour_fee'       => 5,     // JD per late hour
        'late_hours_full_day' => 3,     // hours after which a full day is charged
        'delivery_fee'        => 20,    // JD, delivery / pick-up outside the office
        'wash_fee'            => 5,     // JD
    ];

    /** The 20 contract clauses, [Arabic, English]. Arabic is the binding text (clause 18). */
    private const TERMS = [
        ['تعتبر مقدمة العقد وشروطه وملحقه جزءاً لا يتجزأ منه وتقرأ معه.',
         'Preamble, terms, and annex are integral parts read as one.'],
        ['يلتزم المستأجر فور انتهاء الإيجار بتسليم السيارة شخصياً لمكتب المؤجرة بنفس حالتها والتوابع، وإلا تعتبر مسروقة ما لم تمدد خطياً.',
         'Lessee must return vehicle in person with all accessories; otherwise deemed stolen unless extended in writing.'],
        ['يلتزم المستأجر بـ: أ. عدم نقل بضائع مخالفة | ب. عدم نقل ركاب بالأجرة | ج. عدم سحب مركبات | د. عدم المشاركة بالسباقات | هـ. عدم قيادة غير المخولين | و. عدم القيادة تحت تأثير الكحول/المخدرات | ز. التزام قوانين السير.',
         'Lessee undertakes: a. No illegal goods | b. No passengers/goods for hire | c. No towing | d. No racing | e. No unauthorized drivers | f. No driving under influence | g. Obey traffic laws.'],
        ['مراجعة إدارة السير عند ارتكاب أي مخالفة لإجراء المقتضى.',
         'Lessee must handle traffic violations at traffic dept.'],
        ['المؤجر غير مسؤول عن الوقود طيلة مدة الإيجار.',
         'Lessor is not responsible for fuel costs.'],
        ['المؤجر غير مسؤول عن أي خسارة أو تعطيل ناتج عن خلل مفاجئ بالسيارة.',
         'Lessor is not liable for losses/expenses from mechanical failure.'],
        ['عند حادث: أ. إبلاغ المكتب فوراً | ب. استخراج كروكة | ج. حراسة وسحب السيارة | د. مراجعة المكتب للبراءة | هـ. إذا أثبتت الكروكة عدم مسؤوليته لا يتحمل تكاليف | و. إذا كان مسؤولاً يدفع 500 د فتح حادث و46 د للطرف الثاني وإيجار التصليح ونقص القيمة | ز. كروكة بدون طرف ثاني يدفع 650 د | ح. بدون موافقة يتحمل كامل الإصلاح.',
         'In case of accident/damage: a. Notify lessor immediately | b. Obtain police report (Kroka) | c. Secure/tow vehicle | d. Visit office for clearance | e. If not at fault: no costs | f. If at fault: 500 JOD fee, 46 JOD 2nd-party fee, repair rent, & depreciation | g. Single-party: pays repair fines | h. No report: pays 100% repair & rent.'],
        ['عند شطب المركبة (وكان مسؤولاً) يتحمل المستأجر 2000 دينار.',
         'Total loss (if at fault) lessee pays 2,000 JOD.'],
        ['يدفع كامل الإصلاح والإيجار إذا نتج الضرر عن مخالفة البند 3 أو سوء الاستعمال أو حرق المحرك/القير.',
         'Lessee pays full repair & rent for: clause 3 breach, misuse, or engine/gearbox damage.'],
        ['تعويض أضرار الغير في حال عدم وجود مخطط كروكي.',
         'Liable for third-party damage if no police report exists.'],
        ['المسؤولية المدنية كاملة على المستأجر دون أدنى مسؤولية على الشركة.',
         'Full civil liability on lessee; none on rental company.'],
        ['يحق للشركة سحب السيارة فوراً عند مخالفة الشروط.',
         'Lessor may repossess vehicle if any term is violated.'],
        ['الشركة غير مسؤولة عن المفقودات داخل السيارة.',
         'Lessor is not responsible for lost belongings.'],
        ['موعد التسليم 2:00 ظهراً، وتأخير ساعة فأكثر يحسب يوماً.',
         'Return time is 2:00 PM. 1+ hr delay full day charge.'],
        ['سداد التزاماته بالدينار الأردني خلال يومين كحد أقصى.',
         'Financial obligations payable in JOD within 2 days.'],
        ['وجوب الحصول على مخالصة وبراءة ذمة خطية عند التسليم.',
         'Written clearance must be obtained upon vehicle return.'],
        ['للمؤجرة حق الملاحقة القضائية دون الحاجة لإخطار عدلي.',
         'Lessor may sue without legal notices.'],
        ['النص العربي هو النص القانوني المعتمد.',
         'Arabic text is the legally binding version.'],
        ['الاختصاص المكاني لمحكمة بداية/صلح عمان.',
         'Jurisdiction: Amman Conciliation/First Instance Court.'],
        ['العقد من 20 بنداً ويقرأ كوحدة واحدة.',
         'Contract consists of 20 clauses read as one unit.'],
    ];

    // ── Public API ───────────────────────────────────────────────────────────

    /** Relative path (on the private "local" disk) where a booking's contract lives. */
    public function path(Booking $booking): string
    {
        return self::STORAGE_DIR . '/' . $booking->booking_reference . '.pdf';
    }

    /** Build the PDF and save it. Returns the storage path. */
    public function generate(Booking $booking): string
    {
        $booking->loadMissing(['vehicle.category', 'pickupLocation', 'returnLocation']);

        $pdf = $this->render($booking);

        Storage::disk('local')->put($path = $this->path($booking), $pdf);

        return $path;
    }

    /** The saved contract, building it first if it doesn't exist yet. */
    public function ensure(Booking $booking): string
    {
        return Storage::disk('local')->exists($this->path($booking))
            ? $this->path($booking)
            : $this->generate($booking);
    }

    /** Raw PDF bytes, without touching storage. */
    public function render(Booking $booking): string
    {
        $data = $this->viewData($booking);

        $mpdf = $this->makePdf();
        $mpdf->SetHTMLFooter($this->footer($data));
        $mpdf->WriteHTML(view('contracts.pack', ['d' => $data])->render(), HTMLParserMode::DEFAULT_MODE);

        return $mpdf->Output('', 'S');
    }

    // ── Data ─────────────────────────────────────────────────────────────────

    /** Everything the view prints, already formatted. */
    private function viewData(Booking $b): array
    {
        $v        = $b->vehicle;
        $cur      = $b->currency ?: 'JOD';
        $paid     = $b->payment_status === 'paid' ? (float) $b->total_amount : 0.0;
        $remaining = max(0, (float) $b->total_amount - $paid);
        $hasAddDriver = collect($b->extras ?? [])->contains(
            fn($e) => str_contains(strtolower((string) ($e['key'] ?? '')), 'driver')
        );

        $rules = self::RULES;
        if ($v && (int) $v->mileage_limit_per_day > 0) {
            $rules['km_per_day'] = (int) $v->mileage_limit_per_day;
        }

        $accent = Brand::accent();
        $ink    = Brand::ink();

        return [
            'ink'          => $ink,
            'accent'       => $accent,
            'accent_dark'  => $this->mix($accent, '#000000', 0.28),
            'accent_light' => $this->mix($accent, '#ffffff', 0.45),
            'accent_fg'    => $this->luminance($accent) > 0.55 ? $ink : '#ffffff',
            'tint'         => $this->mix($accent, '#ffffff', 0.90),
            'line'         => $this->mix($ink, '#ffffff', 0.72),

            'logo'    => $this->asset(self::ASSET_DIR . '/wind-logo.png'),
            'diagram' => $this->asset(self::ASSET_DIR . '/inspection-diagram.png'),

            'company' => [
                'name_ar' => (string) trans('rental.brand', [], 'ar'),
                'name_en' => (string) trans('rental.brand', [], 'en'),
                'phone'   => (string) Setting::get('rental_support_phone', ''),
                'email'   => (string) Setting::get('rental_support_email', ''),
                'address' => (string) Setting::get('rental_support_address', ''),
            ],

            'contract_no' => str_pad((string) $b->id, 6, '0', STR_PAD_LEFT),
            'reference'   => (string) $b->booking_reference,
            'issued_at'   => ($b->created_at ?? now())->format('d/m/Y'),

            'renter' => [
                'name'          => $b->driver_name,
                'nationality'   => null, // not collected
                'dob'           => $b->driver_date_of_birth?->format('d/m/Y'),
                'pob'           => null, // not collected
                'national_id'   => $b->driver_national_id,
                'id_type'       => null, // not collected
                'id_expiry'     => null, // not collected
                'license_no'    => $b->driver_license_number,
                'license_place' => $b->driver_license_country,
                'license_expiry' => $b->driver_license_expiry?->format('d/m/Y'),
                'phone'         => $b->driver_phone,
                'address'       => null, // not collected
                'add_driver'    => $hasAddDriver ? 'نعم / Yes' : null,
            ],

            'car' => [
                'plate' => $v?->registration_number,
                'type'  => $v ? trim($v->brand . ' ' . $v->model) : null,
                'color' => $v?->color,
                'year'  => $v?->year,
            ],

            'rental' => [
                'date_out'      => $b->pickup_date_time?->format('d/m/Y'),
                'time_out'      => $b->pickup_date_time?->format('H:i'),
                'return_at'     => $b->return_date_time?->format('d/m/Y  H:i'),
                'days'          => $b->total_days,
                'branch'        => $this->branches($b),
            ],

            'money' => [
                'daily'     => $this->money($b->daily_rate, $cur),
                'total'     => $this->money($b->total_amount, $cur),
                'paid'      => $this->money($paid, $cur),
                'remaining' => $this->money($remaining, $cur),
            ],

            'rules' => $rules,

            // Values dropped into the blanks of the legal paragraphs.
            'legal' => [
                'deposit'  => (float) $b->security_deposit > 0 ? $this->plain($b->security_deposit) : null,
                'court'    => 'عمّان',
                'due'      => $remaining > 0 ? $this->plain($remaining) : null,
                'due_date' => $remaining > 0 ? $b->return_date_time?->format('d / m / Y') : null,
            ],

            'terms' => array_map(fn($t) => ['ar' => $t[0], 'en' => $t[1]], self::TERMS),

            'inspection' => [
                'model'    => $v ? trim($v->brand . ' ' . $v->model . ' ' . $v->year) : null,
                'plate'    => $v?->registration_number,
                'date_out' => $b->pickup_date_time?->format('d/m/Y  H:i'),
                'date_in'  => $b->return_date_time?->format('d/m/Y  H:i'),
            ],
        ];
    }

    // ── PDF plumbing ─────────────────────────────────────────────────────────

    private function makePdf(): Mpdf
    {
        $tmp = storage_path('app/mpdf');
        if (! is_dir($tmp)) {
            mkdir($tmp, 0775, true);
        }

        $dirs  = (new ConfigVariables())->getDefaults()['fontDir'];
        $fonts = (new FontVariables())->getDefaults()['fontdata'];

        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 11, 'margin_right'  => 11,
            'margin_top'    => 9,  'margin_bottom' => 15,
            'margin_footer' => 6,
            'default_font'  => 'tajawal',
            'tempDir'       => $tmp,
            'fontDir'       => array_merge($dirs, [base_path(self::FONT_DIR)]),
            'fontdata'      => $fonts + [
                'tajawal' => [
                    'R'          => 'Tajawal-Regular.ttf',
                    'B'          => 'Tajawal-Bold.ttf',
                    'useOTL'     => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'autoLangToFont' => false,
        ]);

        $mpdf->SetTitle('Rental Agreement');
        $mpdf->SetAuthor(config('app.name'));
        $mpdf->SetDirectionality('rtl');

        return $mpdf;
    }

    private function footer(array $d): string
    {
        $contact = collect([$d['company']['phone'], $d['company']['email']])->filter()->implode('   •   ');

        return '<table width="100%" style="font-family:tajawal;font-size:7pt;color:#8a8478;border-top:0.2mm solid #d9d3c5;"><tr>'
             . '<td width="38%" style="text-align:right;padding-top:1mm">' . e($d['company']['name_ar']) . '</td>'
             . '<td width="24%" style="text-align:center;padding-top:1mm" dir="ltr">' . e($d['contract_no']) . '  ·  {PAGENO} / {nbpg}</td>'
             . '<td width="38%" style="text-align:left;padding-top:1mm" dir="ltr">' . e($contact) . '</td>'
             . '</tr></table>';
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /** Local path of a bundled asset, in the form mPDF's <img> accepts on every OS. */
    private function asset(string $relative): string
    {
        return str_replace('\\', '/', base_path($relative));
    }

    /** "Pick-up" alone, or "Pick-up ← Return" for a one-way rental. */
    private function branches(Booking $b): ?string
    {
        $from = $this->branchName($b->pickupLocation);
        $to   = $this->branchName($b->returnLocation);

        return $from && $to && $from !== $to ? "{$from} ← {$to}" : ($from ?: $to);
    }

    private function branchName($location): ?string
    {
        if (! $location) {
            return null;
        }

        $name = method_exists($location, 'getTranslation')
            ? ($location->getTranslation('name', 'ar', false) ?: $location->getTranslation('name', 'en', false))
            : $location->name;

        return $name ?: null;
    }

    private function plain(mixed $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }

    private function money(mixed $amount, string $currency): string
    {
        return $this->plain($amount) . ' ' . $currency;
    }

    /** Blend two #rrggbb colours; $ratio is the share of $with (0–1). */
    private function mix(string $hex, string $with, float $ratio): string
    {
        [$r1, $g1, $b1] = sscanf($hex, '#%02x%02x%02x');
        [$r2, $g2, $b2] = sscanf($with, '#%02x%02x%02x');

        return sprintf(
            '#%02x%02x%02x',
            (int) round($r1 + ($r2 - $r1) * $ratio),
            (int) round($g1 + ($g2 - $g1) * $ratio),
            (int) round($b1 + ($b2 - $b1) * $ratio)
        );
    }

    /** Perceived brightness, 0 (black) – 1 (white). */
    private function luminance(string $hex): float
    {
        [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');

        return (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    }
}
