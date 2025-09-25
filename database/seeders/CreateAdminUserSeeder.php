<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Check or create the admin user
        $user = User::firstOrCreate(
            ['email' => 'admin1@gmail.com'],
            ['name' => 'Admin', 'password' => bcrypt('12345678')]
        );

        // Check or create the role
        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

        // Assign all permissions to the role
        $permissions = Permission::pluck('id', 'id')->all();
        $role->syncPermissions($permissions);

        // Assign the role to the user
        $user->assignRole($role->name);
    }
}
