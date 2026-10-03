<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isHead = User::where('line_head_id', $user->id)->exists();

        if ($isHead) {
            $salaries = Salary::with('user.employee')
                ->whereHas('user', function ($query) use ($user) {
                    $query->where('line_head_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $subordinates = User::with('employee')->where('line_head_id', $user->id)->get();
        } else {
            $salaries = Salary::with('user.employee')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $subordinates = [];
        }

        return Inertia::render('Salary/Index', [
            'salaries' => $salaries,
            'isHead' => $isHead,
            'subordinates' => $subordinates,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|min:1|max:12', // Bulan target (misal: Oktober = 10)
            'year' => 'required|integer',
            'daily_rate' => 'required|numeric',
            'allowances' => 'required|numeric',
            'deductions' => 'required|numeric',
        ]);

        // TENTUKAN RENTANG TANGGAL 15 KE 15
        // Contoh: Jika pilih Bulan Oktober 2026 -> 15 Sept 2026 s/d 15 Okt 2026
        $targetMonth = $request->month;
        $targetYear = $request->year;

        $startDate = Carbon::createFromDate($targetYear, $targetMonth, 15)->subMonth(); // 15 bulan lalu
        $endDate = Carbon::createFromDate($targetYear, $targetMonth, 15); // 15 bulan ini

        // Ambil data absensi dalam rentang tanggal tersebut
        $attendances = Attendance::where('user_id', $request->user_id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date', 'asc')
            ->get();

        // Filter hari masuk (hadir / terlambat)
        $presentDaysCount = 0;
        $attendanceDetails = [];

        foreach ($attendances as $att) {
            if (in_array($att->status, ['present', 'late'])) {
                $presentDaysCount++;
                $attendanceDetails[] = [
                    'date' => $att->date,
                    'status' => 'Masuk ('.strtoupper($att->status).')',
                ];
            } else {
                $attendanceDetails[] = [
                    'date' => $att->date,
                    'status' => 'Tidak Masuk / Alpa',
                ];
            }
        }

        // Kalkulasi Gaji
        $base_salary = $presentDaysCount * $request->daily_rate;
        $net_salary = $base_salary + $request->allowances - $request->deductions;

        // Label Periode yang jelas
        $periodLabel = '15 '.$startDate->translatedFormat('F Y').' s.d. 15 '.$endDate->translatedFormat('F Y').' ('.$presentDaysCount.' Hari)';

        // Simpan ke Database
        Salary::create([
            'user_id' => $request->user_id,
            'period' => $periodLabel,
            'base_salary' => $base_salary,
            'allowances' => $request->allowances,
            'deductions' => $request->deductions,
            'net_salary' => $net_salary,
            // Kita bisa simpan rincian tanggal ke kolom catatan karyawan sementara atau abaikan jika ingin ditampilkan lewat relasi
        ]);

        return back()->with('success', 'Gaji periode 15-15 berhasil dihitung otomatis.');
    }

    public function destroy(Salary $salary)
    {
        $salary->delete();

        return back()->with('success', 'Data gaji berhasil dihapus.');
    }

    public function complain(Request $request, Salary $salary)
    {
        $request->validate([
            'employee_note' => 'required|string|max:500',
        ]);

        if ($salary->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $salary->update([
            'employee_note' => $request->employee_note,
            'is_resolved' => false,
        ]);

        return back()->with('success', 'Komplain berhasil dikirim ke atasan.');
    }
}
