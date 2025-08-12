<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Transaksi</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 6px;
        }
        th {
            background-color: #f2f2f2;
        }
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h3>Rekap Transaksi</h3>
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    <p>Tanggal Cetak: {{ $tanggalCetak }}</p>

    <table>
        <thead>
            <tr>
                <th>Kode Transaksi</th>
                <th>Tanggal</th>
                <th>Nama Pembeli</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekap as $t)
                <tr>
                    <td>{{ $t->kode_transaksi }}</td>
                    <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $t->nama_pembeli ?? '-' }}</td>
                    <td>Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total Penjualan:</strong> Rp {{ number_format($rekap->sum('total'), 0, ',', '.') }}</p>
    <p><strong>Jumlah Transaksi:</strong> {{ $rekap->count() }}</p>
</body>
</html>
