<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class SalarySeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua karyawan (user yang memiliki line_head_id)
        $employees = User::whereNotNull('line_head_id')->get();

        if ($employees->isEmpty()) {
            $this->command->info('Tidak ada karyawan bawahan yang ditemukan. Buat data karyawan terlebih dahulu.');

            return;
        }

        // Rentang periode tutup buku 15-15 (Contoh: 15 Sept 2026 s.d 15 Okt 2026)
        $startDate = Carbon::create(2026, 9, 15);
        $endDate = Carbon::create(2026, 10, 15);

        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($employees as $employee) {
            $this->command->info('Membuat data presensi & alpa untuk: '.$employee->name);

            foreach ($period as $date) {
                // Lewati hari Minggu (libur)
                if ($date->isSunday()) {
                    continue;
                }

                // Probabilitas kehadiran:
                // 70% Hadir Normal, 15% Terlambat, 15% Alpa (Tidak Hadir)
                $rand = rand(1, 100);

                if ($rand <= 70) {
                    $status = 'present';
                    $timeIn = '08:00:00';
                    $timeOut = '17:00:00';
                    $notes = 'Hadir tepat waktu';
                } elseif ($rand <= 85) {
                    $status = 'late';
                    $timeIn = '08:45:00';
                    $timeOut = '17:00:00';
                    $notes = 'Terlambat masuk kerja';
                } else {
                    // Status ALPA / TIDAK HADIR
                    $status = 'absent';
                    $timeIn = null;
                    $timeOut = null;
                    $notes = 'Tanpa Keterangan (Alpa)';
                }

                Attendance::updateOrCreate(
                    [
                        'user_id' => $employee->id,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'time_in' => $timeIn,
                        'time_out' => $timeOut,
                        'status' => $status,
                        'notes' => $notes,
                    ]
                );
            }
        }

        $this->command->info('Berhasil! Data presensi lengkap dengan status Hadir, Terlambat, dan Alpa telah digenerate.');
    }
}
