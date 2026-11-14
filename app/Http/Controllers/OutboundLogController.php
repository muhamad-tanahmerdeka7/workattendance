<?php

namespace App\Http\Controllers;

use App\Models\OutboundLog;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf; // <-- TAMBAHAN UNTUK PDF

class OutboundLogController extends Controller
{
    public function index(Request $request)
    {
        // Tarik data riwayat keluar, panggil relasi rawMaterial, urutkan dari yang terbaru
        $query = OutboundLog::with('rawMaterial')->orderBy('tanggal_keluar', 'desc')->orderBy('created_at', 'desc');

        // 1. Filter Pencarian (Nama Barang, Kode Item, No Dokumen, atau Lokasi Tujuan)
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('rawMaterial', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                        ->orWhere('item_code', 'like', "%{$search}%");
                })
                    ->orWhere('no_dokumen', 'like', "%{$search}%")
                    ->orWhere('lokasi_tujuan', 'like', "%{$search}%");
            });
        }

        // 2. Filter Rentang Tanggal (Start Date & End Date)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_keluar', [$request->start_date, $request->end_date]);
        }

        return Inertia::render('Outbound/Index', [
            'outbounds' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only(['search', 'start_date', 'end_date']),

            // TAMBAHAN AGAR DROPDOWN BAHAN BAKU MUNCUL DI VUE
            'raw_materials' => RawMaterial::orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Method untuk memproses pengeluaran bahan baku produksi
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'qty_keluar' => 'required|numeric|min:0.1',
            'tanggal_keluar' => 'required|date',
            'lokasi_tujuan' => 'required|string|max:255', // (Proses, Assembling, Pembekuan)
            'no_dokumen' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        // 2. Gunakan DB Transaction
        DB::transaction(function () use ($validated, $request) {

            // Ambil master bahan baku dan kunci row sementara (lockForUpdate)
            $rawMaterial = RawMaterial::lockForUpdate()->findOrFail($request->raw_material_id);

            // PASTIKAN STOK CUKUP (Pencegahan Stok Minus)
            // MENGGUNAKAN 'current_stock' SESUAI DATABASE ANDA
            if ($rawMaterial->current_stock < $request->qty_keluar) {
                throw ValidationException::withMessages([
                    'qty_keluar' => 'Stok tidak mencukupi! Sisa stok saat ini hanya: '.($rawMaterial->current_stock ?? 0),
                ]);
            }

            // Hitung variabel stok menggunakan 'current_stock'
            $stokSebelum = $rawMaterial->current_stock ?? 0;

            // PERBAIKAN SYNTAX PENGURANGAN STOK YANG SEBELUMNYA TERPOTONG
            $sisaStok = $stokSebelum - $request->qty_keluar;

            // Masukkan data stok ke array validasi untuk disimpan ke log
            $validated['stok_sebelum'] = $stokSebelum;
            $validated['sisa_stok'] = $sisaStok;

            // Simpan riwayat barang keluar
            OutboundLog::create($validated);

            // KURANGI STOK DI KOLOM 'current_stock'
            $rawMaterial->decrement('current_stock', $request->qty_keluar);
        });

        // 3. Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Bahan berhasil dikeluarkan untuk produksi!');
    }
    //pdfpublic function printPdf(Request $request)


    public function printPdf(Request $request)
    {
        // Ambil data bahan keluar
        $query = OutboundLog::with('rawMaterial')->orderBy('tanggal_keluar', 'desc');

        // Jika user melakukan filter tanggal, PDF juga akan terfilter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_keluar', [$request->start_date, $request->end_date]);
        }

        $outbounds = $query->get();

        // PERHATIKAN BARIS INI: Ubah 'pdf.outbounds' menjadi 'outbound.outbounds'
        $pdf = Pdf::loadView('outbound.outbounds', ['outbounds' => $outbounds]);

        // Menampilkan PDF di tab baru
        return $pdf->stream('Laporan_Bahan_Keluar.pdf');
    }
}