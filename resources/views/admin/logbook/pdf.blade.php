<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Logbook Bulanan UP TKJ</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px double #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
            font-size: 16px;
        }
        .header h3 {
            margin: 5px 0 0;
            font-weight: normal;
            font-size: 12px;
            color: #666;
        }
        .meta-info {
            margin-bottom: 15px;
            font-size: 11px;
        }
        .meta-info table {
            width: 100%;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .report-table th, .report-table td {
            border: 1px solid #666;
            padding: 6px 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .signature-section {
            margin-top: 30px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border: none;
        }
        .signature-table td {
            border: none;
            width: 50%;
            text-align: center;
            padding-top: 10px;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Logbook Harian Unit Produksi (UP) TKJ</h2>
        <h3>SMK Wisata Indonesia (Wistek)</h3>
        <small>Jl. Lenteng Agung Raya Gg. Langgar No.1, Kebagusan, Ps. Minggu, Jakarta Selatan</small>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td style="width: 10%;"><strong>Periode</strong></td>
                <td style="width: 40%;">: {{ $namaBulan }} {{ $tahun }}</td>
                <td style="width: 15%; text-align: right;"><strong>Tanggal Cetak</strong></td>
                <td style="width: 35%;">: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Kategori</strong></td>
                <td>: Rekap Shift & Logbook Harian</td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 3%;">No</th>
                <th rowspan="2" style="width: 10%;">Tanggal</th>
                <th rowspan="2" style="width: 10%;">Kas Awal</th>
                <th rowspan="2" style="width: 10%;">Kas Akhir</th>
                <th colspan="3">Omzet Shift 1 Pagi</th>
                <th colspan="3">Omzet Shift 2 Siang</th>
                <th rowspan="2" style="width: 12%;">Total Omzet</th>
                <th rowspan="2" style="width: 7%;">Kertas</th>
                <th rowspan="2" style="width: 10%;">Kondisi Mesin</th>
            </tr>
            <tr>
                <th>Print</th>
                <th>Kopi</th>
                <th>Jilid</th>
                <th>Print</th>
                <th>Kopi</th>
                <th>Jilid</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalKasAwal = 0;
                $totalKasAkhir = 0;
                $grandTotalOmzet = 0;
                $i = 1;
            @endphp
            @forelse($logbooks as $logbook)
                @php
                    $s1 = $logbook->details->where('shift_id', 1)->first();
                    $s2 = $logbook->details->where('shift_id', 2)->first();
                    $totalHarian = ($s1?->total_uang ?? 0) + ($s2?->total_uang ?? 0);
                    
                    $totalKasAwal += $logbook->kas_awal;
                    $totalKasAkhir += $logbook->kas_akhir ?? 0;
                    $grandTotalOmzet += $totalHarian;
                @endphp
                <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td class="text-center">{{ $logbook->tanggal->format('d/m/Y') }}</td>
                    <td class="text-right">Rp {{ number_format($logbook->kas_awal, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $logbook->kas_akhir ? 'Rp ' . number_format($logbook->kas_akhir, 0, ',', '.') : '-' }}</td>
                    
                    {{-- Shift 1 --}}
                    <td class="text-center">{{ $s1?->jumlah_print ?? 0 }}</td>
                    <td class="text-center">{{ $s1?->jumlah_fotokopi ?? 0 }}</td>
                    <td class="text-center">{{ $s1?->jumlah_jilid ?? 0 }}</td>
                    
                    {{-- Shift 2 --}}
                    <td class="text-center">{{ $s2?->jumlah_print ?? 0 }}</td>
                    <td class="text-center">{{ $s2?->jumlah_fotokopi ?? 0 }}</td>
                    <td class="text-center">{{ $s2?->jumlah_jilid ?? 0 }}</td>
                    
                    <td class="text-right fw-bold text-primary">Rp {{ number_format($totalHarian, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $logbook->stok_kertas ?? '-' }}</td>
                    <td>{{ $logbook->status_mesin ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center py-4">Tidak ada data logbook untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if($logbooks->count() > 0)
            <tfoot>
                <tr style="background-color: #e9e9e9; font-weight: bold;">
                    <td colspan="2" class="text-center">TOTAL</td>
                    <td class="text-right">Rp {{ number_format($totalKasAwal, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalKasAkhir, 0, ',', '.') }}</td>
                    <td colspan="6" class="text-center">-</td>
                    <td class="text-right">Rp {{ number_format($grandTotalOmzet, 0, ',', '.') }}</td>
                    <td colspan="2" class="text-center"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    Menyetujui,<br>
                    <strong>Kepala Program Keahlian TKJ</strong>
                    <div class="signature-space"></div>
                    ( ............................................ )
                </td>
                <td>
                    Jakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Pembina Unit Produksi TKJ</strong>
                    <div class="signature-space"></div>
                    ( ............................................ )
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
