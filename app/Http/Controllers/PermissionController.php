<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\IndexPermissionRequest;
use App\Services\PermissionService;
use Inertia\Inertia;

class PermissionController extends Controller
{
    public function __construct(
        private PermissionService $permissionService
    ) {}

    public function index(IndexPermissionRequest $request)
    {
        $permissions = $this->permissionService->getPermissions(
            $request->filters(),
            $request->perPage()
        );

        return Inertia::render('permissions/Index', [
            'permissions' => $permissions,
            'filters' => $request->filters(),
        ]);
    }
}
