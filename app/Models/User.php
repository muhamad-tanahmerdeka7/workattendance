<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'line_head_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi ke Data Detail Karyawan
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    // Relasi ke Atasan (Line Head)
    public function lineHead()
    {
        return $this->belongsTo(User::class, 'line_head_id');
    }

    // Relasi ke Bawahan (Subordinates)
    public function subordinates()
    {
        return $this->hasMany(User::class, 'line_head_id');
    }

    // Relasi ke Presensi
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Relasi ke Pengajuan Izin
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    // Relasi ke Pengajuan Lembur
    public function overtimeRequests()
    {
        return $this->hasMany(OvertimeRequest::class);
    }

    // Relasi ke Plotting Shift Karyawan
    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }
}
