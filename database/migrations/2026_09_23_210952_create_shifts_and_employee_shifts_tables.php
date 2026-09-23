<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Shift
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Pagi, Siang, Malam, Fleksibel/Pengganti
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_flexible')->default(false); // Flag untuk shift pengganti/opsional
            $table->timestamps();
        });

        // 2. Tabel Plotting Shift Karyawan per Tanggal
        Schema::create('employee_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('assigned_by')->nullable()->constrained('users'); // Head yang menugaskan
            $table->text('notes')->nullable(); // Catatan (misal: Menggantikan Si A)
            $table->timestamps();
        });

        // 3. Tambahkan shift_id pada tabel attendances
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->after('user_id')->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
        });
        Schema::dropIfExists('employee_shifts');
        Schema::dropIfExists('shifts');
    }
};
