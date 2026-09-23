<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Jika Line Head, tampilkan semua pengajuan dari bawahannya (termasuk dirinya)
        if ($user->can('approve leave request') || $user->hasRole('Line Head')) {
            $teamUserIds = User::where('line_head_id', $user->id)->pluck('id');
            $allIds = $teamUserIds->push($user->id);

            $leaveRequests = LeaveRequest::with('user')
                ->whereIn('user_id', $allIds)
                ->latest()
                ->get();
        } else {
            // Jika Karyawan Biasa, hanya tampilkan pengajuan miliknya sendiri
            $leaveRequests = LeaveRequest::with('user')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return Inertia::render('Leave/Index', [
            'leaveRequests' => $leaveRequests,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:permission,sick',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $request->user()->leaveRequests()->create([
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('leave.index')->with('success', 'Pengajuan berhasil dikirim.');
    }

    public function approve(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
        ]);

        return redirect()->route('leave.index')->with('success', 'Pengajuan berhasil disetujui.');
    }
}
