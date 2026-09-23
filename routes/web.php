<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController; // <-- DITAMBAHKAN
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Group Route untuk Dashboard dan Fitur Aplikasi (Harus Login & Verifikasi)
Route::middleware(['auth', 'verified'])->group(function () {

    // DIPERBAIKI: Mengarahkan ke DashboardController
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // 1. MODUL PRESENSI (ATTENDANCE)
    // ==========================================
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkIn');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkOut');
    Route::get('/attendance/monitoring', [AttendanceController::class, 'monitoring'])->name('attendance.monitoring');

    // ==========================================
    // 2. MODUL IZIN & SAKIT (LEAVE REQUESTS)
    // ==========================================
    Route::get('/leave', [LeaveRequestController::class, 'index'])->name('leave.index');
    Route::post('/leave', [LeaveRequestController::class, 'store'])->name('leave.store');
    Route::post('/leave/{id}/approve', [LeaveRequestController::class, 'approve'])->name('leave.approve');

    // ==========================================
    // 3. MODUL LEMBUR (OVERTIME REQUESTS)
    // ==========================================
    Route::get('/overtime', [OvertimeRequestController::class, 'index'])->name('overtime.index');
    Route::post('/overtime', [OvertimeRequestController::class, 'store'])->name('overtime.store');
    Route::post('/overtime/{id}/approve', [OvertimeRequestController::class, 'approve'])->name('overtime.approve');

    // ==========================================
    // 4. KELOLA KARYAWAN (LINE HEAD ONLY)
    // ==========================================
    Route::resource('employees', EmployeeController::class);
});

// Group Route Profil bawaan Laravel Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
