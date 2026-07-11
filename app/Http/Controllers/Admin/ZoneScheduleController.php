<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\ZoneDeliverySchedule;
use App\Services\ZoneScheduleService;
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
    public function __construct(
        private readonly ZoneScheduleService $schedules,
    ) {}

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Zone $zone): View
    {
        return view('admin.zone-schedules.index', $this->schedules->getIndexData($zone));
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
            $copyFrom = $this->schedules->findSchedule((int) $request->input('copy_from'));
        }

        // Build list of months admin can choose (current + next 12)
        $monthOptions = $this->schedules->buildMonthOptions($zone);

        return view('admin.zone-schedules.create', compact(
            'zone', 'defaultMonth', 'copyFrom', 'monthOptions'
        ));
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(Zone $zone, Request $request): RedirectResponse
    {
        $data = $this->validateSchedule($request);

        $this->schedules->save($zone, $data);

        return redirect()
            ->route('admin.zones.schedules.index', $zone)
            ->with('success', 'تم حفظ جدول التوصيل لشهر ' . $this->schedules->monthLabel($data['month']) . '.');
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function edit(Zone $zone, ZoneDeliverySchedule $schedule): View
    {
        $zone->load('country');
        $monthOptions = $this->schedules->buildMonthOptions($zone, $schedule->month);

        return view('admin.zone-schedules.edit', compact('zone', 'schedule', 'monthOptions'));
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(Zone $zone, ZoneDeliverySchedule $schedule, Request $request): RedirectResponse
    {
        $data = $this->validateSchedule($request, $schedule);
        $this->schedules->update($schedule, $data);

        return redirect()
            ->route('admin.zones.schedules.index', $zone)
            ->with('success', 'تم تحديث جدول شهر ' . $schedule->monthLabel() . '.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(Zone $zone, ZoneDeliverySchedule $schedule): RedirectResponse
    {
        $label = $schedule->monthLabel();
        $this->schedules->delete($schedule);

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
        return redirect()->route('admin.zones.schedules.create', [
            'zone'      => $zone->id,
            'month'     => $this->schedules->nextAvailableMonth($zone),
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

        return $this->schedules->normalizeScheduleData(
            $data,
            $request->boolean('is_active', true)
        );
    }
}
