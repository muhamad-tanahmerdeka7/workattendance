<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'name',
        'category',
        'unit',
        'storage_location',
        'condition_note',
        'current_stock',
        'minimum_stock',
    ];

    public function recipes()
    {
        return $this->hasMany(ProductRecipe::class);
    }

    // <-- TAMBAHAN RELASI UNTUK RIWAYAT STOK MASUK -->
    public function inboundLogs()
    {
        // Menghubungkan bahan baku dengan riwayat masuknya, diurutkan dari yang terbaru
        return $this->hasMany(InboundLog::class)->orderBy('tanggal_masuk', 'desc');
    }
}
