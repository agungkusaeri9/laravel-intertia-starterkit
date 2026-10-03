<?php

namespace App\Services;

use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

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

    public function getRoles(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->roleRepository->getAllPaginated($filters, $perPage);
    }

    public function getRoleById(int $id): ?Role
    {
        return $this->roleRepository->findById($id);
    }

    public function createRole(array $data, array $permissions = []): Role
    {
        return DB::transaction(function () use ($data, $permissions) {
            $role = $this->roleRepository->create($data);

            if (! $role) {
                throw new \Exception('Gagal menambahkan data peran ke database.');
            }

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
            $existingRole = $this->roleRepository->findById($role->id);
            if (! $existingRole) {
                throw new \Exception('Data peran tidak ditemukan di sistem.');
            }

            $updated = $this->roleRepository->update($existingRole, $data);
            if (! $updated) {
                throw new \Exception('Gagal memperbarui data peran ke database.');
            }

            $this->roleRepository->syncPermissions($existingRole, $permissions);

            $this->activityLogService->log(
                'Update Role',
                "Updated role: {$existingRole->name}",
                ['role_id' => $existingRole->id, 'permissions' => $permissions]
            );

            return true;
        });
    }

    public function deleteRole(Role $role): bool
    {
        return DB::transaction(function () use ($role) {
            $existingRole = $this->roleRepository->findById($role->id);
            if (! $existingRole) {
                throw new \Exception('Data peran tidak ditemukan di sistem.');
            }

            if ($existingRole->name === 'super admin') {
                throw new \Exception('Peran super admin tidak dapat dihapus.');
            }

            $roleName = $existingRole->name;
            $roleId = $existingRole->id;

            $deleted = $this->roleRepository->delete($existingRole);
            if (! $deleted) {
                throw new \Exception('Gagal menghapus data peran dari database.');
            }

            $this->activityLogService->log(
                'Delete Role',
                "Deleted role: {$roleName}",
                ['deleted_role_id' => $roleId]
            );

            return true;
        });
    }
}
