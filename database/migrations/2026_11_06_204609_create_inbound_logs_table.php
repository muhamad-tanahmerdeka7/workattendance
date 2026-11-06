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

            $table->decimal('qty_masuk', 10, 2);
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
