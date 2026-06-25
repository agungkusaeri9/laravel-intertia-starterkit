<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function __construct(
        private RoleService $roleService
    ) {}

    public function index()
    {
        return Inertia::render('roles/Index', [
            'roles' => $this->roleService->getAllRoles(),
        ]);
    }

    public function create()
    {
        return Inertia::render('roles/Create', [
            'permissions' => Permission::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'permissions' => ['array'],
        ]);

        $this->roleService->createRole(
            ['name' => $validated['name']],
            $validated['permissions'] ?? []
        );

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return Inertia::render('roles/Edit', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::all(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['array'],
        ]);

        $this->roleService->updateRole(
            $role,
            ['name' => $validated['name']],
            $validated['permissions'] ?? []
        );

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'super admin') {
            return back()->with('error', 'Cannot delete super admin role.');
        }

        $this->roleService->deleteRole($role);

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
