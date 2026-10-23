<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\CompanyLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = AttendanceLog::with('user')->latest('scanned_at');

        // Jika Line Head atau memiliki permission approval, tampilkan tim dan dirinya sendiri
        if ($user->can('approve leave request') || $user->hasRole('Line Head')) {
            $teamUserIds = User::where('line_head_id', $user->id)->pluck('id');
            $allIds = $teamUserIds->push($user->id);
            $query->whereIn('user_id', $allIds);
        } else {
            // Karyawan biasa hanya melihat riwayat log miliknya
            $query->where('user_id', $user->id);
        }

        // Filter tanggal jika dipilih
        if ($request->filled('date')) {
            $query->whereDate('scanned_at', $request->date);
        }

        // Filter sumber data: 'machine' (mesin fisik) atau 'gps' (ponsel/web)
        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        // Cek hak akses Manager (bisa lewat email spesifik, role Manager, atau role Admin)
        $isManager = ($user->email === 'muhamad@gmail.com') || $user->hasRole(['Manager', 'Admin']);

        // Ambil data titik lokasi kerja saat ini (atau buat default jika belum ada)
        $companyLocation = CompanyLocation::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Plant 1 / Kantor Utama',
                'latitude' => -6.3089120,
                'longitude' => 106.8784110,
                'radius_meters' => 50,
                'updated_by' => 'System',
            ]
        );

        return Inertia::render('AttendanceLog/Index', [
            'logs' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only(['date', 'source_type']),
            'isManager' => $isManager,
            'companyLocation' => $companyLocation,
        ]);
    }

    /**
     * Method untuk menyimpan titik lokasi dan radius baru oleh Manager
     */
    public function updateLocation(Request $request)
    {
        $user = $request->user();

        // Otorisasi ketat
        if ($user->email !== 'muhamad@gmail.com' && !$user->hasRole(['Manager', 'Admin'])) {
            abort(403, 'Hanya Manager yang dapat mengubah titik koordinat lokasi kerja.');
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'required|integer|min:10|max:1000',
        ]);

        $location = CompanyLocation::firstOrNew(['id' => 1]);
        $location->latitude = $validated['latitude'];
        $location->longitude = $validated['longitude'];
        $location->radius_meters = $validated['radius_meters'];
        $location->updated_by = $user->email;
        $location->save();

        return redirect()->back()->with('success', 'Titik lokasi kerja dan radius berhasil diperbarui.');
    }
}
