<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['descripcion' => 'Administrador del sistema']
        );

        Role::firstOrCreate(
            ['name' => 'usuario', 'guard_name' => 'web'],
            ['descripcion' => 'Usuario regular']
        );
    }
}