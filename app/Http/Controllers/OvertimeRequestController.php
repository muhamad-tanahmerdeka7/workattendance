<?php

namespace App\Http\Controllers;

use App\Models\OvertimeRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OvertimeRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('Line Head')) {
            $overtimes = OvertimeRequest::with('user')
                ->whereHas('user', function ($q) use ($user) {
                    $q->where('line_head_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $overtimes = OvertimeRequest::with('user')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return Inertia::render('Overtime/Index', [
            'overtimes' => $overtimes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'reason' => 'required|string',
        ]);

        $request->user()->overtimeRequests()->create($validated);

        return back()->with('success', 'Pengajuan lembur berhasil dikirim.');
    }

    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_note' => 'nullable|string',
        ]);

        $overtime = OvertimeRequest::findOrFail($id);
        $overtime->update([
            'status' => $validated['status'],
            'rejection_note' => $validated['rejection_note'] ?? null,
            'approved_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Status lembur berhasil diperbarui.');
    }
}
