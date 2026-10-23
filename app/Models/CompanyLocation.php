<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'radius_meters',
        'updated_by',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meters' => 'integer',
    ];
}
