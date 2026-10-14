<?php

namespace App\Http\Controllers;

use App\Models\CaseRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the medical case management dashboard.
     */
    public function index(Request $request): View
    {
        // 5 mandatory statistic counts directly from real database queries
        $totalCases = CaseRecord::count();
        $planningCount = CaseRecord::where('status', CaseRecord::STATUS_PLANNING)->count();
        $inReviewCount = CaseRecord::where('status', CaseRecord::STATUS_IN_REVIEW)->count();
        $approvedCount = CaseRecord::where('status', CaseRecord::STATUS_APPROVED)->count();
        $completedCount = CaseRecord::where('status', CaseRecord::STATUS_COMPLETED)->count();

        // Additional status breakdown
        $draftCount = CaseRecord::where('status', CaseRecord::STATUS_DRAFT)->count();
        $cancelledCount = CaseRecord::where('status', CaseRecord::STATUS_CANCELLED)->count();

        // Recent cases list (top 6 newest)
        $recentCases = CaseRecord::with('creator')
            ->latest('id')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'totalCases',
            'planningCount',
            'inReviewCount',
            'approvedCount',
            'completedCount',
            'draftCount',
            'cancelledCount',
            'recentCases'
        ));
    }
}
