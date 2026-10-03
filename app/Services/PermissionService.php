<?php

namespace App\Services;

use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function __construct(
        private PermissionRepositoryInterface $permissionRepository
    ) {}

    public function getAllPermissions(): Collection
    {
        return $this->permissionRepository->getAll();
    }

    public function getPermissions(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->permissionRepository->getAllPaginated($filters, $perPage);
    }

    public function getPermissionById(int $id): ?Permission
    {
        return $this->permissionRepository->findById($id);
    }
}
