<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Zone;
use App\Models\ZoneDeliverySchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Manages monthly delivery schedules per zone.
 *
 * Routes (add inside your admin group in web.php):
 *
 *   Route::get( 'zones/{zone}/schedules',           [ZoneScheduleController::class, 'index'])  ->name('zones.schedules.index');
 *   Route::get( 'zones/{zone}/schedules/create',    [ZoneScheduleController::class, 'create']) ->name('zones.schedules.create');
 *   Route::post('zones/{zone}/schedules',           [ZoneScheduleController::class, 'store'])  ->name('zones.schedules.store');
 *   Route::get( 'zones/{zone}/schedules/{schedule}/edit',   [ZoneScheduleController::class, 'edit'])   ->name('zones.schedules.edit');
 *   Route::put( 'zones/{zone}/schedules/{schedule}',        [ZoneScheduleController::class, 'update']) ->name('zones.schedules.update');
 *   Route::delete('zones/{zone}/schedules/{schedule}',      [ZoneScheduleController::class, 'destroy'])->name('zones.schedules.destroy');
 *   Route::post('zones/{zone}/schedules/{schedule}/duplicate', [ZoneScheduleController::class, 'duplicate'])->name('zones.schedules.duplicate');
 */
class ZoneScheduleController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Zone $zone): View
    {
        $zone->load('country');
        $schedules    = $zone->deliverySchedules()->with('zone')->get();
        $currentMonth = ZoneDeliverySchedule::currentMonthKey();
        $nextMonth    = ZoneDeliverySchedule::nextMonthKey();

        $hasCurrentMonth = $schedules->where('month', $currentMonth)->isNotEmpty();
        $hasNextMonth    = $schedules->where('month', $nextMonth)->isNotEmpty();

        return view('admin.zone-schedules.index', compact(
            'zone', 'schedules', 'currentMonth', 'nextMonth',
            'hasCurrentMonth', 'hasNextMonth'
        ));
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create(Zone $zone, Request $request): View
    {
        $zone->load('country');

        // Default to next month; allow overriding via ?month=YYYY-MM
        $defaultMonth = $request->input('month', ZoneDeliverySchedule::nextMonthKey());

        // If duplicating from a previous schedule
        $copyFrom = null;
        if ($request->filled('copy_from')) {
            $copyFrom = ZoneDeliverySchedule::find($request->input('copy_from'));
        }

        // Build list of months admin can choose (current + next 12)
        $monthOptions = $this->buildMonthOptions($zone);

        return view('admin.zone-schedules.create', compact(
            'zone', 'defaultMonth', 'copyFrom', 'monthOptions'
        ));
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(Zone $zone, Request $request): RedirectResponse
    {
        $data = $this->validateSchedule($request);
        $data['zone_id'] = $zone->id;

        ZoneDeliverySchedule::updateOrCreate(
            ['zone_id' => $zone->id, 'month' => $data['month']],
            $data
        );

        return redirect()
            ->route('admin.zones.schedules.index', $zone)
            ->with('success', 'تم حفظ جدول التوصيل لشهر ' . $this->monthLabel($data['month']) . '.');
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function edit(Zone $zone, ZoneDeliverySchedule $schedule): View
    {
        $zone->load('country');
        $monthOptions = $this->buildMonthOptions($zone, $schedule->month);

        return view('admin.zone-schedules.edit', compact('zone', 'schedule', 'monthOptions'));
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(Zone $zone, ZoneDeliverySchedule $schedule, Request $request): RedirectResponse
    {
        $data = $this->validateSchedule($request, $schedule);
        $schedule->update($data);

        return redirect()
            ->route('admin.zones.schedules.index', $zone)
            ->with('success', 'تم تحديث جدول شهر ' . $schedule->monthLabel() . '.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(Zone $zone, ZoneDeliverySchedule $schedule): RedirectResponse
    {
        $label = $schedule->monthLabel();
        $schedule->delete();

        return redirect()
            ->route('admin.zones.schedules.index', $zone)
            ->with('success', 'تم حذف جدول شهر ' . $label . '.');
    }

    // ── Duplicate ─────────────────────────────────────────────────────────────

    /**
     * Copies a schedule to the next available month.
     * Redirects to create form pre-filled with copied data.
     */
    public function duplicate(Zone $zone, ZoneDeliverySchedule $schedule): RedirectResponse
    {
        // Find next month that doesn't already have a schedule
        $targetMonth = ZoneDeliverySchedule::nextMonthKey();
        if ($zone->scheduleForMonth($targetMonth)) {
            $targetMonth = ZoneDeliverySchedule::nextMonthKey(2);
        }

        return redirect()->route('admin.zones.schedules.create', [
            'zone'      => $zone->id,
            'month'     => $targetMonth,
            'copy_from' => $schedule->id,
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function validateSchedule(Request $request, ?ZoneDeliverySchedule $existing = null): array
    {
        $data = $request->validate([
            'month'          => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'delivery_days'  => ['nullable', 'integer', 'min:1', 'max:365'],
            'available_days' => ['nullable', 'string'],  // comma-separated input
            'notes'          => ['nullable', 'string', 'max:255'],
            'is_active'      => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

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
    private function buildMonthOptions(Zone $zone, ?string $alwaysInclude = null): array
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

    private function monthLabel(string $month): string
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