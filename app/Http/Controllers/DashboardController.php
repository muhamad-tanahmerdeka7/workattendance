<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = now()->format('Y-m-d');

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $stats = [
            'total_present' => Attendance::where('user_id', $user->id)->where('status', 'present')->count(),
            'total_late' => Attendance::where('user_id', $user->id)->where('status', 'late')->count(),
            'total_leave' => LeaveRequest::where('user_id', $user->id)->where('status', 'approved')->count(),
        ];

        return Inertia::render('Dashboard', [
            'todayAttendance' => $todayAttendance,
            'stats' => $stats,
        ]);
    }
}
