<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache permission Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Daftar seluruh permission aplikasi
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

        // 2. Role Karyawan (Hanya akses presensi, izin, lembur milik sendiri)
        $karyawanRole = Role::firstOrCreate(['name' => 'Karyawan']);
        $karyawanRole->syncPermissions([
            'view dashboard',         // Ditambahkan agar karyawan bisa akses dashboard utama
            'view own attendance',
            'create attendance',
            'create leave request',
            'view leave request',
            'create overtime request',
            'view overtime request',
        ]);

        // 3. Role Line Head (Akses seluruh fitur)
        $lineHeadRole = Role::firstOrCreate(['name' => 'Line Head']);
        $lineHeadRole->syncPermissions(Permission::all());

        // 4. Dummy Line Head
        $head = User::firstOrCreate(
            ['email' => 'head@example.com'],
            [
                'name' => 'Line Head Demo',
                'password' => bcrypt('password'),
            ]
        );
        $head->assignRole($lineHeadRole);

        // 5. Dummy Karyawan
        $karyawan = User::firstOrCreate(
            ['email' => 'karyawan@example.com'],
            [
                'name' => 'Karyawan Demo',
                'password' => bcrypt('password'),
                'line_head_id' => $head->id,
            ]
        );
        $karyawan->assignRole($karyawanRole);
    }
}
