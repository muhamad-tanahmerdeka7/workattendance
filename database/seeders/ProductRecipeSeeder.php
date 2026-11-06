<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductRecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // -------------------------------------------------------------
        // 1. DATA PRODUK FINISH GOOD (80 Pack Bento per Batch)
        // -------------------------------------------------------------
        $p1 = Product::updateOrCreate(
            ['item_code' => 'FG-CKN-DON'],
            [
                'name' => 'RTH F.F. SLICE CHICKEN DON',
                'line_production' => 'Plant 8 - Kitchen',
                'batch_yield' => 80,
                'unit' => 'PAC',
                'selling_price' => 35000,
                'description' => 'Bento donburi ayam iris gurih dengan garlic rice dan saus mucyu.',
                'is_active' => true,
            ]
        );

        $p2 = Product::updateOrCreate(
            ['item_code' => 'FG-HMB-KTC'],
            [
                'name' => 'FG RTH HAMBAGU KETCHUP RICE',
                'line_production' => 'Plant 8 - Kitchen',
                'batch_yield' => 80,
                'unit' => 'PAC',
                'selling_price' => 38000,
                'description' => 'Bento daging sapi hambagu cincang juicy dengan ketchup rice dan saus teriyaki.',
                'is_active' => true,
            ]
        );

        // -------------------------------------------------------------
        // 2. KATALOG BAHAN BAKU, BUMBU & KEMASAN
        // -------------------------------------------------------------
        $materials = [
            // Bahan Komposisi Daging / Lauk Utama
            ['item_code' => 'C-00366', 'name' => 'CHICKEN SLICE JFS', 'category' => 'meat', 'unit' => 'PAC', 'storage_location' => 'Logistik Frozen (RM Vendor)', 'condition_note' => null],
            ['item_code' => 'M-00042', 'name' => 'MINYAK GORENG - POUCH', 'category' => 'seasoning', 'unit' => 'G', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'B-00071', 'name' => 'BIJI WIJEN KODE 4030', 'category' => 'seasoning', 'unit' => 'G', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'T-00359', 'name' => 'TAMAGOYAKI FROZEN 500 GR', 'category' => 'meat', 'unit' => 'PAC', 'storage_location' => 'Logistik Frozen (RM Vendor)', 'condition_note' => 'New Item'],
            ['item_code' => 'S-00469', 'name' => 'SAPI SESELAN SEDANG UP (Bersih Lemak)', 'category' => 'meat', 'unit' => 'G', 'storage_location' => 'Logistik RM', 'condition_note' => 'Kondisi Thawing (-6 C) - (-2 C)'],
            ['item_code' => 'A-00131', 'name' => 'AYAM KULIT', 'category' => 'meat', 'unit' => 'G', 'storage_location' => 'Logistik RM', 'condition_note' => 'Kondisi Thawing (-6 C) - (-2 C)'],
            ['item_code' => 'T-00211', 'name' => 'TELUR AYAM - CAMPUR', 'category' => 'meat', 'unit' => 'G', 'storage_location' => 'Logistik RM', 'condition_note' => 'Kondisi Fresh'],
            ['item_code' => 'T-00271', 'name' => 'TEPUNG BREAD CRUMB 6012', 'category' => 'seasoning', 'unit' => 'G', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'F-00076', 'name' => 'FRESH MILK PLAIN DM', 'category' => 'seasoning', 'unit' => 'G', 'storage_location' => 'Logistik Frozen', 'condition_note' => null],
            ['item_code' => 'B-00668', 'name' => 'BK FF260004', 'category' => 'seasoning', 'unit' => 'PAC', 'storage_location' => 'Logistik Seasoning (Blackbox - Plant Cakung)', 'condition_note' => null],
            ['item_code' => 'B-00666', 'name' => 'BK FF260002', 'category' => 'seasoning', 'unit' => 'PAC', 'storage_location' => 'Logistik Seasoning (Blackbox - Plant Cakung)', 'condition_note' => null],
            ['item_code' => 'B-00047', 'name' => 'BAWANG BOMBAY (Giling)', 'category' => 'vegetable', 'unit' => 'G', 'storage_location' => 'Logistik Dry', 'condition_note' => null],

            // Beras & Bumbu Racik Nasi
            ['item_code' => 'B-00069', 'name' => 'BERAS 1 R', 'category' => 'rice', 'unit' => 'PAC', 'storage_location' => 'Logistik Beras', 'condition_note' => null],
            ['item_code' => 'B-00630', 'name' => 'BERAS 500 GR', 'category' => 'rice', 'unit' => 'PAC', 'storage_location' => 'Logistik Beras', 'condition_note' => null],
            ['item_code' => 'B-00312', 'name' => 'BAWANG PUTIH CUTTING KUPAS (Giling)', 'category' => 'vegetable', 'unit' => 'G', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'B-00663', 'name' => 'BK FF260001', 'category' => 'seasoning', 'unit' => 'PAC', 'storage_location' => 'Logistik Seasoning (Blackbox - Plant Cakung)', 'condition_note' => null],
            ['item_code' => 'B-00665', 'name' => 'BK FF260005', 'category' => 'seasoning', 'unit' => 'PAC', 'storage_location' => 'Logistik Seasoning (Blackbox - Plant Cakung)', 'condition_note' => null],
            ['item_code' => 'B-00672', 'name' => 'BK FF260008', 'category' => 'seasoning', 'unit' => 'PAC', 'storage_location' => 'Logistik Seasoning (Blackbox - Plant Cakung)', 'condition_note' => null],
            ['item_code' => 'S-00687', 'name' => 'SAUS TOMAT 1 KG MCL', 'category' => 'seasoning', 'unit' => 'PAC', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'B-00667', 'name' => 'BK FF260003', 'category' => 'seasoning', 'unit' => 'PAC', 'storage_location' => 'Logistik Seasoning (Blackbox - Plant Cakung)', 'condition_note' => null],

            // Saus & Bumbu Cair
            ['item_code' => 'S-00141', 'name' => 'SESAME OIL', 'category' => 'seasoning', 'unit' => 'G', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'B-00664', 'name' => 'BK FF260003', 'category' => 'seasoning', 'unit' => 'PAC', 'storage_location' => 'Logistik Seasoning (Blackbox - Plant Cakung)', 'condition_note' => null],
            ['item_code' => 'O-00046', 'name' => 'OYSTER SAUCE', 'category' => 'seasoning', 'unit' => 'G', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'J-00055', 'name' => 'JAPANESE SWEET COOKING SAUCE SE', 'category' => 'seasoning', 'unit' => 'G', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'G-00045', 'name' => 'GOCHUJANG 1 KG', 'category' => 'seasoning', 'unit' => 'PAC', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'C-00367', 'name' => 'CHILLI BEAN SAUCE 226 GRAM', 'category' => 'seasoning', 'unit' => 'PAC', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'S-00488', 'name' => 'SAUCE TERIYAKI 950 GRAM', 'category' => 'seasoning', 'unit' => 'PAC', 'storage_location' => 'Logistik Frozen', 'condition_note' => 'Kondisi Thawing'],

            // Sayuran
            ['item_code' => 'M-00219', 'name' => 'MIX VEGETABLES 3 WAY FROZEN 1 KG', 'category' => 'vegetable', 'unit' => 'PAC', 'storage_location' => 'Logistik Frozen (RM Vendor)', 'condition_note' => null],
            ['item_code' => 'B-00047-UT', 'name' => 'BAWANG BOMBAY (Utuh Kupas)', 'category' => 'vegetable', 'unit' => 'G', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'P-00300', 'name' => 'PAPRIKA', 'category' => 'vegetable', 'unit' => 'G', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'J-00030', 'name' => 'JAGUNG PIPIL', 'category' => 'vegetable', 'unit' => 'PAC', 'storage_location' => 'Logistik Frozen', 'condition_note' => 'RM Vendor - New Item'],
            ['item_code' => 'W-00010', 'name' => 'WORTEL DADU 1 X 1 X 1', 'category' => 'vegetable', 'unit' => 'PAC', 'storage_location' => 'Logistik Frozen', 'condition_note' => 'RM Vendor - New Item'],

            // Kemasan (Packaging)
            ['item_code' => 'PKG-LB-01', 'name' => 'LUNCH BOX (Oval Bento)', 'category' => 'packaging', 'unit' => 'PC', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'PKG-OB-01', 'name' => 'OUTER BOX', 'category' => 'packaging', 'unit' => 'PC', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'PKG-ROL-01', 'name' => 'PLASTIK ROLL NYLON 15 um / LLDPE 60 um', 'category' => 'packaging', 'unit' => 'ROL', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
            ['item_code' => 'C-00359', 'name' => 'CARTON BOX BESAR', 'category' => 'packaging', 'unit' => 'PC', 'storage_location' => 'Logistik Dry', 'condition_note' => null],
        ];

        $materialMap = [];
        foreach ($materials as $m) {
            $created = RawMaterial::updateOrCreate(
                ['item_code' => $m['item_code']],
                [
                    'name' => $m['name'],
                    'category' => $m['category'],
                    'unit' => $m['unit'],
                    'storage_location' => $m['storage_location'],
                    'condition_note' => $m['condition_note'],
                    'current_stock' => 500, // Stok awal simulasi
                    'minimum_stock' => 20,
                ]
            );
            $materialMap[$m['item_code']] = $created->id;
        }

        // -------------------------------------------------------------
        // 3. KOMPOSISI RESEP 1: RTH F.F. SLICE CHICKEN DON (Gambar 1)
        // -------------------------------------------------------------
        ProductRecipe::where('product_id', $p1->id)->delete();
        $p1Items = [
            // Tabel 1. Komposisi Chicken + Tamagoyaki
            ['code' => 'C-00366', 'group' => 'Tabel 1. Komposisi Chicken + Tamagoyaki', 'qty' => 7, 'unit' => 'PAC'],
            ['code' => 'M-00042', 'group' => 'Tabel 1. Komposisi Chicken + Tamagoyaki', 'qty' => 100, 'unit' => 'G'],
            ['code' => 'B-00071', 'group' => 'Tabel 1. Komposisi Chicken + Tamagoyaki', 'qty' => 20, 'unit' => 'G'],
            ['code' => 'T-00359', 'group' => 'Tabel 1. Komposisi Chicken + Tamagoyaki', 'qty' => 3.5, 'unit' => 'PAC'],

            // Tabel 2. Komposisi Garlic Rice
            ['code' => 'B-00069', 'group' => 'Tabel 2. Komposisi Garlic Rice', 'qty' => 1, 'unit' => 'PAC'],
            ['code' => 'B-00312', 'group' => 'Tabel 2. Komposisi Garlic Rice', 'qty' => 360, 'unit' => 'G'],
            ['code' => 'B-00663', 'group' => 'Tabel 2. Komposisi Garlic Rice', 'qty' => 1, 'unit' => 'PAC'],
            ['code' => 'B-00665', 'group' => 'Tabel 2. Komposisi Garlic Rice', 'qty' => 1, 'unit' => 'PAC'],
            ['code' => 'B-00672', 'group' => 'Tabel 2. Komposisi Garlic Rice', 'qty' => 1, 'unit' => 'PAC'],
            ['code' => 'M-00219', 'group' => 'Tabel 2. Komposisi Garlic Rice', 'qty' => 1, 'unit' => 'PAC'],

            // Tabel 3. Komposisi Mucyu Sauce
            ['code' => 'S-00141', 'group' => 'Tabel 3. Komposisi Mucyu Sauce', 'qty' => 300, 'unit' => 'G'],
            ['code' => 'B-00664', 'group' => 'Tabel 3. Komposisi Mucyu Sauce', 'qty' => 1, 'unit' => 'PAC'],
            ['code' => 'O-00046', 'group' => 'Tabel 3. Komposisi Mucyu Sauce', 'qty' => 750, 'unit' => 'G'],
            ['code' => 'J-00055', 'group' => 'Tabel 3. Komposisi Mucyu Sauce', 'qty' => 750, 'unit' => 'G'],
            ['code' => 'G-00045', 'group' => 'Tabel 3. Komposisi Mucyu Sauce', 'qty' => 1, 'unit' => 'PAC'],
            ['code' => 'C-00367', 'group' => 'Tabel 3. Komposisi Mucyu Sauce', 'qty' => 1, 'unit' => 'PAC'],

            // Tabel 5. Kemasan
            ['code' => 'PKG-LB-01', 'group' => 'Tabel 5. Kemasan', 'qty' => 80, 'unit' => 'PC'],
            ['code' => 'PKG-OB-01', 'group' => 'Tabel 5. Kemasan', 'qty' => 80, 'unit' => 'PC'],
            ['code' => 'PKG-ROL-01', 'group' => 'Tabel 5. Kemasan', 'qty' => 0.06, 'unit' => 'ROL'],
            ['code' => 'C-00359', 'group' => 'Tabel 5. Kemasan', 'qty' => 4, 'unit' => 'PC'],
        ];

        foreach ($p1Items as $item) {
            if (isset($materialMap[$item['code']])) {
                ProductRecipe::create([
                    'product_id' => $p1->id,
                    'raw_material_id' => $materialMap[$item['code']],
                    'table_group' => $item['group'],
                    'quantity' => $item['qty'],
                    'unit' => $item['unit'],
                ]);
            }
        }

        // -------------------------------------------------------------
        // 4. KOMPOSISI RESEP 2: FG RTH HAMBAGU KETCHUP RICE (Gambar 2)
        // -------------------------------------------------------------
        ProductRecipe::where('product_id', $p2->id)->delete();
        $p2Items = [
            // Tabel 1. Komposisi Hambagu
            ['code' => 'S-00469', 'group' => 'Tabel 1. Komposisi Hambagu', 'qty' => 3800, 'unit' => 'G'],
            ['code' => 'A-00131', 'group' => 'Tabel 1. Komposisi Hambagu', 'qty' => 950, 'unit' => 'G'],
            ['code' => 'T-00211', 'group' => 'Tabel 1. Komposisi Hambagu', 'qty' => 1200, 'unit' => 'G'],
            ['code' => 'T-00271', 'group' => 'Tabel 1. Komposisi Hambagu', 'qty' => 200, 'unit' => 'G'],
            ['code' => 'F-00076', 'group' => 'Tabel 1. Komposisi Hambagu', 'qty' => 425, 'unit' => 'G'],
            ['code' => 'B-00668', 'group' => 'Tabel 1. Komposisi Hambagu', 'qty' => 1, 'unit' => 'PAC'],
            ['code' => 'B-00666', 'group' => 'Tabel 1. Komposisi Hambagu', 'qty' => 1, 'unit' => 'PAC'],
            ['code' => 'B-00047', 'group' => 'Tabel 1. Komposisi Hambagu', 'qty' => 470, 'unit' => 'G'],
            ['code' => 'M-00042', 'group' => 'Tabel 1. Komposisi Hambagu', 'qty' => 25, 'unit' => 'G'],

            // Tabel 2. Komposisi Ketchup Rice
            ['code' => 'B-00630', 'group' => 'Tabel 2. Komposisi Ketchup Rice', 'qty' => 12, 'unit' => 'PAC'],
            ['code' => 'B-00672', 'group' => 'Tabel 2. Komposisi Ketchup Rice', 'qty' => 1, 'unit' => 'PAC'],
            ['code' => 'S-00687', 'group' => 'Tabel 2. Komposisi Ketchup Rice', 'qty' => 3, 'unit' => 'PAC'],
            ['code' => 'B-00667', 'group' => 'Tabel 2. Komposisi Ketchup Rice', 'qty' => 1, 'unit' => 'PAC'],

            // Tabel 3. Komposisi Sauce
            ['code' => 'S-00488', 'group' => 'Tabel 3. Komposisi Sauce', 'qty' => 4, 'unit' => 'PAC'],

            // Tabel 4. Komposisi Sayuran
            ['code' => 'B-00047-UT', 'group' => 'Tabel 4. Komposisi Sayuran', 'qty' => 1000, 'unit' => 'G'],
            ['code' => 'P-00300', 'group' => 'Tabel 4. Komposisi Sayuran', 'qty' => 1000, 'unit' => 'G'],
            ['code' => 'J-00030', 'group' => 'Tabel 4. Komposisi Sayuran', 'qty' => 1, 'unit' => 'PAC'],
            ['code' => 'W-00010', 'group' => 'Tabel 4. Komposisi Sayuran', 'qty' => 0.5, 'unit' => 'PAC'],

            // Tabel 5. Kemasan
            ['code' => 'PKG-LB-01', 'group' => 'Tabel 5. Kemasan', 'qty' => 80, 'unit' => 'PC'],
            ['code' => 'PKG-OB-01', 'group' => 'Tabel 5. Kemasan', 'qty' => 80, 'unit' => 'PC'],
            ['code' => 'PKG-ROL-01', 'group' => 'Tabel 5. Kemasan', 'qty' => 0.06, 'unit' => 'ROL'],
            ['code' => 'C-00359', 'group' => 'Tabel 5. Kemasan', 'qty' => 6, 'unit' => 'PC'],
        ];

        foreach ($p2Items as $item) {
            if (isset($materialMap[$item['code']])) {
                ProductRecipe::create([
                    'product_id' => $p2->id,
                    'raw_material_id' => $materialMap[$item['code']],
                    'table_group' => $item['group'],
                    'quantity' => $item['qty'],
                    'unit' => $item['unit'],
                ]);
            }
        }
    }
}
