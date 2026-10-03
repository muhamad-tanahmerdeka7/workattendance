<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
   use HasFactory;

    protected $fillable = [
        'user_id',
        'period',
        'base_salary',
        'allowances',
        'deductions',
        'net_salary',
        'employee_note',
        'is_resolved',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}