<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
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

// Group Route Utama Aplikasi (Harus Login & Verifikasi)
Route::middleware(['auth', 'verified'])->group(function () {

    // ==========================================
    // FITUR BERSAMA (KARYAWAN & LINE HEAD)
    // ==========================================

    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view dashboard')
        ->name('dashboard');

    // 1. Modul Presensi Karyawan
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->middleware('permission:view own attendance')
        ->name('attendance.index');

    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])
        ->middleware('permission:create attendance')
        ->name('attendance.checkIn');

    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])
        ->middleware('permission:create attendance')
        ->name('attendance.checkOut');

    // 2. Modul Izin & Sakit (Pengajuan)
    Route::get('/leave', [LeaveRequestController::class, 'index'])
        ->middleware('permission:view leave request')
        ->name('leave.index');

    Route::post('/leave', [LeaveRequestController::class, 'store'])
        ->middleware('permission:create leave request')
        ->name('leave.store');

    // 3. Modul Lembur (Pengajuan)
    Route::get('/overtime', [OvertimeRequestController::class, 'index'])
        ->middleware('permission:view overtime request')
        ->name('overtime.index');

    Route::post('/overtime', [OvertimeRequestController::class, 'store'])
        ->middleware('permission:create overtime request')
        ->name('overtime.store');


    // ==========================================
    // FITUR KHUSUS (LINE HEAD ONLY)
    // ==========================================

    // Monitoring Kehadiran Seluruh Tim
    Route::get('/attendance/monitoring', [AttendanceController::class, 'monitoring'])
        ->middleware('permission:view all attendance')
        ->name('attendance.monitoring');

    // Persetujuan Izin & Lembur
    Route::post('/leave/{id}/approve', [LeaveRequestController::class, 'approve'])
        ->middleware('permission:approve leave request')
        ->name('leave.approve');

    Route::post('/overtime/{id}/approve', [OvertimeRequestController::class, 'approve'])
        ->middleware('permission:approve overtime request')
        ->name('overtime.approve');

    // Kelola Data Karyawan
    Route::resource('employees', EmployeeController::class)
        ->middleware('permission:manage employees');
});

// Group Route Profil bawaan Laravel Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
