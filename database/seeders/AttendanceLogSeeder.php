<?php

namespace Database\Seeders;

use App\Models\AttendanceLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AttendanceLogSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::take(5)->get();

        if ($users->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            // 1. Contoh Log dari Mesin Fisik (Fingerprint / Scan Wajah)
            AttendanceLog::create([
                'user_id' => $user->id,
                'nik' => $user->nik ?? '8160001',
                'source_type' => 'machine',
                'device_name' => 'Mesin Fingerprint Gerbang Utama',
                'scan_type' => 'in',
                'scanned_at' => Carbon::now()->subHours(5),
                'raw_payload' => json_encode(['verify_mode' => 'fingerprint', 'device_ip' => '192.168.1.200']),
            ]);

            // 2. Contoh Log dari GPS Ponsel (Dalam Radius Kantor)
            AttendanceLog::create([
                'user_id' => $user->id,
                'nik' => $user->nik ?? '8160001',
                'source_type' => 'gps',
                'device_name' => 'Chrome Mobile (Android)',
                'latitude' => -6.308912,
                'longitude' => 106.878411,
                'distance_meters' => 25.50,
                'is_in_radius' => true,
                'scan_type' => 'in',
                'scanned_at' => Carbon::now()->subHours(4),
                'raw_payload' => json_encode(['accuracy' => 12.0]),
            ]);

            // 3. Contoh Log GPS di Luar Radius
            AttendanceLog::create([
                'user_id' => $user->id,
                'nik' => $user->nik ?? '8160001',
                'source_type' => 'gps',
                'device_name' => 'Safari Mobile (iOS)',
                'latitude' => -6.208763,
                'longitude' => 106.845599,
                'distance_meters' => 12400.00,
                'is_in_radius' => false,
                'scan_type' => 'out',
                'scanned_at' => Carbon::now()->subMinutes(30),
                'raw_payload' => json_encode(['accuracy' => 15.0]),
            ]);
        }
    }
}
