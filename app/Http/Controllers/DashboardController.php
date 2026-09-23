<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();

        // Presensi pribadi pengguna yang sedang login hari ini
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Cek apakah user memiliki peran Line Head / permission melihat monitoring tim
        if ($user->can('view all attendance') || $user->hasRole('Line Head')) {
            // Ambil seluruh ID karyawan yang menjadi bawahan Line Head ini (termasuk dirinya)
            $teamUserIds = User::where('line_head_id', $user->id)->pluck('id');
            $allIds = $teamUserIds->push($user->id);

            // Statistik untuk Line Head (Menghitung seluruh tim hari ini)
            $stats = [
                // Total Hadir menghitung yang tepat waktu (present) DAN terlambat (late)
                'total_present' => Attendance::whereIn('user_id', $allIds)
                    ->where('date', $today)
                    ->whereIn('status', ['present', 'late'])
                    ->count(),

                // Rincian total yang terlambat hari ini
                'total_late' => Attendance::whereIn('user_id', $allIds)
                    ->where('date', $today)
                    ->where('status', 'late')
                    ->count(),

                // Total Izin/Sakit yang disetujui hari ini
                'total_leave' => LeaveRequest::whereIn('user_id', $allIds)
                    ->where('status', 'approved')
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today)
                    ->count(),
            ];
        } else {
            // Statistik untuk Karyawan Biasa (Menghitung total histori milik sendiri)
            $stats = [
                'total_present' => Attendance::where('user_id', $user->id)
                    ->whereIn('status', ['present', 'late'])
                    ->count(),

                'total_late' => Attendance::where('user_id', $user->id)
                    ->where('status', 'late')
                    ->count(),

                'total_leave' => LeaveRequest::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->count(),
            ];
        }

        return Inertia::render('Dashboard', [
            'todayAttendance' => $todayAttendance,
            'stats' => $stats,
        ]);
    }
}
