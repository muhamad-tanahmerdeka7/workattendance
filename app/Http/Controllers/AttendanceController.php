<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\EmployeeShift;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Diperbarui agar riwayat presensi pribadi urut dari tanggal terbaru ke terlama
        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        // TAMBAHAN: Mengambil data shift/jadwal libur karyawan yang sedang login untuk hari ini
        $myShift = EmployeeShift::with('shift')
            ->where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Khusus HR Manager: otomatis menggunakan Shift Pagi default jika belum di-plot
        if (!$myShift && $user->hasRole('HR Manager')) {
            $morningShift = Shift::where('name', 'Pagi')->first() ?? (object) [
                'name' => 'Pagi',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'is_flexible' => false,
            ];

            $myShift = (object) [
                'shift' => $morningShift,
                'is_off' => false,
            ];
        }

        return Inertia::render('Attendance/Index', [
            'todayAttendance' => $todayAttendance,
            'attendances' => $attendances,
            'myShift' => $myShift, // <-- Dikirim ke halaman Vue karyawan
        ]);
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->toTimeString();

        // Anggap jam masuk standar pukul 08:00:00
        $status = Carbon::now()->gt(Carbon::createFromTimeString('08:00:00')) ? 'late' : 'present';

        Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            [
                'time_in' => $currentTime,
                'status' => $status,
                'notes' => $request->notes,
            ]
        );

        return back()->with('success', 'Berhasil melakukan absen masuk.');
    }

    public function checkOut(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->toTimeString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->firstOrFail();

        $attendance->update([
            'time_out' => $currentTime,
        ]);

        return back()->with('success', 'Berhasil melakukan absen pulang.');
    }

    public function monitoring(Request $request)
    {
        $user = $request->user();

        // Line Head melihat presensi bawahan
        // Menambahkan 'user.employee' agar relasi ke tabel Employee (NIK) ikut dipanggil
        $teamAttendances = Attendance::with('user.employee')
            ->whereHas('user', function ($query) use ($user) {
                $query->where('line_head_id', $user->id);
            })
            ->orderBy('date', 'desc')
            ->get();

        return Inertia::render('Attendance/Monitoring', [
            'teamAttendances' => $teamAttendances,
        ]);
    }
}
