<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbound_logs', function (Blueprint $table) {
            $table->id();

            // Relasi ke bahan baku (Sama persis dengan inbound_logs)
            $table->foreignId('raw_material_id')->constrained('raw_materials')->onDelete('cascade');

            // --- PENCATATAN STOK ---
            // Stok sebelum dikeluarkan
            $table->decimal('stok_sebelum', 10, 2)->default(0);
            // Kuantitas yang dikeluarkan (dipakai untuk produksi)
            $table->decimal('qty_keluar', 10, 2);
            // Sisa stok setelah bahan dikeluarkan (sesuai permintaan Anda)
            $table->decimal('sisa_stok', 10, 2)->default(0);

            // --- TUJUAN LOKASI PRODUKSI ---
            // Sesuai kebutuhan: Proses, Assembling, Pembekuan (Denso)
            $table->string('lokasi_tujuan')->comment('Pilihan: Proses, Assembling, Pembekuan (Denso)');

            // --- INFORMASI TAMBAHAN ---
            $table->date('tanggal_keluar');
            $table->string('no_dokumen')->nullable()->comment('Bisa diisi nomor DO / Surat Jalan internal jika ada');
            $table->string('keterangan')->nullable(); // Opsional untuk catatan tambahan

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbound_logs');
    }
};
