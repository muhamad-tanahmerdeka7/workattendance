<?php

namespace Database\Seeders;

use App\Models\EmployeeShift;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeeShiftSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil shift yang diinginkan, misal Shift Pagi
        $shift = Shift::where('name', 'Pagi')->first() ?? Shift::first();
        $users = User::all();

        foreach ($users as $user) {
            EmployeeShift::firstOrCreate(
                [
                    // Cek di model/migration EmployeeShift:
                    // Biasanya memakai 'user_id' atau 'employee_id'
                    'user_id' => $user->id,
                    // Cek nama kolom tanggal: biasanya 'date' atau 'schedule_date'
                    'date'    => now()->toDateString(),
                ],
                [
                    'shift_id' => $shift->id,
                ]
            );
        }
    }
}
