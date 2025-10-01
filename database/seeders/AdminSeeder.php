<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $role = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'api',
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'surname' => 'Principal',
                'password' => Hash::make('Password123!'),
                'sexe' => 'homme',
                'telephone' => '0000000000'
            ]
        );

        $admin->assignRole($role);
        
        $this->command->info("✅ Premier admin créé !");
    }
}
