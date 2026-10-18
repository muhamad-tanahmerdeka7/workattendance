<?php

namespace App\Http\Controllers;

use App\Models\EmployeeShift;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $date = $request->input('date', Carbon::today()->toDateString());

        // Ambil semua shift
        $shifts = Shift::all();

        // Query karyawan beserta shift pada tanggal terpilih
        $query = User::with(['employee', 'employeeShifts' => function ($q) use ($date) {
            $q->where('date', $date);
        }]);

        // Jika HR Manager, tampilkan semua bawahan (Karyawan & Line Head)
        // Jika Line Head, hanya tampilkan bawahan langsungnya
        if ($user->hasRole('HR Manager')) {
            $query->where('id', '!=', $user->id); // Menampilkan semua user selain akun HR Manager itu sendiri
        } else {
            $query->where('line_head_id', $user->id);
        }

        $subordinates = $query->get();

        return Inertia::render('Shift/Index', [
            'shifts' => $shifts,
            'subordinates' => $subordinates,
            'selectedDate' => $date,
        ]);
    }

    public function assign(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'nullable|exists:shifts,id', // Bisa null jika statusnya 'off' (Libur)
            'date' => 'required|date',
            'mode' => 'required|in:daily,weekly',
            'is_off' => 'required|boolean',
        ]);

        $startDate = Carbon::parse($request->date);
        $iterations = ($request->mode === 'weekly') ? 7 : 1;

        // Lakukan perulangan jika mode mingguan (7 hari ke depan) atau harian (1 hari)
        for ($i = 0; $i < $iterations; $i++) {
            $targetDate = $startDate->copy()->addDays($i)->toDateString();

            if ($request->is_off) {
                // Jika dijatuhkan ke kotak Libur (Day Off), hapus shift atau tandai sebagai libur
                EmployeeShift::updateOrCreate(
                    [
                        'user_id' => $request->user_id,
                        'date' => $targetDate,
                    ],
                    [
                        'shift_id' => null, // null berarti libur / off
                        'assigned_by' => $request->user()->id,
                        'notes' => 'Hari Libur (Day Off)',
                    ]
                );
            } else {
                // Jika dijatuhkan ke Shift Pagi/Siang/Malam
                EmployeeShift::updateOrCreate(
                    [
                        'user_id' => $request->user_id,
                        'date' => $targetDate,
                    ],
                    [
                        'shift_id' => $request->shift_id,
                        'assigned_by' => $request->user()->id,
                        'notes' => 'Shift Terjadwal',
                    ]
                );
            }
        }

        $modeText = $request->mode === 'weekly' ? 'selama 1 minggu' : 'untuk hari ini';

        return back()->with('success', "Berhasil memperbarui jadwal shift {$modeText}.");
    }

    // -- EKSPOR LAPORAN REKAPITULASI SHIFT BULANAN (INLINE STYLING EXCEL) --
    public function export(Request $request)
    {
        $user = $request->user();

        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month.'-15')->subMonth()->toDateString(); // Closing 15 bulan lalu
        $endDate = Carbon::parse($month.'-15')->toDateString(); // Closing 15 bulan ini

        $query = User::with(['employee', 'employeeShifts' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate])->with('shift');
        }]);

        if ($user->hasRole('HR Manager')) {
            $query->where('id', '!=', $user->id);
        } else {
            $query->where('line_head_id', $user->id);
        }

        $subordinates = $query->get();

        $filename = 'Laporan_Rekap_Shift_Plant8_'.$month.'.xls';

        $headers = [
            'Content-type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($subordinates, $month) {
            echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>";
            echo '<head>';
            echo "<meta charset='UTF-8'>";
            echo '</head>';
            echo '<body>';

            // Header Judul Laporan Perusahaan
            echo "<table style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><td colspan='8' style='font-size: 14pt; font-weight: bold; text-align: center; border: none; padding-bottom: 2px;'>LAPORAN REKAPITULASI SHIFT KERJA - PLANT 8</td></tr>";
            echo "<tr><td colspan='8' style='font-size: 10pt; text-align: center; color: #333333; border: none; margin-bottom: 15px;'>Periode Closing: <b>{$month}</b> &nbsp;|&nbsp; Dicetak oleh: <b>PT Hokben Plant 8</b></td></tr>";
            echo "<tr><td colspan='8' style='border:none; height: 10px;'></td></tr>";
            echo '</table>';

            // Tabel Utama Data Karyawan dengan Inline Styling (Warna Dijamin Keluar)
            echo "<table style='border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 10pt;'>";
            echo '<thead>';
            echo "<tr style='background-color: #0066cc; color: #ffffff;'>";
            echo "<th style='border: 0.5pt solid #000000; padding: 8px; text-align: center; background-color: #0066cc; color: #ffffff;'>NIK</th>";
            echo "<th style='border: 0.5pt solid #000000; padding: 8px; text-align: center; background-color: #0066cc; color: #ffffff;'>Nama Karyawan</th>";
            echo "<th style='border: 0.5pt solid #000000; padding: 8px; text-align: center; background-color: #0066cc; color: #ffffff;'>Departemen</th>";
            echo "<th style='border: 0.5pt solid #000000; padding: 8px; text-align: center; background-color: #0066cc; color: #ffffff;'>Total Shift Pagi</th>";
            echo "<th style='border: 0.5pt solid #000000; padding: 8px; text-align: center; background-color: #0066cc; color: #ffffff;'>Total Shift Siang</th>";
            echo "<th style='border: 0.5pt solid #000000; padding: 8px; text-align: center; background-color: #0066cc; color: #ffffff;'>Total Shift Malam</th>";
            echo "<th style='border: 0.5pt solid #000000; padding: 8px; text-align: center; background-color: #0066cc; color: #ffffff;'>Total Libur (Off)</th>";
            echo "<th style='border: 0.5pt solid #000000; padding: 8px; text-align: center; background-color: #0066cc; color: #ffffff;'>Total Hadir Perbulan</th>";
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($subordinates as $sub) {
                $pagi = $sub->employeeShifts->filter(fn ($s) => $s->shift && stripos($s->shift->name, 'pagi') !== false)->count();
                $siang = $sub->employeeShifts->filter(fn ($s) => $s->shift && stripos($s->shift->name, 'siang') !== false)->count();
                $malam = $sub->employeeShifts->filter(fn ($s) => $s->shift && stripos($s->shift->name, 'malam') !== false)->count();
                $libur = $sub->employeeShifts->filter(fn ($s) => is_null($s->shift_id) || (isset($s->shift->name) && stripos($s->shift->name, 'libur') !== false))->count();

                $totalHadir = $pagi + $siang + $malam;

                $nik = $sub->employee?->employee_code ?? '-';
                $name = htmlspecialchars($sub->name);
                $dept = htmlspecialchars($sub->employee?->department ?? 'PLANT 8');

                echo '<tr>';
                echo "<td style='border: 0.5pt solid #000000; padding: 6px; text-align: center;'>{$nik}</td>";
                echo "<td style='border: 0.5pt solid #000000; padding: 6px; text-align: left;'>{$name}</td>";
                echo "<td style='border: 0.5pt solid #000000; padding: 6px; text-align: center;'>{$dept}</td>";
                echo "<td style='border: 0.5pt solid #000000; padding: 6px; text-align: center;'>{$pagi}</td>";
                echo "<td style='border: 0.5pt solid #000000; padding: 6px; text-align: center;'>{$siang}</td>";
                echo "<td style='border: 0.5pt solid #000000; padding: 6px; text-align: center;'>{$malam}</td>";
                echo "<td style='border: 0.5pt solid #000000; padding: 6px; text-align: center; color: #cc0000; font-weight: bold;'>{$libur}</td>";
                echo "<td style='border: 0.5pt solid #000000; padding: 6px; text-align: center; background-color: #e6f4ea; font-weight: bold;'>{$totalHadir}</td>";
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</body>';
            echo '</html>';
        };

        return response()->stream($callback, 200, $headers);
    }

    // -- EKSPOR LAPORAN REKAPITULASI SHIFT BULANAN (FORMAT PDF CETAK RESMI) --
    public function exportPdf(Request $request)
    {
        $user = $request->user();

        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month.'-15')->subMonth()->toDateString();
        $endDate = Carbon::parse($month.'-15')->toDateString();

        $query = User::with(['employee', 'employeeShifts' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate])->with('shift');
        }]);

        if ($user->hasRole('HR Manager')) {
            $query->where('id', '!=', $user->id);
        } else {
            $query->where('line_head_id', $user->id);
        }

        $subordinates = $query->get();

        return view('shifts.pdf-report', [
            'subordinates' => $subordinates,
            'month' => $month,
        ]);
    }

    // -- TAMPILAN MATRIKS JADWAL SHIFT UNTUK KARYAWAN (MATRIKS REKAP MINGGUAN) --
    public function schedule(Request $request)
    {
        $user = $request->user();

        // Tanggal awal (default 7 hari dari tanggal saat ini / tanggal yang dipilih)
        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfWeek()->toDateString()));
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = $startDate->copy()->addDays($i)->toDateString();
        }

        // Ambil seluruh karyawan tim
        $query = User::with(['employee', 'employeeShifts' => function ($q) use ($dates) {
            $q->whereIn('date', $dates)->with('shift');
        }]);

        if ($user->hasRole('HR Manager')) {
            $query->where('id', '!=', $user->id);
        } elseif ($user->line_head_id) {
            $query->where('line_head_id', $user->line_head_id)->orWhere('id', $user->line_head_id);
        } else {
            $query->where('line_head_id', $user->id);
        }

        $employees = $query->get();

        return Inertia::render('Shift/Schedule', [
            'employees' => $employees,
            'dates' => $dates,
            'startDate' => $startDate->toDateString(),
        ]);
    }

    // -- EKSPOR MATRIKS JADWAL TIM KE EXCEL / WPS SPREADSHEET (INLINE STYLING + TOTAL HARIAN) --
    public function exportSchedule(Request $request)
    {
        $user = $request->user();
        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfWeek()->toDateString()));

        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = $startDate->copy()->addDays($i)->toDateString();
        }

        $query = User::with(['employee', 'employeeShifts' => function ($q) use ($dates) {
            $q->whereIn('date', $dates)->with('shift');
        }]);

        if ($user->hasRole('HR Manager')) {
            $query->where('id', '!=', $user->id);
        } elseif ($user->line_head_id) {
            $query->where('line_head_id', $user->line_head_id)->orWhere('id', $user->line_head_id);
        } else {
            $query->where('line_head_id', $user->id);
        }

        $employees = $query->get();
        $filename = 'Jadwal_Shift_Tim_'.$startDate->toDateString().'.xls';

        $headers = [
            'Content-type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($employees, $dates, $startDate) {
            echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>";
            echo "<head><meta charset='UTF-8'></head><body>";

            echo "<table style='border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;'>";
            echo "<tr><td colspan='10' style='font-size: 14pt; font-weight: bold; text-align: center;'>MATRIKS JADWAL SHIFT KERJA TIM - PLANT 8</td></tr>";
            echo "<tr><td colspan='10' style='font-size: 10pt; text-align: center; color: #333333;'>Periode Pekan: <b>{$startDate->toDateString()}</b> s/d <b>".$dates[6].'</b></td></tr>';
            echo "<tr><td colspan='10' style='border:none; height: 10px;'></td></tr>";
            echo '</table>';

            echo "<table style='border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 10pt;'>";
            echo "<thead><tr style='background-color: #0066cc; color: #ffffff;'>";
            echo "<th style='border: 0.5pt solid #000; padding: 8px; background-color: #0066cc; color: #fff;'>NO</th>";
            echo "<th style='border: 0.5pt solid #000; padding: 8px; background-color: #0066cc; color: #fff;'>NAMA KARYAWAN</th>";
            echo "<th style='border: 0.5pt solid #000; padding: 8px; background-color: #0066cc; color: #fff;'>BAGIAN</th>";
            foreach ($dates as $d) {
                $dayNum = Carbon::parse($d)->day;
                echo "<th style='border: 0.5pt solid #000; padding: 8px; background-color: #0066cc; color: #fff;'>TGL {$dayNum}</th>";
            }
            echo "<th style='border: 0.5pt solid #000; padding: 8px; background-color: #0066cc; color: #fff;'>TOTAL KERJA</th>";
            echo '</tr></thead><tbody>';

            // Array Penampung Total Kerja Per Hari
            $dailyCounts = array_fill_keys($dates, 0);

            foreach ($employees as $idx => $emp) {
                $totalKerja = 0;
                echo '<tr>';
                echo "<td style='border: 0.5pt solid #000; padding: 6px; text-align: center;'>".($idx + 1).'</td>';
                echo "<td style='border: 0.5pt solid #000; padding: 6px; font-weight: bold;'>".htmlspecialchars($emp->name).'</td>';
                echo "<td style='border: 0.5pt solid #000; padding: 6px; text-align: center;'>".htmlspecialchars($emp->employee?->department ?? 'Plant 8').'</td>';

                foreach ($dates as $d) {
                    $shiftData = $emp->employeeShifts->firstWhere('date', $d);
                    if ($shiftData && $shiftData->shift_id) {
                        $label = $shiftData->shift ? strtoupper(substr($shiftData->shift->name, 0, 3)) : 'MSK';
                        $totalKerja++;
                        $dailyCounts[$d]++; // Tambah hitungan vertikal harian
                        echo "<td style='border: 0.5pt solid #000; padding: 6px; text-align: center; background-color: #e6f4ea; font-weight: bold;'>{$label}</td>";
                    } else {
                        echo "<td style='border: 0.5pt solid #000; padding: 6px; text-align: center; color: #cc0000; font-weight: bold;'>OFF</td>";
                    }
                }

                echo "<td style='border: 0.5pt solid #000; padding: 6px; text-align: center; font-weight: bold; background-color: #f1f5f9;'>{$totalKerja} Hari</td>";
                echo '</tr>';
            }

            // BARIS FOOTER REKAPITULASI TOTAL MASUK KERJA (PER HARI)
            echo "<tr style='background-color: #f1f5f9; font-weight: bold;'>";
            echo "<td colspan='3' style='border: 0.5pt solid #000; padding: 8px; text-align: right; text-transform: uppercase;'>Total Masuk Kerja (Per Hari):</td>";
            foreach ($dates as $d) {
                $count = $dailyCounts[$d];
                $bgColor = $count == 0 ? '#ffe6e6' : ($count < 5 ? '#fef3c7' : '#d1fae5');
                $textColor = $count == 0 ? '#dc2626' : ($count < 5 ? '#92400e' : '#065f46');
                echo "<td style='border: 0.5pt solid #000; padding: 8px; text-align: center; background-color: {$bgColor}; color: {$textColor}; font-weight: bold;'>{$count}</td>";
            }
            echo "<td style='border: 0.5pt solid #000; background-color: #e2e8f0;'></td>";
            echo '</tr>';

            echo '</tbody></table></body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }

    // -- EKSPOR MATRIKS JADWAL TIM KE PDF CETAK BLADE (LENGKAP DENGAN TOTAL HARIAN) --
    public function exportSchedulePdf(Request $request)
    {
        $user = $request->user();
        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfWeek()->toDateString()));

        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = $startDate->copy()->addDays($i)->toDateString();
        }

        $query = User::with(['employee', 'employeeShifts' => function ($q) use ($dates) {
            $q->whereIn('date', $dates)->with('shift');
        }]);

        if ($user->hasRole('HR Manager')) {
            $query->where('id', '!=', $user->id);
        } elseif ($user->line_head_id) {
            $query->where('line_head_id', $user->line_head_id)->orWhere('id', $user->line_head_id);
        } else {
            $query->where('line_head_id', $user->id);
        }

        $employees = $query->get();

        // Hitung Total Harian untuk Dikirim ke View PDF
        $dailyCounts = array_fill_keys($dates, 0);
        foreach ($employees as $emp) {
            foreach ($dates as $d) {
                $shiftData = $emp->employeeShifts->firstWhere('date', $d);
                if ($shiftData && $shiftData->shift_id) {
                    $dailyCounts[$d]++;
                }
            }
        }

        return view('shifts.pdf-schedule', [
            'employees' => $employees,
            'dates' => $dates,
            'startDate' => $startDate->toDateString(),
            'dailyCounts' => $dailyCounts,
        ]);
    }
}
