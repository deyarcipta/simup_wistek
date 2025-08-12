<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Buku Besar Keuangan</title>
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

    <h2 style="text-align: center;">Buku Besar Keuangan</h2>
    <h4 style="text-align: center;">Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</h4>

    {{-- Saldo Awal --}}
    <p><strong>Saldo Akhir:</strong> Rp {{ number_format($bukuBesar->last()['saldo'] ?? 0, 0, ',', '.') }}</p>

    <table>
        <thead>
            <tr>
                <th class="text-center">Tanggal</th>
                <th>Keterangan</th>
                <th class="text-end">Debit</th>
                <th class="text-end">Kredit</th>
                <th class="text-end">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bukuBesar as $row)
                <tr>
                    <td class="text-center">{{ $row['tanggal'] }}</td>
                    <td>{{ $row['keterangan'] }}</td>
                    <td class="text-end">{{ $row['debit'] ? number_format($row['debit'], 0, ',', '.') : '-' }}</td>
                    <td class="text-end">{{ $row['kredit'] ? number_format($row['kredit'], 0, ',', '.') : '-' }}</td>
                    <td class="text-end">{{ number_format($row['saldo'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-end"><strong>Saldo Akhir</strong></td>
                <td class="text-end"><strong>{{ number_format($bukuBesar->last()['saldo'] ?? 0, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
