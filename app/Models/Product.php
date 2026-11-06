<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'name',
        'line_production',
        'batch_yield',
        'unit',
        'selling_price',
        'description',
        'is_active',
    ];

    public function recipes()
    {
        return $this->hasMany(ProductRecipe::class);
    }
}
