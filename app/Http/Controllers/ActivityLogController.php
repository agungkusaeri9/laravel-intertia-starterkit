<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityLog\IndexActivityLogRequest;
use App\Services\ActivityLogService;
use App\Services\UserService;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function __construct(
        private ActivityLogService $activityLogService,
        private UserService $userService
    ) {}

    public function index(IndexActivityLogRequest $request)
    {
        $logs = $this->activityLogService->getLogs($request->filters(), $request->perPage());

        return Inertia::render('activity/Index', [
            'logs' => $logs,
            'filters' => $request->filters(),
            'users' => $this->userService->getUserOptions(),
        ]);
    }
}
