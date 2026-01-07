<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Display the activity log list view
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax() || $request->wantsJson()) {
            $activities = $this->activityLogService->getActivityLogs($request);

            $formattedData = $activities->getCollection()->map(function ($activity) {
                return $this->activityLogService->formatActivityForDisplay($activity);
            });

            return response()->json([
                'data' => $formattedData,
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
                'from' => $activities->firstItem(),
                'to' => $activities->lastItem(),
            ]);
        }

        $users = $this->activityLogService->getUsersWithActivity();
        $logNames = $this->activityLogService->getLogNames();

        return view('pages.admin.activity-log', [
            'users' => $users,
            'logNames' => $logNames,
        ]);
    }
}
