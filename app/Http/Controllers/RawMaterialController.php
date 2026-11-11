<?php

namespace App\Http\Controllers;

use App\Models\InboundLog;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RawMaterialController extends Controller
{
    public function index(Request $request)
    {
        // $query = RawMaterial::query()->latest();
        $query = RawMaterial::query()->with('inboundLogs')->latest();

        // Filter pencarian nama atau kode item
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('item_code', 'like', '%'.$request->search.'%');
            });
        }

        // Filter kategori (meat, rice, seasoning, vegetable, packaging)
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter lokasi simpan (Frozen, Dry, Beras, Seasoning)
        if ($request->filled('storage') && $request->storage !== 'all') {
            $query->where('storage_location', 'like', '%'.$request->storage.'%');
        }

        return Inertia::render('RawMaterial/Index', [
            'materials' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'category', 'storage']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|unique:raw_materials,item_code|max:50',
            'name' => 'required|string|max:255',
            'category' => 'required|in:meat,rice,seasoning,vegetable,packaging',
            'unit' => 'required|string|max:20',
            'storage_location' => 'required|string|max:100',
            'condition_note' => 'nullable|string|max:255',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        RawMaterial::create($validated);

        return back()->with('success', 'Bahan baku baru berhasil ditambahkan.');
    }

    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:50|unique:raw_materials,item_code,'.$rawMaterial->id,
            'name' => 'required|string|max:255',
            'category' => 'required|in:meat,rice,seasoning,vegetable,packaging',
            'unit' => 'required|string|max:20',
            'storage_location' => 'required|string|max:100',
            'condition_note' => 'nullable|string|max:255',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        $rawMaterial->update($validated);

        return back()->with('success', 'Data bahan baku berhasil diperbarui.');
    }

    public function destroy(RawMaterial $rawMaterial)
    {
        $rawMaterial->delete();

        return back()->with('success', 'Bahan baku berhasil dihapus.');
    }

    // <-- TAMBAHAN METHOD UNTUK MENANGANI PENAMBAHAN STOK MASUK -->
    public function addStock(Request $request, $id)
    {
        $request->validate([
            'qty_masuk' => 'required|numeric|min:0.01',
            'tanggal_masuk' => 'required|date',
            'no_dokumen' => 'nullable|string|max:100',
            'lokasi_simpan' => 'required|string',   // <-- TAMBAHAN VALIDASI LOKASI
            'suhu_masuk' => 'required|numeric',     // <-- TAMBAHAN VALIDASI SUHU
        ]);

        DB::transaction(function () use ($request, $id) {
            // Menggunakan lockForUpdate() agar aman jika ada akses bersamaan
            $material = RawMaterial::lockForUpdate()->findOrFail($id);

            // 1. Simpan stok fisik saat ini SEBELUM ditambahkan
            $stokSebelum = $material->current_stock;

            // 2. Tambahkan kuantitas baru ke master stok
            $material->current_stock = $material->current_stock + $request->qty_masuk;
            $material->save();

            // 3. Catat log inbound beserta nilai stok_sebelum, lokasi, dan suhu
            InboundLog::create([
                'raw_material_id' => $material->id,
                'stok_sebelum' => $stokSebelum,
                'qty_masuk' => $request->qty_masuk,
                'tanggal_masuk' => $request->tanggal_masuk,
                'no_dokumen' => $request->no_dokumen,
                'lokasi_simpan' => $request->lokasi_simpan, // <-- SIMPAN DATA LOKASI
                'suhu_masuk' => $request->suhu_masuk,       // <-- SIMPAN DATA SUHU
            ]);
        });

        return back()->with('success', 'Stok Fisik, Lokasi & Suhu Penerimaan berhasil dicatat.');
    }
}
