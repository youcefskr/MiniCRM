<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin'),
            'email_verified_at' => now(), 
        ]);

        if (Role::where('name', 'admin')->exists()) {
            $admin->assignRole('admin');
        } else {
            $role = Role::create(['name' => 'admin']);
            $admin->assignRole($role);
        }
    }
}
