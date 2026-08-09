<?php

namespace App\Services;

use App\Models\RentalExtra;
use App\Models\RentalLocation;
use App\Models\Setting;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

/**
 * Single source of truth for what a rental costs.
 *
 * Both the storefront quote and the admin booking form call quote() so the
 * customer-facing breakdown and the stored booking totals can never drift.
 */
class RentalPricingService
{
    /**
     * Optional add-ons offered during checkout, keyed by code.
     *
     * Read from the rental_extras table so the admin can rename, reprice, add
     * and retire them. The hardcoded set below is only a fallback for the
     * window between deploying this code and running the migration — without
     * it, pulling the new code before migrating would silently drop every
     * add-on from the booking form.
     *
     * @return array<string, array{label: string, label_ar: string, price: float, per_day: bool, icon: string}>
     */
    public function availableExtras(): array
    {
        if (! Schema::hasTable('rental_extras')) {
            return $this->fallbackExtras();
        }

        $extras = RentalExtra::active()->ordered()->get();

        if ($extras->isEmpty()) {
            return $this->fallbackExtras();
        }

        return $extras->mapWithKeys(
            fn (RentalExtra $extra) => [$extra->code => $extra->toCatalogueEntry()]
        )->all();
    }

    /**
     * The original four add-ons, priced from the legacy `rental_extra_*`
     * settings keys. Used only when rental_extras is missing or empty.
     *
     * @return array<string, array{label: string, label_ar: string, price: float, per_day: bool, icon: string}>
     */
    private function fallbackExtras(): array
    {
        return [
            'cdw' => [
                'label'    => 'Collision Damage Waiver',
                'label_ar' => 'تأمين ضد الحوادث',
                'price'    => (float) Setting::get('rental_extra_cdw', 8),
                'per_day'  => true,
                'icon'     => 'fa-solid fa-shield-halved',
            ],
            'additional_driver' => [
                'label'    => 'Additional Driver',
                'label_ar' => 'سائق إضافي',
                'price'    => (float) Setting::get('rental_extra_driver', 6),
                'per_day'  => true,
                'icon'     => 'fa-solid fa-user-plus',
            ],
            'gps' => [
                'label'    => 'GPS Navigation',
                'label_ar' => 'جهاز ملاحة',
                'price'    => (float) Setting::get('rental_extra_gps', 4),
                'per_day'  => true,
                'icon'     => 'fa-solid fa-location-arrow',
            ],
            'child_seat' => [
                'label'    => 'Child Seat',
                'label_ar' => 'مقعد أطفال',
                'price'    => (float) Setting::get('rental_extra_child_seat', 5),
                'per_day'  => true,
                'icon'     => 'fa-solid fa-baby',
            ],
        ];
    }

    public function vatRate(): float
    {
        return (float) Setting::get('rental_vat_rate', 16) / 100;
    }

    public function currency(): string
    {
        return (string) Setting::get('rental_currency', 'JOD');
    }

    /**
     * Rental days are billed as started 24-hour periods, with a floor of one
     * day and of the vehicle's own minimum. A 25-hour rental bills as 2 days,
     * which is how rental desks actually charge.
     */
    public function billableDays(Carbon $pickup, Carbon $return, ?Vehicle $vehicle = null): int
    {
        // Carbon 3 returns a float here, so an exact 72h rental can come back as
        // 72.000004 and ceil() to 4 days. Round to whole minutes first so the
        // day count is driven by the clock, not by microsecond noise.
        $minutes = (int) round($pickup->diffInMinutes($return, absolute: true));
        $days    = max(1, (int) ceil($minutes / 1440));

        if ($vehicle && $vehicle->min_rental_days > $days) {
            $days = (int) $vehicle->min_rental_days;
        }

        return $days;
    }

    /**
     * Full price breakdown for a prospective booking.
     *
     * @param  array<string>  $extraKeys  Keys from availableExtras()
     * @return array{
     *   days:int, rate_plan:string, per_day:float, subtotal:float,
     *   pickup_fee:float, one_way_fee:float, location_fee:float,
     *   extras:array, extras_total:float, taxable:float, vat_rate:float,
     *   tax_amount:float, security_deposit:float, total:float, currency:string
     * }
     */
    public function quote(
        Vehicle $vehicle,
        Carbon $pickup,
        Carbon $return,
        ?RentalLocation $pickupLocation = null,
        ?RentalLocation $returnLocation = null,
        array $extraKeys = []
    ): array {
        $days = $this->billableDays($pickup, $return, $vehicle);
        $rate = $vehicle->rateForDays($days);

        $subtotal = round($rate['per_day'] * $days, 2);

        // Pickup fee is charged once; the one-way fee only applies when the car
        // is dropped at a different branch than it was collected from.
        $pickupFee = (float) ($pickupLocation->pickup_fee ?? 0);
        $oneWayFee = 0.0;

        if ($returnLocation && $pickupLocation && $returnLocation->id !== $pickupLocation->id) {
            $oneWayFee = (float) $returnLocation->one_way_fee;
        }

        $locationFee = round($pickupFee + $oneWayFee, 2);

        // Extras
        $catalogue    = $this->availableExtras();
        $extras       = [];
        $extrasTotal  = 0.0;

        foreach ($extraKeys as $key) {
            if (! isset($catalogue[$key])) {
                continue;
            }

            $extra = $catalogue[$key];
            $line  = round($extra['per_day'] ? $extra['price'] * $days : $extra['price'], 2);

            $extras[] = [
                'key'      => $key,
                'label'    => $extra['label'],
                'label_ar' => $extra['label_ar'],
                'unit'     => $extra['price'],
                'per_day'  => $extra['per_day'],
                'total'    => $line,
            ];

            $extrasTotal += $line;
        }

        $extrasTotal = round($extrasTotal, 2);

        // VAT applies to the rental and its fees, not to the refundable deposit.
        $taxable = round($subtotal + $locationFee + $extrasTotal, 2);
        $vatRate = $this->vatRate();
        $tax     = round($taxable * $vatRate, 2);

        $deposit = (float) $vehicle->security_deposit;
        $total   = round($taxable + $tax, 2);

        return [
            'days'             => $days,
            'rate_plan'        => $rate['plan'],
            'per_day'          => $rate['per_day'],
            'subtotal'         => $subtotal,
            'pickup_fee'       => $pickupFee,
            'one_way_fee'      => $oneWayFee,
            'location_fee'     => $locationFee,
            'extras'           => $extras,
            'extras_total'     => $extrasTotal,
            'taxable'          => $taxable,
            'vat_rate'         => $vatRate,
            'tax_amount'       => $tax,
            'security_deposit' => $deposit,
            'total'            => $total,
            'currency'         => $this->currency(),
        ];
    }
}
