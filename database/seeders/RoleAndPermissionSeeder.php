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
        // 1. Reset cache permission Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Daftar seluruh permission aplikasi
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
            'manage shifts',        // Mengatur master shift & plotting jadwal
            'manage roles',         // Mengatur role & permission
            'manage user accounts', // Membuat akun & menentukan hierarki line head
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 3. Role HR Manager / Super Admin (Akses Penuh Seluruh Sistem)
        $hrRole = Role::firstOrCreate(['name' => 'HR Manager']);
        $hrRole->syncPermissions(Permission::all());

        // 4. Role Line Head (Mengelola presensi, shift tim, dan approval bawahan)
        $lineHeadRole = Role::firstOrCreate(['name' => 'Line Head']);
        $lineHeadRole->syncPermissions([
            'view dashboard',
            'view attendance',
            'view own attendance',
            'create attendance',
            'view all attendance',
            'view leave request',
            'approve leave request',
            'view overtime request',
            'approve overtime request',
            'manage shifts',
        ]);

        // 5. Role Karyawan (Hanya akses mandiri)
        $karyawanRole = Role::firstOrCreate(['name' => 'Karyawan']);
        $karyawanRole->syncPermissions([
            'view dashboard',
            'view own attendance',
            'create attendance',
            'create leave request',
            'view leave request',
            'create overtime request',
            'view overtime request',
        ]);

        // 6. Akun HR Manager (Muhamad)
        $admin = User::updateOrCreate(
            ['email' => 'muhamad@gmail.com'],
            [
                'name' => 'Muhamad S.Kom',
                'password' => bcrypt('password'),
            ]
        );
        $admin->syncRoles([$hrRole]);

        // 7. Akun Dummy Line Head
        $head = User::updateOrCreate(
            ['email' => 'head@example.com'],
            [
                'name' => 'Line Head Demo',
                'password' => bcrypt('password'),
            ]
        );
        $head->syncRoles([$lineHeadRole]);

        // 8. Akun Dummy Karyawan
        $karyawan = User::updateOrCreate(
            ['email' => 'karyawan@example.com'],
            [
                'name' => 'Karyawan Demo',
                'password' => bcrypt('password'),
                'line_head_id' => $head->id,
            ]
        );
        $karyawan->syncRoles([$karyawanRole]);
    }
}