<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Receptionist']);
        Role::create(['name' => 'Nurse']);
        Role::create(['name' => 'Doctor']);
        Role::create(['name' => 'Patient']);
        Role::create(['name' => 'Inventory Manager']);



        $superAdmin = User::create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
        ]);
        $superAdmin->assignRole('Super Admin');

        $receptionist = User::create([
            'name' => 'Receptionist User',
            'email' => 'receptionist@example.com',
            'password' => bcrypt('password'),
        ]);
        $receptionist->assignRole('Receptionist');

        $nurse = User::create([
            'name' => 'Nurse User',
            'email' => 'nurse@example.com',
            'password' => bcrypt('password'),
        ]);
        $nurse->assignRole('Nurse');
    }
}
