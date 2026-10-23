<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nik')->nullable(); // NIK atau PIN karyawan dari mesin
            $table->enum('source_type', ['gps', 'machine']); // Sumber data
            $table->string('device_name')->nullable(); // Nama mesin / tipe browser/ponsel
            $table->decimal('latitude', 10, 7)->nullable(); // Koordinat GPS
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('distance_meters', 8, 2)->nullable(); // Jarak dari titik acuan kantor (meter)
            $table->boolean('is_in_radius')->default(false); // Validitas geofence
            $table->enum('scan_type', ['in', 'out'])->default('in'); // Check-in atau Check-out
            $table->timestamp('scanned_at'); // Waktu presensi sebenarnya
            $table->text('raw_payload')->nullable(); // JSON data mentah untuk kebutuhan audit
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
