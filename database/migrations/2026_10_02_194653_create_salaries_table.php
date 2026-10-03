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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('period'); // Contoh: "Oktober 2026"
            $table->integer('base_salary'); // Gaji Pokok
            $table->integer('allowances')->default(0); // Tunjangan
            $table->integer('deductions')->default(0); // Potongan (karena telat/alpa)
            $table->integer('net_salary'); // Gaji Bersih (Pokok + Tunjangan - Potongan)

            // Kolom untuk komplain karyawan
            $table->text('employee_note')->nullable(); // Catatan/Komplain karyawan
            $table->boolean('is_resolved')->default(true); // Status komplain (true = aman, false = ada komplain)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
