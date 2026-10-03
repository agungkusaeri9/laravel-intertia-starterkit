<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;

interface PermissionRepositoryInterface
{
    public function getAll(): Collection;

    public function getAllPaginated(array $filters, int $perPage): LengthAwarePaginator;

    public function findById(int $id): ?Permission;
}
