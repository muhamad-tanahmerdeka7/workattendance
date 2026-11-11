<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_logs', function (Blueprint $table) {
            $table->id();
            // Kolom ini yang tadi dicari oleh Laravel (Penting!)
            $table->foreignId('raw_material_id')->constrained('raw_materials')->onDelete('cascade');

            // --- TAMBAHAN KOLOM BARU ---
            // Merekam stok fisik sebelum qty_masuk ditambahkan
            $table->decimal('stok_sebelum', 10, 2)->default(0);
            $table->decimal('qty_masuk', 10, 2);

            // --- TAMBAHAN LOKASI DAN SUHU ---
            $table->string('lokasi_simpan')->comment('Menyimpan 6 pilihan lokasi (misal: Ruang 1 Rawmat - Mesin Chiller)');
            $table->decimal('suhu_masuk', 5, 2)->nullable()->comment('Suhu barang saat masuk dalam Celcius');

            $table->date('tanggal_masuk');
            $table->string('no_dokumen')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_logs');
    }
};
