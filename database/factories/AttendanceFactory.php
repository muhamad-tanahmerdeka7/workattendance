<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        // Status acak: 80% Hadir, 10% Terlambat, 10% Alpa/Tidak Masuk
        $statuses = ['present', 'present', 'present', 'present', 'late', 'absent'];
        $status = $this->faker->randomElement($statuses);

        return [
            'user_id' => User::factory(), // Akan di-override di seeder
            'date' => now(),             // Akan di-override di seeder
            'time_in' => $status === 'absent' ? null : '08:00:00',
            'time_out' => $status === 'absent' ? null : '17:00:00',
            'status' => $status === 'absent' ? 'absent' : ($status === 'late' ? 'late' : 'present'),
            'notes' => $status === 'late' ? 'Terlambat karena macet' : ($status === 'absent' ? 'Tanpa keterangan / Alpa' : 'Hadir tepat waktu'),
        ];
    }
}
