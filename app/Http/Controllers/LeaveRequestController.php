<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Mengambil pengajuan sesuai role
        if ($user->hasRole('Line Head')) {
            $leaves = LeaveRequest::with('user')
                ->whereHas('user', function ($q) use ($user) {
                    $q->where('line_head_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $leaves = LeaveRequest::with('user')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return Inertia::render('Leave/Index', [
            'leaves' => $leaves ?? [],
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

        $request->user()->leaveRequests()->create($validated);

        return back()->with('success', 'Pengajuan berhasil dikirim.');
    }

    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_note' => 'nullable|string',
        ]);

        $leave = LeaveRequest::findOrFail($id);
        $leave->update([
            'status' => $validated['status'],
            'rejection_note' => $validated['rejection_note'] ?? null,
            'approved_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
