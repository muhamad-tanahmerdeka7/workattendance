<!DOCTYPE html>
<html>

<head>
    <title>Laporan Bahan Keluar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f3f4f6;
        }

        .text-center {
            text-align: center;
        }

        .signature-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .signature-table td {
            border: none;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 5px;
        }

        .signature-space {
            height: 60px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Riwayat Bahan Keluar / Produksi</h2>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal Keluar</th>
                <th>No. DO</th>
                <th>Kode Item</th>
                <th>Nama Material</th>
                <th class="text-center">Qty Keluar</th>
                <th>Unit </th>
                <th>Lokasi Tujuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($outbounds as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->tanggal_keluar }}</td>
                    <td>{{ $item->no_dokumen ?? '-' }}</td>
                    <td>{{ $item->rawMaterial->item_code ?? 'N/A' }}</td>
                    <td>{{ $item->rawMaterial->name ?? 'Terhapus' }}</td>
                    <td class="text-center">{{ (float) $item->qty_keluar }}</td>
                    <td>{{ $item->rawMaterial->unit ?? 'N/A' }}</td>
                    <td>{{ $item->lokasi_tujuan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data pengeluaran bahan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                Dibuat oleh,
                <div class="signature-space"></div>
                ( ........................................ )
            </td>
            <td>
                Diperiksa oleh,
                <div class="signature-space"></div>
                ( ........................................ )
            </td>
            <td>
                Disetujui oleh,
                <div class="signature-space"></div>
                ( ........................................ )
            </td>
        </tr>
    </table>
</body>

</html>
