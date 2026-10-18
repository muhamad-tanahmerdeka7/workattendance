<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['user.roles'])->get();

        return Inertia::render('Employees/Index', [
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:6',
            'role'          => 'required|string|in:HR Manager,Line Head,Karyawan',
            'employee_code' => 'required|string|unique:employees,employee_code',
            'department'    => 'required|string',
            'position'      => 'required|string',
            'join_date'     => 'required|date',
        ]);

        // Reset cache permission Spatie agar hak akses akun langsung terbaca sistem
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Hanya role Karyawan yang dihubungkan ke Line Head / Atasan langsung
        $lineHeadId = ($request->role === 'Karyawan') ? $request->user()->id : null;

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'line_head_id' => $lineHeadId,
        ]);

        // Berikan role sesuai pilihan dropdown
        $user->assignRole($request->role);

        // Jika HR Manager, berikan seluruh permission aplikasi secara penuh
        if ($request->role === 'HR Manager') {
            $user->syncPermissions(Permission::all());
        }

        $user->employee()->create([
            'employee_code' => $request->employee_code,
            'department'    => $request->department,
            'position'      => $request->position,
            'join_date'     => $request->join_date,
        ]);

        return back()->with('success', 'Data karyawan dan hak akses berhasil ditambahkan.');
    }
}
