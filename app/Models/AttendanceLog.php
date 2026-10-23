<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $table = 'attendance_logs';

    protected $fillable = [
        'user_id',
        'nik',
        'source_type',
        'device_name',
        'latitude',
        'longitude',
        'distance_meters',
        'is_in_radius',
        'scan_type',
        'scanned_at',
        'raw_payload',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'is_in_radius' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'distance_meters' => 'float',
    ];

    /**
     * Relasi ke data pengguna / karyawan
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
