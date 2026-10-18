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

            $leaveRequests = LeaveRequest::with(['user', 'approver'])
                ->whereIn('user_id', $allIds)
                ->latest()
                ->get();
        } else {
            // Jika Karyawan Biasa, hanya tampilkan pengajuan miliknya sendiri
            $leaveRequests = LeaveRequest::with(['user', 'approver'])
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
        // Memperluas enum type mencakup leave dan dispensation, serta opsi lampiran
        $validated = $request->validate([
            'type' => 'required|in:permission,sick,leave,dispensation',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
        }

        $request->user()->leaveRequests()->create([
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Pengajuan berhasil dikirim.');
    }

    public function approve(Request $request, $id)
    {
        // Proteksi otorisasi agar hanya atasan yang dapat menyetujui
        if (! $request->user()->can('approve leave request') && ! $request->user()->hasRole('Line Head')) {
            abort(403, 'Anda tidak memiliki hak akses untuk menyetujui pengajuan ini.');
        }

        $leaveRequest = LeaveRequest::findOrFail($id);

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
        ]);

        return redirect()->back()->with('success', 'Pengajuan berhasil disetujui.');
    }

    // Penambahan fungsi reject yang sebelumnya belum ada
    public function reject(Request $request, $id)
    {
        if (! $request->user()->can('approve leave request') && ! $request->user()->hasRole('Line Head')) {
            abort(403, 'Anda tidak memiliki hak akses untuk menolak pengajuan ini.');
        }

        $validated = $request->validate([
            'rejection_note' => 'required|string|max:255',
        ]);

        $leaveRequest = LeaveRequest::findOrFail($id);

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
            'rejection_note' => $validated['rejection_note'],
        ]);

        return redirect()->back()->with('success', 'Pengajuan berhasil ditolak.');
    }

    /**
     * Method tambahan: Khusus menampilkan menu Izin & Sakit
     */
    public function indexPermissionSick(Request $request)
    {
        return $this->renderFilteredLeaveView($request, ['permission', 'sick'], 'Izin & Sakit');
    }

    /**
     * Method tambahan: Khusus menampilkan menu Cuti & Dispensasi
     */
    public function indexLeaveDispensation(Request $request)
    {
        return $this->renderFilteredLeaveView($request, ['leave', 'dispensation'], 'Cuti & Dispensasi');
    }

    /**
     * Helper privat untuk memfilter data berdasarkan kategori menu
     */
    private function renderFilteredLeaveView(Request $request, array $types, string $pageTitle)
    {
        $user = $request->user();

        $query = LeaveRequest::with(['user', 'approver'])
            ->whereIn('type', $types)
            ->latest();

        if ($user->can('approve leave request') || $user->hasRole('Line Head')) {
            $teamUserIds = User::where('line_head_id', $user->id)->pluck('id');
            $allIds = $teamUserIds->push($user->id);
            $query->whereIn('user_id', $allIds);
        } else {
            $query->where('user_id', $user->id);
        }

        return Inertia::render('Leave/Index', [
            'leaveRequests' => $query->get(),
            'pageTitle' => $pageTitle,
            'allowedTypes' => $types,
            'isApprover' => $user->can('approve leave request') || $user->hasRole('Line Head'),
        ]);
    }
}
