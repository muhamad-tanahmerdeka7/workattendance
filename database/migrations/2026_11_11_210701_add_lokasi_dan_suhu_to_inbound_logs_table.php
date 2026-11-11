<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inbound_logs', function (Blueprint $table) {
            // Menambahkan kolom lokasi_simpan dan suhu_masuk
            // nullable() digunakan agar data lama tidak error
            $table->string('lokasi_simpan')->nullable()->after('no_dokumen');
            $table->decimal('suhu_masuk', 5, 2)->nullable()->after('lokasi_simpan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inbound_logs', function (Blueprint $table) {
            // Menghapus kolom jika di-rollback
            $table->dropColumn(['lokasi_simpan', 'suhu_masuk']);
        });
    }
};
