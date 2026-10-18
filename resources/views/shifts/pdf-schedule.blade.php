<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Matriks Jadwal Shift Tim - PT Hokben Plant 8</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            margin: 0;
        }

        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 10px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .btn-print {
            background: #0066cc;
            color: #fff;
            border: none;
            padding: 6px 16px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }

        .kop {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .kop h2 {
            margin: 0;
            color: #0066cc;
            font-size: 14pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 0.5pt solid #000;
            padding: 6px 8px;
            font-size: 8.5pt;
            text-align: center;
        }

        th {
            background-color: #0066cc;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
        }

        .text-left {
            text-align: left;
        }

        .bg-masuk {
            background-color: #e6f4ea;
            font-weight: bold;
        }

        .text-off {
            color: #dc2626;
            font-weight: bold;
        }

        .footer-rekap {
            background-color: #f1f5f9;
            font-weight: bold;
        }

        .count-zero {
            background-color: #ffe6e6;
            color: #dc2626;
        }

        .count-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .count-ok {
            background-color: #d1fae5;
            color: #065f46;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="no-print-bar">
        <span><b>Matriks Jadwal Shift Tim</b> (Gunakan Landscape A4)</span>
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
    </div>

    <div class="kop">
        <div>
            <h2>PT HOKBEN PLANT 8</h2>
            <p style="margin: 3px 0 0; font-weight: bold; font-size: 11pt;">MATRIKS JADWAL SHIFT KERJA TIM</p>
        </div>
        <div style="text-align: right; font-size: 8.5pt;">
            Periode Pekan: <b>{{ $startDate }}</b> s/d <b>{{ $dates[6] }}</b><br>
            Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">NO</th>
                <th style="width: 180px;">NAMA KARYAWAN</th>
                <th style="width: 100px;">BAGIAN</th>
                @foreach ($dates as $d)
                    <th>TGL {{ \Carbon\Carbon::parse($d)->day }}</th>
                @endforeach
                <th style="width: 90px; background-color: #004080;">TOTAL KERJA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $idx => $emp)
                @php $totalKerja = 0; @endphp
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td class="text-left" style="font-weight: bold;">{{ $emp->name }}</td>
                    <td>{{ $emp->employee?->department ?? 'Plant 8' }}</td>
                    @foreach ($dates as $d)
                        @php
                            $shiftData = $emp->employeeShifts->firstWhere('date', $d);
                            $isMasuk = $shiftData && $shiftData->shift_id;
                            if ($isMasuk) {
                                $totalKerja++;
                            }
                        @endphp
                        <td class="{{ $isMasuk ? 'bg-masuk' : 'text-off' }}">
                            {{ $isMasuk ? ($shiftData->shift ? strtoupper(substr($shiftData->shift->name, 0, 3)) : 'MSK') : 'OFF' }}
                        </td>
                    @endforeach
                    <td style="font-weight: bold; background-color: #f1f5f9;">{{ $totalKerja }} Hari</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="footer-rekap">
                <td colspan="3" style="text-align: right; padding-right: 12px;">TOTAL MASUK KERJA (PER HARI):</td>
                @foreach ($dates as $d)
                    @php
                        $c = $dailyCounts[$d];
                        $class = $c == 0 ? 'count-zero' : ($c < 5 ? 'count-warning' : 'count-ok');
                    @endphp
                    <td class="{{ $class }}">{{ $c }}</td>
                @endforeach
                <td style="background-color: #e2e8f0;"></td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
