<?php

namespace App\Repositories\Contracts;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?Role;
    public function create(array $data): Role;
    public function update(Role $role, array $data): bool;
    public function delete(Role $role): bool;
    public function syncPermissions(Role $role, array $permissions): void;
}
