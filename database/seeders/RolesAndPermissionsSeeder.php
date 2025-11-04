<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create roles safely (avoids duplicates)
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // List of permissions
        $permissions = [
            // Users
            'manage users',
            'view users',

            // Contacts
            'create contacts',
            'edit contacts',
            'delete contacts',
            'view contacts',
            'import contacts',
            'export contacts',

            // Interactions
            'create interactions',
            'view interactions',
            'edit interactions',
            'delete interactions',
            'schedule interactions',

            // Roles & Permissions
            'manage role and permissions',
        ];

        // Create permissions safely
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // Give all permissions to admin
        $admin->syncPermissions(Permission::all());

        // Give only specific permissions to user
        $userPermissions = [
            'create contacts',
            'edit contacts',
            'view contacts',
            'import contacts',
            'export contacts',
            'create interactions',
            'view interactions',
            'schedule interactions',
        ];

        $user->syncPermissions($userPermissions);
    }
}
