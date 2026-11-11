<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Form Standar Resep - {{ $product->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .header {
            background: #2563eb;
            color: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .header-tag {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
            margin-bottom: 6px;
        }

        .header h1 {
            margin: 0 0 5px 0;
            font-size: 18px;
        }

        .header p {
            margin: 0;
            font-size: 12px;
            opacity: 0.9;
        }

        .section-box {
            margin-bottom: 20px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            background: #fff;
        }

        th,
        td {
            padding: 6px 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background-color: #f3f4f6;
            color: #4b5563;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: 500;
            border-radius: 4px;
            background-color: #e5e7eb;
            color: #374151;
        }

        .footer {
            text-align: right;
            padding-top: 15px;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <div class="header">
        <span class="header-tag">Form Standar Resep (BOM)</span>
        <h1>{{ $product->name }}</h1>
        <p>Kode: {{ $product->item_code }} &bull; Line: {{ $product->line_production }} &bull; Output: 1 Resep =
            {{ $product->batch_yield }} {{ $product->unit }}</p>
    </div>

    @php
        // Mengelompokkan resep berdasarkan table_group secara otomatis
        $groupedRecipes = $product->recipes->groupBy('table_group');
    @endphp

    @if ($groupedRecipes->count() === 0)
        <p style="text-align: center; color: #6b7280; padding: 20px;">Belum ada komposisi bahan baku (BOM) untuk produk
            ini.</p>
    @else
        @foreach ($groupedRecipes as $groupName => $items)
            <div class="section-box">
                <div class="section-title">
                    <span>{{ $groupName }}</span>
                    <span style="font-weight: normal; color: #6b7280;">{{ $items->count() }} Item</span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 20%;">Kode Item</th>
                            <th style="width: 40%;">Nama Bahan / Material</th>
                            <th class="text-right" style="width: 15%;">Jumlah</th>
                            <th class="text-center" style="width: 10%;">Unit</th>
                            <th style="width: 15%;">Lokasi Simpan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $recipe)
                            <tr>
                                <td><strong>{{ $recipe->rawMaterial?->item_code }}</strong></td>
                                <td>
                                    {{ $recipe->rawMaterial?->name }}
                                    @if ($recipe->rawMaterial?->condition_note)
                                        <br><span
                                            style="font-size: 9px; color: #d97706;">{{ $recipe->rawMaterial->condition_note }}</span>
                                    @endif
                                </td>
                                <td class="text-right" style="color: #2563eb; font-weight: bold;">
                                    {{ number_format($recipe->quantity, 2, ',', '.') }}</td>
                                <td class="text-center">{{ $recipe->unit }}</td>
                                <td><span class="badge">{{ $recipe->rawMaterial?->storage_location }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

    <div class="footer">
        Dicetak dari Sistem ERP/WMS &bull; Dokumen Standar Resep Produksi
    </div>

</body>

</html>
