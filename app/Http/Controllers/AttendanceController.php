<?php

namespace App\Http\Controllers;

use App\Models\Attendance;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        return Inertia::render('Attendance/Index', [
            'todayAttendance' => $todayAttendance,
            'attendances' => $attendances,
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
        $teamAttendances = Attendance::with('user')
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