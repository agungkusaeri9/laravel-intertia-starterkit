<?php

namespace App\Services;

use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    public function __construct(
        private ActivityLogRepositoryInterface $activityLogRepository
    ) {}

    public function getLogs(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->activityLogRepository->getAllPaginated($filters, $perPage);
    }

    public function log(string $activity, ?string $description = null, ?array $data = null): void
    {
        $this->activityLogRepository->create([
            'user_id' => auth()->id(),
            'activity' => $activity,
            'description' => $description,
            'data' => $data,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
