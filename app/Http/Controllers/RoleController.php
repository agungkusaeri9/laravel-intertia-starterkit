<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\IndexRoleRequest;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Services\PermissionService;
use App\Services\RoleService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        private RoleService $roleService,
        private PermissionService $permissionService
    ) {}

    public function index(IndexRoleRequest $request)
    {
        $roles = $this->roleService->getRoles($request->filters(), $request->perPage());

        return Inertia::render('roles/Index', [
            'roles' => $roles,
            'filters' => $request->filters(),
        ]);
    }

    public function create()
    {
        return Inertia::render('roles/Create', [
            'permissions' => $this->permissionService->getAllPermissions(),
        ]);
    }

    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->roleService->createRole(
                ['name' => $validated['name']],
                $validated['permissions'] ?? []
            );

            return redirect()->route('roles.index')->with('success', 'Peran baru berhasil ditambahkan.');
        } catch (\Throwable $e) {
            Log::error('Gagal menambahkan peran: '.$e->getMessage(), ['exception' => $e]);

            return back()->with('error', 'Gagal menambahkan peran: '.$e->getMessage())->withInput();
        }
    }

    public function edit(Role $role)
    {
        return Inertia::render('roles/Edit', [
            'role' => $role->load('permissions'),
            'permissions' => $this->permissionService->getAllPermissions(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $validated = $request->validated();

        try {
            $this->roleService->updateRole(
                $role,
                ['name' => $validated['name']],
                $validated['permissions'] ?? []
            );

            return redirect()->route('roles.index')->with('success', 'Data peran berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('Gagal memperbarui peran: '.$e->getMessage(), ['exception' => $e]);

            return back()->with('error', 'Gagal memperbarui peran: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(Role $role)
    {
        try {
            if ($role->name === 'super admin') {
                return back()->with('error', 'Peran super admin tidak dapat dihapus.');
            }

            $this->roleService->deleteRole($role);

            return redirect()->route('roles.index')->with('success', 'Peran berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus peran: '.$e->getMessage(), ['exception' => $e]);

            return back()->with('error', 'Gagal menghapus peran: '.$e->getMessage());
        }
    }
}
