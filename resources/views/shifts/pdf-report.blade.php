<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Shift - PT Hokben Plant 8</title>
    <style>
        /* Pengaturan Cetak & Halaman (A4 Landscape) */
        @page {
            size: A4 landscape;
            margin: 15mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Tombol Cetak / Simpan PDF (Hanya Muncul di Layar) */
        .no-print-bar {
            background-color: #1e293b;
            color: #ffffff;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-print {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            font-size: 9pt;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-print:hover {
            background-color: #1d4ed8;
        }

        /* Kop Surat & Header Laporan */
        .kop-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .company-title h1 {
            font-size: 16pt;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .company-title p {
            font-size: 10pt;
            font-weight: 600;
            color: #0284c7;
            margin: 2px 0 0 0;
        }

        .report-info {
            text-align: right;
            font-size: 8.5pt;
            color: #475569;
        }

        .report-info table {
            margin-left: auto;
            border-collapse: collapse;
        }

        .report-info td {
            padding: 2px 6px;
            border: none;
        }

        /* Ringkasan Statistik (Summary Cards) */
        .summary-cards {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .card {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            background-color: #f8fafc;
            text-align: center;
        }

        .card .value {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
        }

        .card .label {
            font-size: 7.5pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* Tabel Utama */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        table.data-table th {
            background-color: #0066cc;
            color: #ffffff;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px 8px;
            border: 1px solid #0f172a;
            text-align: center;
            vertical-align: middle;
        }

        table.data-table td {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            font-size: 9pt;
            vertical-align: middle;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        /* Badge & Aksen Warna */
        .badge-libur {
            color: #dc2626;
            font-weight: bold;
        }

        .bg-total-hadir {
            background-color: #e6f4ea;
            font-weight: bold;
            text-align: center;
        }

        /* Kolom Tanda Tangan (Approval Area) */
        .signature-section {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            border: none;
            width: 33.3%;
            text-align: center;
            vertical-align: top;
            font-size: 8.5pt;
        }

        .signature-box {
            height: 60px;
        }

        /* Pengaturan Cetak Media (Sembunyikan Elemen Non-Cetak) */
        @media print {
            .no-print-bar {
                display: none !important;
            }

            body {
                background: none;
                margin: 0;
            }

            table.data-table th {
                background-color: #0066cc !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .bg-total-hadir {
                background-color: #e6f4ea !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Bar Navigasi Cetak (Tidak Muncul Saat Dicetak) -->
    <div class="no-print-bar">
        <div>
            <strong>Preview Laporan PDF Rekapitulasi Shift</strong>
            <span style="font-size: 8pt; opacity: 0.8; margin-left: 10px;">Gunakan browser untuk menyimpan sebagai PDF (Landscape A4)</span>
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">
                🖨️ Cetak / Simpan Dokumen PDF
            </button>
        </div>
    </div>

    <!-- Kop Surat Resmi Perusahaan -->
    <div class="kop-header">
        <div class="company-title">
            <h1>PT HOKI DELTA MANUSIA (HOKBEN)</h1>
            <p>PLANT 8 — LAPORAN REKAPITULASI SHIFT KERJA KARYAWAN</p>
        </div>
        <div class="report-info">
            <table>
                <tr>
                    <td style="text-align: right; font-weight: bold;">Periode Closing:</td>
                    <td><b>{{ $month }}</b> (Tutup Buku Tgl 15)</td>
                </tr>
                <tr>
                    <td style="text-align: right; font-weight: bold;">Tanggal Cetak:</td>
                    <td>{{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Tabel Data Rekapitulasi Shift Karyawan -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th style="width: 100px;">NIK</th>
                <th style="width: 220px;">Nama Karyawan</th>
                <th style="width: 120px;">Departemen</th>
                <th style="width: 100px;">Shift Pagi</th>
                <th style="width: 100px;">Shift Siang</th>
                <th style="width: 100px;">Shift Malam</th>
                <th style="width: 110px;">Total Libur (Off)</th>
                <th style="width: 140px; background-color: #0052a3;">Total Hadir Perbulan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandPagi = 0;
                $grandSiang = 0;
                $grandMalam = 0;
                $grandLibur = 0;
                $grandHadir = 0;
            @endphp

            @forelse($subordinates as $index => $sub)
                @php
                    $pagi = $sub->employeeShifts->filter(fn ($s) => $s->shift && stripos($s->shift->name, 'pagi') !== false)->count();
                    $siang = $sub->employeeShifts->filter(fn ($s) => $s->shift && stripos($s->shift->name, 'siang') !== false)->count();
                    $malam = $sub->employeeShifts->filter(fn ($s) => $s->shift && stripos($s->shift->name, 'malam') !== false)->count();
                    $libur = $sub->employeeShifts->filter(fn ($s) => is_null($s->shift_id) || (isset($s->shift->name) && stripos($s->shift->name, 'libur') !== false))->count();

                    $totalHadir = $pagi + $siang + $malam;

                    $grandPagi += $pagi;
                    $grandSiang += $siang;
                    $grandMalam += $malam;
                    $grandLibur += $libur;
                    $grandHadir += $totalHadir;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-bold">{{ $sub->employee?->employee_code ?? '-' }}</td>
                    <td class="text-left font-bold">{{ $sub->name }}</td>
                    <td class="text-center">{{ $sub->employee?->department ?? 'PLANT 8' }}</td>
                    <td class="text-center">{{ $pagi }}</td>
                    <td class="text-center">{{ $siang }}</td>
                    <td class="text-center">{{ $malam }}</td>
                    <td class="text-center badge-libur">{{ $libur }}</td>
                    <td class="bg-total-hadir">{{ $totalHadir }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada data rekapitulasi shift untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($subordinates) > 0)
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="4" class="text-center" style="padding: 10px;">TOTAL REKAPITULASI KESELURUHAN TIM</td>
                <td class="text-center">{{ $grandPagi }}</td>
                <td class="text-center">{{ $grandSiang }}</td>
                <td class="text-center">{{ $grandMalam }}</td>
                <td class="text-center badge-libur">{{ $grandLibur }}</td>
                <td class="bg-total-hadir" style="background-color: #d1fae5; font-size: 10pt;">{{ $grandHadir }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Area Tanda Tangan & Lembar Pengesahan -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    Dibuat Oleh,<br>
                    <b>Admin / Staff HR Plant 8</b>
                    <div class="signature-box"></div>
                    <b>( _________________________ )</b>
                </td>
                <td>
                    Dipelajari Oleh,<br>
                    <b>Line Head / Supervisor</b>
                    <div class="signature-box"></div>
                    <b>( {{ Auth::user()->name ?? 'Line Head Plant 8' }} )</b>
                </td>
                <td>
                    Disetujui Oleh,<br>
                    <b>Manager Plant 8</b>
                    <div class="signature-box"></div>
                    <b>( _________________________ )</b>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
