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

        // 1. Cek hak akses HR Manager / Super Admin
        $isHrAdmin = ($user->email === 'muhamad@gmail.com')
            || $user->hasRole(['HR Manager', 'Super Admin', 'Admin'])
            || $user->can('manage employees');

        $query = LeaveRequest::with(['user', 'approver'])->latest();

        if ($isHrAdmin) {
            // HR Manager / Admin melihat SELURUH pengajuan dari semua karyawan
        } elseif ($user->hasRole('Line Head') || $user->can('approve leave request')) {
            // Line Head hanya melihat bawahannya dan pengajuan miliknya sendiri
            $teamUserIds = User::where('line_head_id', $user->id)->pluck('id');
            $allIds = $teamUserIds->push($user->id);
            $query->whereIn('user_id', $allIds);
        } else {
            // Karyawan biasa hanya melihat pengajuan miliknya sendiri
            $query->where('user_id', $user->id);
        }

        return Inertia::render('Leave/Index', [
            'leaveRequests' => $query->get(),
            'isApprover' => $isHrAdmin || $user->hasRole('Line Head') || $user->can('approve leave request'),
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
        $user = $request->user();

        $isHrAdmin = ($user->email === 'muhamad@gmail.com')
            || $user->hasRole(['HR Manager', 'Super Admin', 'Admin'])
            || $user->can('manage employees');

        // Proteksi otorisasi agar hanya atasan atau HR Manager yang dapat menyetujui
        if (! $isHrAdmin && ! $user->can('approve leave request') && ! $user->hasRole('Line Head')) {
            abort(403, 'Anda tidak memiliki hak akses untuk menyetujui pengajuan ini.');
        }

        $leaveRequest = LeaveRequest::findOrFail($id);

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => $user->id,
        ]);

        return redirect()->back()->with('success', 'Pengajuan berhasil disetujui.');
    }

    // Fungsi reject
    public function reject(Request $request, $id)
    {
        $user = $request->user();

        $isHrAdmin = ($user->email === 'muhamad@gmail.com')
            || $user->hasRole(['HR Manager', 'Super Admin', 'Admin'])
            || $user->can('manage employees');

        if (! $isHrAdmin && ! $user->can('approve leave request') && ! $user->hasRole('Line Head')) {
            abort(403, 'Anda tidak memiliki hak akses untuk menolak pengajuan ini.');
        }

        $validated = $request->validate([
            'rejection_note' => 'required|string|max:255',
        ]);

        $leaveRequest = LeaveRequest::findOrFail($id);

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
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

        // 1. Cek hak akses HR Manager / Super Admin
        $isHrAdmin = ($user->email === 'muhamad@gmail.com')
            || $user->hasRole(['HR Manager', 'Super Admin', 'Admin'])
            || $user->can('manage employees');

        $query = LeaveRequest::with(['user', 'approver'])
            ->whereIn('type', $types)
            ->latest();

        if ($isHrAdmin) {
            // HR Manager / Admin dapat melihat SEMUA pengajuan tanpa batasan line_head_id
        } elseif ($user->hasRole('Line Head') || $user->can('approve leave request')) {
            // Line Head hanya melihat pengajuan dari bawahan langsung dan dirinya sendiri
            $teamUserIds = User::where('line_head_id', $user->id)->pluck('id');
            $allIds = $teamUserIds->push($user->id);
            $query->whereIn('user_id', $allIds);
        } else {
            // Karyawan biasa hanya melihat pengajuan miliknya
            $query->where('user_id', $user->id);
        }

        return Inertia::render('Leave/Index', [
            'leaveRequests' => $query->get(),
            'pageTitle' => $pageTitle,
            'allowedTypes' => $types,
            'isApprover' => $isHrAdmin || $user->hasRole('Line Head') || $user->can('approve leave request'),
        ]);
    }
}
