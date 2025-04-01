<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{

    public function run()
    {
        $guard = 'sanctum';

        // List of permissions
        $permissions = [
            'dashboard',
            'view categories',
            'create categories',
            'update categories',
            'delete categories',
            'view transactions',
            'create transactions',
            'update transactions',
            'delete transactions',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => strtolower($permission), 'guard_name' => $guard]);
        }

        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => $guard]);
        $finance = Role::firstOrCreate(['name' => 'finance', 'guard_name' => $guard]);

        // Assign permissions to admin
        $admin->syncPermissions(Permission::all());

        // Assign view-only permissions to staff and finance
        $viewPermissions = Permission::where('name', 'LIKE', 'view%')->get();
        $staff->syncPermissions($viewPermissions);
        $finance->syncPermissions($viewPermissions);
    }
}
