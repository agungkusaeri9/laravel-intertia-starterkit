<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Inertia\Inertia;

class PermissionController extends Controller
{
    public function index()
    {
        return Inertia::render('permissions/Index', [
            'permissions' => Permission::all(),
        ]);
    }
}
