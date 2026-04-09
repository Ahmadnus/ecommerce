<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;

/**
 * DashboardController
 * Handles the main admin overview page and analytics.
 */
class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    /**
     * Main dashboard with KPI cards, recent activity, charts data.
     */
    public function index()
    {
        $stats   = $this->dashboardService->getStats();
        $charts  = $this->dashboardService->getChartData();
        $recent  = $this->dashboardService->getRecentActivity();

        return view('admin.dashboard.index', compact('stats', 'charts', 'recent'));
    }

    /**
     * Full analytics page (expandable for deeper reporting).
     */
    public function analytics()
    {
        $analytics = $this->dashboardService->getDetailedAnalytics();
        return view('admin.dashboard.analytics', compact('analytics'));
    }
}