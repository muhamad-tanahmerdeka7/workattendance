<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboundLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
