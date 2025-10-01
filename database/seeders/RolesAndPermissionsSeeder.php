<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         // Créer les rôles
        $roles = ['admin', 'medecin', 'infirmier', 'patient'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'api']);
        }
        // (Optionnel) Créer des permissions
        $permissions = [
            'patient.view', 
            'patient.create',
            'observation.create', 
            'note.create', 
            'message.send',
            'prescription.create', // pour medecin seulement
            'user.manage',         // admin only
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'api']);
        }
        // Assigner les permissions aux rôles
        Role::findByName('admin', 'api')->givePermissionTo(Permission::all());
        Role::findByName('medecin', 'api')->givePermissionTo([
            'patient.view', 'patient.create', 'observation.create', 'note.create', 'message.send', 'prescription.create'
        ]);
        Role::findByName('infirmier', 'api')->givePermissionTo([
            'patient.view', 'observation.create', 'note.create', 'message.send'
        ]);
        Role::findByName('patient', 'api')->givePermissionTo([
            'patient.view'
        ]);
    }
}
