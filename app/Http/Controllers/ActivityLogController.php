<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'per_page', 'user_id', 'start_date', 'end_date']);
        $logs = $this->activityLogService->getLogs($filters, (int) $request->input('per_page', 10));

        return Inertia::render('activity/Index', [
            'logs' => $logs,
            'filters' => $filters,
            'users' => User::select('id', 'name')->orderBy('name')->get(),
        ]);
    }
}
