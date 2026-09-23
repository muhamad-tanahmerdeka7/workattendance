<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view dashboard',
            'view attendance',
            'create attendance',
            'update attendance',
            'view own attendance',
            'view all attendance',
            'create leave request',
            'view leave request',
            'approve leave request',
            'create overtime request',
            'view overtime request',
            'approve overtime request',
            'manage employees',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Role Karyawan
        $karyawanRole = Role::firstOrCreate(['name' => 'Karyawan']);
        $karyawanRole->syncPermissions([
            'view own attendance',
            'create attendance',
            'create leave request',
            'view leave request',
            'create overtime request',
            'view overtime request',
        ]);

        // Role Line Head
        $lineHeadRole = Role::firstOrCreate(['name' => 'Line Head']);
        $lineHeadRole->syncPermissions(Permission::all());

        // Dummy Line Head
        $head = User::factory()->create([
            'name' => 'Line Head Demo',
            'email' => 'head@example.com',
            'password' => bcrypt('password'),
        ]);
        $head->assignRole($lineHeadRole);

        // Dummy Karyawan
        $karyawan = User::factory()->create([
            'name' => 'Karyawan Demo',
            'email' => 'karyawan@example.com',
            'password' => bcrypt('password'),
            'line_head_id' => $head->id,
        ]);
        $karyawan->assignRole($karyawanRole);
    }
}
