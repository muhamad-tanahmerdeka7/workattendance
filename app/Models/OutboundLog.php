<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutboundLog extends Model
{
    use HasFactory;

    // Memastikan nama tabel yang digunakan
    protected $table = 'outbound_logs';

    // Kolom yang diizinkan untuk diisi
    protected $fillable = [
        'raw_material_id',
        'stok_sebelum',
        'qty_keluar',
        'sisa_stok',
        'lokasi_tujuan',
        'tanggal_keluar',
        'no_dokumen',
        'keterangan',
    ];

    /**
     * Relasi ke model RawMaterial
     */
    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }
}
