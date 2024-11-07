<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $hubAdmin = Role::create(['name' => 'Hub Admin']);
        $spokeAdmin = Role::create(['name' => 'Spoke Admin']);
        $spokeUser = Role::create(['name' => 'Spoke User']);

        // Create permissions
        Permission::create(['name' => 'manage hub']);
        Permission::create(['name' => 'manage spoke']);
        Permission::create(['name' => 'manage inventory']);
        Permission::create(['name' => 'view reports']);

        // Assign permissions to roles
        $hubAdmin->givePermissionTo(['manage hub', 'manage spoke', 'view reports']);
        $spokeAdmin->givePermissionTo(['manage spoke', 'manage inventory']);
        $spokeUser->givePermissionTo('view reports');
    }
}
