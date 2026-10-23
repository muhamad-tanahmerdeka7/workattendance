<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\CompanySetting; // Atau model pengaturan koordinat kantor yang Anda gunakan
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

        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        $myShift = EmployeeShift::with('shift')
            ->where('user_id', $user->id)
            ->where('date', $today)
            ->first();

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
            'myShift' => $myShift,
        ]);
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $user = $request->user();
        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->toTimeString();

        // 1. Validasi status keterlambatan
        $status = Carbon::now()->gt(Carbon::createFromTimeString('08:00:00')) ? 'late' : 'present';

        // 2. Simpan atau perbarui rekap absensi harian
        Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            [
                'time_in' => $currentTime,
                'status' => $status,
                'notes' => $request->notes,
            ]
        );

        // 3. Catat rekaman mentah ke AttendanceLog jika koordinat dikirim
        $this->logAttendance($user->id, 'IN', $request);

        return back()->with('success', 'Berhasil melakukan absen masuk.');
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $user = $request->user();
        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->toTimeString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->firstOrFail();

        $attendance->update([
            'time_out' => $currentTime,
        ]);

        // Catat rekaman mentah ke AttendanceLog
        $this->logAttendance($user->id, 'OUT', $request);

        return back()->with('success', 'Berhasil melakukan absen pulang.');
    }

    public function monitoring(Request $request)
    {
        $user = $request->user();

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

    /**
     * Helper untuk mencatat log GPS dan menghitung radius geofence
     */
    private function logAttendance($userId, $type, Request $request)
    {
        $lat = $request->latitude;
        $lng = $request->longitude;
        $distance = null;
        $statusRadius = null;

        // Ambil konfigurasi lokasi kantor dari database
        // Sesuaikan dengan model/setting yang Anda pakai (misal: -6.312356, 106.881287, radius 50m)
        $officeLat = -6.312356;
        $officeLng = 106.881287;
        $maxRadius = 50; // dalam meter

        if ($lat && $lng) {
            $distance = $this->calculateHaversineDistance($officeLat, $officeLng, $lat, $lng);
            $statusRadius = ($distance <= $maxRadius) ? 'Di Dalam Radius' : 'Luar Radius';
        }

        AttendanceLog::create([
            'user_id' => $userId,
            'attendance_time' => Carbon::now(),
            'type' => $type,
            'source' => 'GPS Mobile',
            'device' => $request->header('User-Agent', 'Web Browser'),
            'latitude' => $lat,
            'longitude' => $lng,
            'distance' => $distance ? round($distance) : null,
            'status' => $statusRadius,
        ]);
    }

    /**
     * Menghitung jarak 2 koordinat (dalam satuan meter)
     */
    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
