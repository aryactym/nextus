<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage pages',
            'manage categories',
            'manage courses',
            'manage payments',
            'manage settings',
            'manage sponsors',
            'manage users',
            'manage blog',
            'manage org learners',
            'view org reports',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Berikan permission ke role non-super admin sesuai deskripsi.
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web'])
            ->givePermissionTo($permissions);

        Role::firstOrCreate(['name' => 'Instructor', 'guard_name' => 'web'])
            ->givePermissionTo(['manage courses']);

        Role::firstOrCreate(['name' => 'Org Admin', 'guard_name' => 'web'])
            ->givePermissionTo(['manage org learners', 'view org reports']);
    }
}
