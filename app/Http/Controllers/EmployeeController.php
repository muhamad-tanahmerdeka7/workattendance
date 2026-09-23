<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->get();

        return Inertia::render('Employees/Index', [
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'employee_code' => 'required|string|unique:employees',
            'department' => 'required|string',
            'position' => 'required|string',
            'join_date' => 'required|date',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'line_head_id' => $request->user()->id,
        ]);

        $user->assignRole('Karyawan');

        $user->employee()->create([
            'employee_code' => $request->employee_code,
            'department' => $request->department,
            'position' => $request->position,
            'join_date' => $request->join_date,
        ]);

        return back()->with('success', 'Data karyawan berhasil ditambahkan.');
    }
}
