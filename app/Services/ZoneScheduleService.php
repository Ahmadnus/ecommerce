<?php

namespace App\Services;

use App\Models\Zone;
use App\Models\ZoneDeliverySchedule;

/**
 * ZoneScheduleService
 *
 * Business logic for the monthly delivery schedules per zone: index data,
 * month options, save/duplicate helpers, and input normalization.
 * Never returns views/redirects.
 */
class ZoneScheduleService
{
    /**
     * All data the index page needs for a zone.
     */
    public function getIndexData(Zone $zone): array
    {
        $zone->load('country');
        $schedules    = $zone->deliverySchedules()->with('zone')->get();
        $currentMonth = ZoneDeliverySchedule::currentMonthKey();
        $nextMonth    = ZoneDeliverySchedule::nextMonthKey();

        $hasCurrentMonth = $schedules->where('month', $currentMonth)->isNotEmpty();
        $hasNextMonth    = $schedules->where('month', $nextMonth)->isNotEmpty();

        return compact(
            'zone', 'schedules', 'currentMonth', 'nextMonth',
            'hasCurrentMonth', 'hasNextMonth'
        );
    }

    public function findSchedule(int $id): ?ZoneDeliverySchedule
    {
        return ZoneDeliverySchedule::find($id);
    }

    /**
     * Upsert the schedule for a zone/month.
     */
    public function save(Zone $zone, array $data): ZoneDeliverySchedule
    {
        $data['zone_id'] = $zone->id;

        return ZoneDeliverySchedule::updateOrCreate(
            ['zone_id' => $zone->id, 'month' => $data['month']],
            $data
        );
    }

    public function update(ZoneDeliverySchedule $schedule, array $data): ZoneDeliverySchedule
    {
        $schedule->update($data);

        return $schedule;
    }

    public function delete(ZoneDeliverySchedule $schedule): void
    {
        $schedule->delete();
    }

    /**
     * Next month key that doesn't already have a schedule for the zone
     * (checks next month, then the month after).
     */
    public function nextAvailableMonth(Zone $zone): string
    {
        $targetMonth = ZoneDeliverySchedule::nextMonthKey();
        if ($zone->scheduleForMonth($targetMonth)) {
            $targetMonth = ZoneDeliverySchedule::nextMonthKey(2);
        }

        return $targetMonth;
    }

    /**
     * Normalize validated schedule input: is_active default and
     * "available_days" comma-separated string → clean integer array.
     */
    public function normalizeScheduleData(array $data, bool $isActive): array
    {
        $data['is_active'] = $isActive;

        // Parse "available_days" from a comma-separated string to a clean integer array
        if (! empty($data['available_days'])) {
            $days = array_filter(
                array_map('intval', explode(',', $data['available_days'])),
                fn ($d) => $d >= 1 && $d <= 31
            );
            $days = array_unique($days);
            sort($days);
            $data['available_days'] = array_values($days) ?: null;
        } else {
            $data['available_days'] = null;
        }

        return $data;
    }

    /**
     * Builds the list of months available for selection in the form.
     * Always includes the current month and the next 12.
     * Also includes the existing month when editing.
     */
    public function buildMonthOptions(Zone $zone, ?string $alwaysInclude = null): array
    {
        $existing = $zone->deliverySchedules->pluck('month')->toArray();
        $options  = [];

        for ($i = 0; $i <= 12; $i++) {
            $key   = now()->addMonths($i)->format('Y-m');
            $label = $this->monthLabel($key);
            $taken = in_array($key, $existing);

            $options[$key] = [
                'label' => $label,
                'taken' => $taken && $key !== $alwaysInclude,
            ];
        }

        // Always include the currently-edited month even if it's in the past
        if ($alwaysInclude && ! isset($options[$alwaysInclude])) {
            $options[$alwaysInclude] = [
                'label' => $this->monthLabel($alwaysInclude),
                'taken' => false,
            ];
        }

        return $options;
    }

    public function monthLabel(string $month): string
    {
        $months = [
            '01' => 'يناير', '02' => 'فبراير', '03' => 'مارس',
            '04' => 'أبريل', '05' => 'مايو',   '06' => 'يونيو',
            '07' => 'يوليو', '08' => 'أغسطس',  '09' => 'سبتمبر',
            '10' => 'أكتوبر','11' => 'نوفمبر', '12' => 'ديسمبر',
        ];
        [$year, $m] = explode('-', $month);
        return ($months[$m] ?? $m) . ' ' . $year;
    }
}
