<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['recipes.rawMaterial'])->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('item_code', 'like', '%'.$request->search.'%');
            });
        }

        // Ambil daftar bahan baku untuk dropdown pilihan saat buat resep
        $rawMaterials = RawMaterial::select('id', 'item_code', 'name', 'unit', 'storage_location')->get();

        return Inertia::render('Product/Index', [
            'products' => $query->paginate(10)->withQueryString(),
            'rawMaterials' => $rawMaterials,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        // Abaikan baris resep yang masih kosong agar tidak memblokir penyimpanan produk manual
        if ($request->has('recipes') && is_array($request->recipes)) {
            $filteredRecipes = collect($request->recipes)
                ->filter(fn ($r) => ! empty($r['raw_material_id']))
                ->values()
                ->all();
            $request->merge(['recipes' => $filteredRecipes]);
        }

        $validated = $request->validate([
            'item_code' => 'required|string|unique:products,item_code|max:50',
            'name' => 'required|string|max:255',
            'line_production' => 'required|string|max:100',
            'batch_yield' => 'required|integer|min:1',
            'unit' => 'required|string|max:20',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'recipes' => 'nullable|array',
            'recipes.*.raw_material_id' => 'required|exists:raw_materials,id',
            'recipes.*.table_group' => 'required|string|max:100',
            'recipes.*.quantity' => 'required|numeric|min:0.001',
            'recipes.*.unit' => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::create([
                'item_code' => $validated['item_code'],
                'name' => $validated['name'],
                'line_production' => $validated['line_production'],
                'batch_yield' => $validated['batch_yield'],
                'unit' => $validated['unit'],
                'selling_price' => $validated['selling_price'],
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]);

            if (! empty($validated['recipes'])) {
                foreach ($validated['recipes'] as $recipe) {
                    ProductRecipe::create([
                        'product_id' => $product->id,
                        'raw_material_id' => $recipe['raw_material_id'],
                        'table_group' => $recipe['table_group'],
                        'quantity' => $recipe['quantity'],
                        'unit' => $recipe['unit'] ?? 'PCS',
                    ]);
                }
            }
        });

        return back()->with('success', 'Produk finish good baru berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        // Abaikan baris resep yang masih kosong saat proses edit
        if ($request->has('recipes') && is_array($request->recipes)) {
            $filteredRecipes = collect($request->recipes)
                ->filter(fn ($r) => ! empty($r['raw_material_id']))
                ->values()
                ->all();
            $request->merge(['recipes' => $filteredRecipes]);
        }

        $validated = $request->validate([
            'item_code' => 'required|string|max:50|unique:products,item_code,'.$product->id,
            'name' => 'required|string|max:255',
            'line_production' => 'required|string|max:100',
            'batch_yield' => 'required|integer|min:1',
            'unit' => 'required|string|max:20',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'recipes' => 'nullable|array',
            'recipes.*.raw_material_id' => 'required|exists:raw_materials,id',
            'recipes.*.table_group' => 'required|string|max:100',
            'recipes.*.quantity' => 'required|numeric|min:0.001',
            'recipes.*.unit' => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($product, $validated) {
            $product->update([
                'item_code' => $validated['item_code'],
                'name' => $validated['name'],
                'line_production' => $validated['line_production'],
                'batch_yield' => $validated['batch_yield'],
                'unit' => $validated['unit'],
                'selling_price' => $validated['selling_price'],
                'description' => $validated['description'] ?? null,
            ]);

            // Hapus resep lama milik produk ini, lalu simpan susunan resep yang baru
            ProductRecipe::where('product_id', $product->id)->delete();

            if (! empty($validated['recipes'])) {
                foreach ($validated['recipes'] as $recipe) {
                    ProductRecipe::create([
                        'product_id' => $product->id,
                        'raw_material_id' => $recipe['raw_material_id'],
                        'table_group' => $recipe['table_group'],
                        'quantity' => $recipe['quantity'],
                        'unit' => $recipe['unit'] ?? 'PCS',
                    ]);
                }
            }
        });

        return back()->with('success', 'Data produk dan resep BOM berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            ProductRecipe::where('product_id', $product->id)->delete();
            $product->delete();
        });

        return back()->with('success', 'Produk beserta resep BOM berhasil dihapus.');
    }

    public function exportPdf(Product $product)
    {
        $product->load(['recipes.rawMaterial']);

        $pdf = Pdf::loadView('products.pdf', compact('product'));

        // Menggunakan stream() agar memunculkan preview dokumen PDF di tab baru browser
        return $pdf->stream('Form-Standar-Resep-'.$product->item_code.'.pdf');
    }
}
