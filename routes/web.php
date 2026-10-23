<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceLogController; // <-- TAMBAHAN UNTUK LOG MESIN & GPS
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\ShiftController; // <-- TAMBAHAN UNTUK MODUL SHIFT KERJA
use Illuminate\Support\Facades\Route;

// Mengarahkan domain utama langsung ke halaman Login
Route::get('/', function () {
    return redirect()->route('login');
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
    Route::get('/leave', [LeaveRequestController::class, 'indexPermissionSick'])
        ->middleware('permission:view leave request')
        ->name('leave.index');

    // 2b. Modul Cuti & Dispensasi (Menu Baru)
    Route::get('/leave-dispensation', [LeaveRequestController::class, 'indexLeaveDispensation'])
        ->middleware('permission:view leave request')
        ->name('leave.dispensation.index');

    Route::post('/leave', [LeaveRequestController::class, 'store'])
        ->middleware('permission:create leave request')
        ->name('leave.store');

    // 2c. Modul Log Mesin & GPS (Fitur Baru - Otentikasi Terbuka untuk Semua Pengguna Login)
    Route::get('/attendance-logs', [AttendanceLogController::class, 'index'])
        ->name('attendance.logs.index');

    // 3. Modul Lembur (Pengajuan)
    Route::get('/overtime', [OvertimeRequestController::class, 'index'])
        ->middleware('permission:view overtime request')
        ->name('overtime.index');

    Route::post('/overtime', [OvertimeRequestController::class, 'store'])
        ->middleware('permission:create overtime request')
        ->name('overtime.store');

    // ==========================================
    // MODUL GAJI & KOMPLAIN KARYAWAN
    // ==========================================
    Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries.index');
    Route::post('/salaries', [SalaryController::class, 'store'])->name('salaries.store');
    Route::delete('/salaries/{salary}', [SalaryController::class, 'destroy'])->name('salaries.destroy');
    Route::post('/salaries/{salary}/complain', [SalaryController::class, 'complain'])->name('salaries.complain');

    // ==========================================
    // FITUR KHUSUS (LINE HEAD ONLY)
    // ==========================================

    // Monitoring Kehadiran Seluruh Tim
    Route::get('/attendance/monitoring', [AttendanceController::class, 'monitoring'])
        ->middleware('permission:view all attendance')
        ->name('attendance.monitoring');

    // Persetujuan Izin, Cuti & Lembur
    Route::post('/leave/{id}/approve', [LeaveRequestController::class, 'approve'])
        ->middleware('permission:approve leave request')
        ->name('leave.approve');

    // Penolakan Izin & Cuti (Reject)
    Route::post('/leave/{id}/reject', [LeaveRequestController::class, 'reject'])
        ->middleware('permission:approve leave request')
        ->name('leave.reject');

    Route::post('/overtime/{id}/approve', [OvertimeRequestController::class, 'approve'])
        ->middleware('permission:approve overtime request')
        ->name('overtime.approve');

    // Kelola Data Karyawan
    Route::resource('employees', EmployeeController::class)
        ->middleware('permission:manage employees');

    // Manajemen Shift Kerja (Drag & Drop)
    Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::post('/shifts/assign', [ShiftController::class, 'assign'])->name('shifts.assign');

    // -- RUTE EKSPOR LAPORAN REKAP SHIFT BULANAN --
    Route::get('/shifts/export', [ShiftController::class, 'export'])->name('shifts.export');
    Route::get('/shifts/export-pdf', [ShiftController::class, 'exportPdf'])->name('shifts.exportPdf');

    // -- RUTE MATRIKS JADWAL TIM & EKSPORNYA (EXCEL & PDF) --
    Route::get('/shifts/schedule', [ShiftController::class, 'schedule'])->name('shifts.schedule');
    Route::get('/shifts/schedule/export', [ShiftController::class, 'exportSchedule'])->name('shifts.schedule.export');
    Route::get('/shifts/schedule/export-pdf', [ShiftController::class, 'exportSchedulePdf'])->name('shifts.schedule.exportPdf');
});

// Group Route Profil bawaan Laravel Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';