<?php

namespace App\Services;

use App\Repositories\Contracts\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private ActivityLogService $activityLogService
    ) {}

    public function getAllRoles(): Collection
    {
        return $this->roleRepository->getAll();
    }

    public function getRoleById(int $id): ?Role
    {
        return $this->roleRepository->findById($id);
    }

    public function createRole(array $data, array $permissions = []): Role
    {
        return DB::transaction(function () use ($data, $permissions) {
            $role = $this->roleRepository->create($data);
            $this->roleRepository->syncPermissions($role, $permissions);

            $this->activityLogService->log(
                'Create Role',
                "Created a new role: {$role->name}",
                ['role_id' => $role->id, 'permissions' => $permissions]
            );

            return $role;
        });
    }

    public function updateRole(Role $role, array $data, array $permissions = []): bool
    {
        return DB::transaction(function () use ($role, $data, $permissions) {
            $updated = $this->roleRepository->update($role, $data);
            $this->roleRepository->syncPermissions($role, $permissions);

            if ($updated) {
                $this->activityLogService->log(
                    'Update Role',
                    "Updated role: {$role->name}",
                    ['role_id' => $role->id, 'permissions' => $permissions]
                );
            }

            return $updated;
        });
    }

    public function deleteRole(Role $role): bool
    {
        return DB::transaction(function () use ($role) {
            $roleName = $role->name;
            $roleId = $role->id;

            $deleted = $this->roleRepository->delete($role);

            if ($deleted) {
                $this->activityLogService->log(
                    'Delete Role',
                    "Deleted role: {$roleName}",
                    ['deleted_role_id' => $roleId]
                );
            }

            return $deleted;
        });
    }
}
