<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Kantor / Pabrik Utama');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('radius_meters')->default(50); // Radius toleransi (misal 50 meter)
            $table->string('updated_by')->nullable(); // Email manager yang mengubah
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_locations');
    }
};
