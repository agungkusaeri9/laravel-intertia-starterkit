<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view activity logs',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create Super Admin role
        $superAdminRole = Role::findOrCreate('super admin');
        // No need to sync permissions for super admin if using Gate::before

        // Create Admin role and assign some permissions
        $adminRole = Role::findOrCreate('admin');
        $adminRole->syncPermissions([
            'view users',
            'create users',
            'edit users',
            'view activity logs',
        ]);

        // Assign super admin role to the first user
        $user = User::first();
        if ($user) {
            $user->assignRole($superAdminRole);
        }
    }
}
