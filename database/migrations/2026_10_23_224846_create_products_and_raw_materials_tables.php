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
        // 1. Tabel Produk Jadi (8 Menu Finish Good)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique(); // Misal: FG-CKN-DON, FG-HMB-KTC
            $table->string('name');                // Nama Menu Finish Good
            $table->string('line_production')->default('Plant 8 - Kitchen');
            $table->integer('batch_yield')->default(80); // Standar 1 resep = 80 pack bento
            $table->string('unit')->default('PAC');      // Satuan output: PAC, BOX, PORTION
            $table->decimal('selling_price', 15, 2)->default(0); // Harga jual / porsi
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Katalog Bahan Baku & Kemasan
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique(); // Misal: C-00366, S-00469, B-00069
            $table->string('name');                // Nama Bahan / Bumbu / Kemasan
            $table->enum('category', [
                'meat',        // Daging, Ayam, Telur
                'rice',        // Beras
                'seasoning',   // Saus, Minyak, Bumbu Racik (BK)
                'vegetable',   // Sayuran, Bawang
                'packaging',   // Lunch Box, Outer Box, Plastik Roll, Karton
            ]);
            $table->string('unit');                // Satuan dasar: G, PAC, PC, ROL, KG
            $table->string('storage_location');    // Logistik Frozen, Logistik Dry, Logistik Beras, Logistik Seasoning
            $table->string('condition_note')->nullable(); // Misal: Kondisi Thawing (-6 C) - (-2 C), Kondisi Fresh
            $table->decimal('current_stock', 15, 3)->default(0);  // Jumlah stok fisik di gudang
            $table->decimal('minimum_stock', 15, 3)->default(10); // Batas aman peringatan restock (Safety Stock)
            $table->timestamps();
        });

        // 3. Tabel Pivot Resep / BOM (Menghubungkan Produk Jadi dengan Kebutuhan Bahan)
        Schema::create('product_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->cascadeOnDelete();
            $table->string('table_group'); // Misal: Tabel 1. Komposisi Utama, Tabel 2. Nasi, Tabel 3. Saus, Tabel 5. Kemasan
            $table->decimal('quantity', 15, 3); // Jumlah kebutuhan untuk 1 batch (80 pack)
            $table->string('unit');             // Satuan takaran resep (G, PAC, PC, ROL)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_recipes');
        Schema::dropIfExists('raw_materials');
        Schema::dropIfExists('products');
    }
};
