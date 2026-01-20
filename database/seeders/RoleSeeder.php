<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole  = Role::firstOrCreate(['name' => 'admin']);
        $lawyerRole = Role::firstOrCreate(['name' => 'lawyer']);
        $clientRole = Role::firstOrCreate(['name' => 'client']);

        // admin default
        $admin = User::firstOrCreate(
            ['email' => 'admin@lawyer.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        if (!$admin->hasRole('admin')) $admin->assignRole($adminRole);
    }
}
