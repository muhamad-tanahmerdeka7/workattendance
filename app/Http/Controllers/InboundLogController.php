<?php

namespace App\Http\Controllers;

use App\Models\InboundLog;
use App\Models\RawMaterial; // Pastikan model RawMaterial di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import untuk Database Transaction
use Inertia\Inertia;

class InboundLogController extends Controller
{
    public function index(Request $request)
    {
        // Tarik data riwayat masuk, panggil relasi rawMaterial, urutkan dari yang terbaru
        $query = InboundLog::with('rawMaterial')->orderBy('tanggal_masuk', 'desc')->orderBy('created_at', 'desc');

        // 1. Filter Pencarian (Nama Barang, Kode Item, No Dokumen, atau Lokasi Simpan)
        if ($request->filled('search')) {
            $search = $request->search;

            // Dibungkus function($q) agar operator OR tidak merusak query utama
            $query->where(function ($q) use ($search) {
                $q->whereHas('rawMaterial', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                        ->orWhere('item_code', 'like', "%{$search}%");
                })
                    ->orWhere('no_dokumen', 'like', "%{$search}%")
                    ->orWhere('lokasi_simpan', 'like', "%{$search}%"); // Pencarian berdasarkan chiller/blast
            });
        }

        // 2. Filter Rentang Tanggal (Start Date & End Date)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_masuk', [$request->start_date, $request->end_date]);
        }

        return Inertia::render('Inbound/Index', [
            'inbounds' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only(['search', 'start_date', 'end_date']),
        ]);
    }

    /**
     * Method baru untuk memproses simpan data dari Form Vue
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'qty_masuk' => 'required|numeric|min:0.1',
            'tanggal_masuk' => 'required|date',
            'no_dokumen' => 'nullable|string|max:255',
            'lokasi_simpan' => 'required|string|max:255',
            'suhu_masuk' => 'required|numeric',
        ]);

        // 2. Gunakan DB Transaction agar stok dan log tersimpan bersamaan dengan aman
        DB::transaction(function () use ($validated, $request) {

            // Ambil master bahan baku dan kunci row sementara (lockForUpdate) mencegah bentrok antar admin
            $rawMaterial = RawMaterial::lockForUpdate()->findOrFail($request->raw_material_id);

            // Rekam stok sebelum ditambahkan (sesuaikan 'stok' dengan nama kolom di tabel raw_materials Anda)
            $validated['stok_sebelum'] = $rawMaterial->stok ?? 0;

            // Simpan riwayat barang masuk
            InboundLog::create($validated);

            // Tambahkan stok di master data bahan baku
            $rawMaterial->increment('stok', $request->qty_masuk);
        });

        // 3. Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Bahan masuk beserta lokasi dan suhu berhasil dicatat!');
    }
}
