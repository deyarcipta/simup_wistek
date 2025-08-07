<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Sisa Hasil Usaha (SHU)</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        h2, h4 { margin: 0; }
    </style>
</head>
<body>

    <h2 style="text-align: center;">Laporan Sisa Hasil Usaha (SHU)</h2>
    <h4 style="text-align: center;">Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</h4>

    {{-- Ringkasan --}}
    <p><strong>Total Pemasukan:</strong> Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
    <p><strong>Total Pengeluaran:</strong> Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
    <p><strong>Sisa Hasil Usaha (SHU):</strong> Rp {{ number_format($shu, 0, ',', '.') }}</p>

    {{-- Tabel Pembagian --}}
    <table>
        <thead>
            <tr>
                <th>Penerima</th>
                <th class="text-center">Persentase</th>
                <th class="text-end">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pembagian as $row)
                <tr>
                    <td>{{ $row['penerima'] }}</td>
                    <td class="text-center">{{ $row['persentase'] }}%</td>
                    <td class="text-end">{{ number_format($row['nominal'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td><strong>Total</strong></td>
                <td class="text-center"><strong>100%</strong></td>
                <td class="text-end"><strong>{{ number_format($shu, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
