<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Pagi',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'is_flexible' => false,
            ],
            [
                'name' => 'Siang',
                'start_time' => '13:00:00',
                'end_time' => '22:00:00',
                'is_flexible' => false,
            ],
            [
                'name' => 'Malam',
                'start_time' => '21:00:00',
                'end_time' => '06:00:00',
                'is_flexible' => false,
            ],
            [
                'name' => 'Fleksibel / Pengganti',
                'start_time' => '00:00:00',
                'end_time' => '23:59:59',
                'is_flexible' => true,
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::firstOrCreate(
                ['name' => $shift['name']],
                $shift
            );
        }
    }
}
