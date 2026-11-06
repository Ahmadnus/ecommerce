<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

/**
 * Builds the printable rental contract pack for a booking.
 *
 * The pack is three pages, each one the company's own PDF used as a template
 * with the booking's data written on top of it:
 *
 *   1. rental-contract.pdf     — the stamped RENTAL AGREEMENT (driver, car, money)
 *   2. rental-terms.pdf        — the 20 clauses; renter name filled in
 *   3. vehicle-inspection.pdf  — terms + vehicle condition sheet; car + dates filled in
 *
 * Everything the booking doesn't know (place of birth, odometer, fuel level,
 * signatures …) is deliberately left blank for the counter staff to complete.
 */
class RentalContractService
{
    private const TEMPLATE_DIR = 'resources/contracts';
    private const STORAGE_DIR  = 'contracts';

    private const INK = '#14213d';

    /** Page 1 table geometry, in pixels of a 288-dpi render (1 px = 0.25 pt). */
    private const P1_SCALE = 0.25;

    /** Row top/bottom edges, left (car + money) and right (driver) columns. */
    private const P1_ROWS_LEFT  = [637, 732, 824, 920, 1016, 1111, 1206, 1301, 1396, 1490, 1585, 1680, 1775, 1872, 1960];
    private const P1_ROWS_RIGHT = [642, 736, 831, 926, 1022, 1117, 1211, 1306, 1400, 1495, 1592, 1684, 1779, 1875, 1964];

    /** Free space for the value in each row: [x from, x to] between the two printed labels. */
    private const P1_ZONES_LEFT = [
        [427, 967], [466, 990], [478, 992], [491, 959], [466, 956], [470, 970], [578, 962],
        [521, 999], [494, 956], [520, 1034], [464, 959], [507, 968], [523, 999], [627, 1053],
    ];
    private const P1_ZONES_RIGHT = [
        [1358, 1927], [1450, 2004], [1488, 1930], [1504, 1937], [1390, 1955], [1430, 1949], [1468, 1913],
        [1461, 1922], [1518, 1905], [1468, 1912], [1462, 1923], [1460, 1922], [1518, 1906], [1445, 1914],
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
        $mpdf = $this->makePdf();

        $this->pageContract($mpdf, $booking);
        $this->pageTerms($mpdf, $booking);
        $this->pageInspection($mpdf, $booking);

        return $mpdf->Output('', 'S');
    }

    // ── Pages ────────────────────────────────────────────────────────────────

    private function pageContract(Mpdf $m, Booking $b): void
    {
        $k = $this->startPage($m, 'rental-contract.pdf');

        // The scan has a pre-printed contract number — white it out and print this booking's.
        $this->box($m, $k, 395, 495, 225, 62, self::P1_SCALE, '#ffffff');
        $this->text($m, $k, $this->contractNumber($b), 400, 500, 215, 48, self::P1_SCALE, 'left', 16, true);

        $v = $b->vehicle;
        $cur = $b->currency ?: 'JOD';
        $paid = $b->payment_status === 'paid' ? (float) $b->total_amount : 0.0;
        $remaining = max(0, (float) $b->total_amount - $paid);
        $hasAddDriver = collect($b->extras ?? [])->contains(
            fn($e) => str_contains(strtolower((string) ($e['key'] ?? '')), 'driver')
        );

        // Left column — the car and the money.
        $left = [
            $v?->registration_number,
            $v ? trim($v->brand . ' ' . $v->model) : null,
            $v?->color,
            $v?->year,
            $b->pickup_date_time?->format('d/m/Y'),
            $b->pickup_date_time?->format('H:i'),
            $b->return_date_time?->format('d/m/Y H:i'),
            $b->total_days,
            $this->money($b->daily_rate, $cur),
            $this->money($b->total_amount, $cur),
            $this->money($paid, $cur),
            $this->money($remaining, $cur),
            $b->driver_phone,
            null, // Renter's address — not collected online
        ];

        // Right column — the driver.
        $right = [
            $b->driver_name,
            null, // Nationality — not collected
            $b->driver_date_of_birth?->format('d/m/Y'),
            null, // Place of birth
            $b->driver_national_id,
            null, // Nat. type
            null, // ID expiry
            $b->driver_license_number,
            $b->driver_license_country,
            $b->driver_license_expiry?->format('d/m/Y'),
            $hasAddDriver ? 'نعم / Yes' : null,
            null, null, null, // additional driver's licence details
        ];

        foreach ($left as $i => $value) {
            $this->cell($m, $k, $value, self::P1_ZONES_LEFT[$i], self::P1_ROWS_LEFT[$i], self::P1_ROWS_LEFT[$i + 1]);
        }
        foreach ($right as $i => $value) {
            $this->cell($m, $k, $value, self::P1_ZONES_RIGHT[$i], self::P1_ROWS_RIGHT[$i], self::P1_ROWS_RIGHT[$i + 1]);
        }

        // Blanks inside the legal paragraphs (px of the 288-dpi render; $y is the dotted line's height).
        $line = fn(?string $t, int $x0, int $x1, int $y, float $size = 9) =>
            $this->text($m, $k, $t, $x0, $y - 30, $x1 - $x0, 44, self::P1_SCALE, 'center', $size, true);

        if ((float) $b->security_deposit > 0) {
            $line($this->plain($b->security_deposit), 1822, 2025, 2441);   // "…والبالغة ____ دينار واقر بأن المكتب…"
        }
        $line('عمّان', 1335, 1625, 2557);                                    // "…دائرة تنفيذ محكمة ____"
        if ($remaining > 0) {
            $line($this->plain($remaining), 565, 925, 2804);               // "…المبالغ المترتبة والبالغة ____ دينار بتاريخ"
            if ($due = $b->return_date_time) {                             // …بتاريخ : __ / __ / ____
                $line($due->format('d'), 384, 438, 2804, 8);
                $line($due->format('m'), 322, 384, 2804, 8);
                $line($due->format('Y'), 215, 318, 2804, 8);
            }
        }

        // Renter's printed name under "الفريق الثاني — المستلم (المستأجر)".
        $this->text($m, $k, $b->driver_name, 460, 3018, 445, 50, self::P1_SCALE, 'right', 12, true);
    }

    private function pageTerms(Mpdf $m, Booking $b): void
    {
        // This scan is 586×829 pt; coordinates below are px of a 130-dpi render (1 px = 0.5538 pt).
        $k = $this->startPage($m, 'rental-terms.pdf');
        $s = 72 / 130;

        $this->text($m, $k, $b->driver_name, 580, 1328, 290, 34, $s, 'left', 11, true);   // RENTER NAME :
        $this->text($m, $k, $b->driver_name, 725, 1332, 200, 34, $s, 'right', 11, true);  // اسم المستأجر
    }

    private function pageInspection(Mpdf $m, Booking $b): void
    {
        // Native vector page: coordinates are PDF points.
        $k = $this->startPage($m, 'vehicle-inspection.pdf');
        $v = $b->vehicle;

        $rows = [
            [514.0, 531.9, $v ? trim($v->brand . ' ' . $v->model . ' ' . $v->year) : null],
            [531.9, 549.9, $v?->registration_number],
            [549.9, 579.0, null],  // odometer out — read at the counter
            [579.0, 597.0, null],  // fuel level   — read at the counter
            [597.0, 626.1, $b->pickup_date_time?->format('d/m/Y  H:i')],
            [626.1, 655.3, $b->return_date_time?->format('d/m/Y  H:i')],
        ];

        foreach ($rows as [$y0, $y1, $value]) {
            $this->text($m, $k, $value, 376, $y0, 92, $y1 - $y0, 1, 'center', 9, false, true);
        }

        // Renter's name on the left signature line.
        $this->text($m, $k, $b->driver_name, 52, 747, 186, 16, 1, 'left', 10, true);
    }

    // ── Drawing helpers ──────────────────────────────────────────────────────

    private function makePdf(): Mpdf
    {
        $tmp = storage_path('app/mpdf');
        if (! is_dir($tmp)) {
            mkdir($tmp, 0775, true);
        }

        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 0, 'margin_right'  => 0,
            'margin_top'    => 0, 'margin_bottom' => 0,
            'default_font'  => 'dejavusans',
            'tempDir'       => $tmp,
            'autoLangToFont' => false,
        ]);

        $mpdf->SetTitle('Rental Agreement');
        $mpdf->SetAuthor(config('app.name'));
        $mpdf->SetAutoPageBreak(false);

        return $mpdf;
    }

    /**
     * Add an A4 page with a template PDF laid over it.
     *
     * @return float  Source-points → A4-points factor (1.0 unless the template isn't A4).
     */
    private function startPage(Mpdf $m, string $template): float
    {
        $m->SetSourceFile(base_path(self::TEMPLATE_DIR . '/' . $template));
        $id = $m->ImportPage(1);
        $size = $m->getTemplateSize($id);

        $m->AddPage();
        $m->UseTemplate($id, 0, 0, 210, 297);

        return 595.2756 / ($size['width'] / 0.352778);
    }

    /** One table cell: centred in its zone, vertically centred in its row. */
    private function cell(Mpdf $m, float $k, mixed $value, array $zone, int $y0, int $y1): void
    {
        $this->text($m, $k, $value, $zone[0], $y0, $zone[1] - $zone[0], $y1 - $y0, self::P1_SCALE, 'center', 12, true, true);
    }

    /** A filled rectangle (used to white out printed text). Coordinates in source units × $s pt. */
    private function box(Mpdf $m, float $k, float $x, float $y, float $w, float $h, float $s, string $hex): void
    {
        $f = $s * $k * 0.352778;
        [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');

        $m->SetFillColor($r, $g, $b);
        $m->Rect($x * $f, $y * $f, $w * $f, $h * $f, 'F');
    }

    /**
     * Write text into a box. Coordinates are in source units; $s converts them to points.
     * The font shrinks (never below 6 pt) until the text fits the width.
     */
    private function text(
        Mpdf $m, float $k, mixed $value,
        float $x, float $y, float $w, float $h, float $s,
        string $align = 'left', float $size = 11, bool $bold = false, bool $vcenter = false
    ): void {
        $value = trim((string) $value);
        if ($value === '') {
            return;
        }

        $f = $s * $k * 0.352778;           // source unit → mm
        $wMm = $w * $f;

        $m->SetFont('dejavusans', $bold ? 'B' : '');
        while ($size > 6) {
            $m->SetFontSize($size);
            if ($m->GetStringWidth($value) <= $wMm * 0.98) {
                break;
            }
            $size -= 0.5;
        }

        $rtl = preg_match('/\p{Arabic}/u', $value) === 1;
        $html = sprintf(
            '<div dir="%s" style="font-family:dejavusans;font-size:%spt;color:%s;font-weight:%s;text-align:%s;white-space:nowrap;%s">%s</div>',
            $rtl ? 'rtl' : 'ltr',
            $size,
            self::INK,
            $bold ? 'bold' : 'normal',
            $align,
            $vcenter ? 'line-height:' . ($h * $f) . 'mm;' : '',
            e($value)
        );

        $m->WriteFixedPosHTML($html, $x * $f, $y * $f, $wMm, $h * $f, 'visible');
    }

    // ── Formatting ───────────────────────────────────────────────────────────

    private function contractNumber(Booking $b): string
    {
        return str_pad((string) $b->id, 6, '0', STR_PAD_LEFT);
    }

    private function plain(mixed $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }

    private function money(mixed $amount, string $currency): string
    {
        return $this->plain($amount) . ' ' . $currency;
    }
}
