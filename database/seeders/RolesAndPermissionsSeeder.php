<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // 🛠️ Créer les rôles
        $admin = Role::create(['name' => 'admin']);
        $user = Role::create(['name' => 'user']);

        // 📜 Créer les permissions
        $permissions = [
            // Utilisateurs
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
            'manage role and permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // 👑 Donner toutes les permissions à l'admin
        $admin->givePermissionTo(Permission::all());

        // 👤 Donner uniquement les permissions utilisateur standard
        $user->givePermissionTo([
            'create contacts',
            'edit contacts',
            'view contacts',
            'import contacts',
            'export contacts',
            'create interactions',
            'view interactions',
            'schedule interactions',
        ]);
    }
}
