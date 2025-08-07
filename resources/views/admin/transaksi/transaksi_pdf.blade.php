<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h3 style="text-align: center;">Laporan Transaksi</h3>
    <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Pembeli</th>
                <th>Detail</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $trx)
                <tr>
                    <td>{{ $trx->kode_transaksi }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $trx->nama_pembeli ?? '-' }}</td>
                    <td>
                        @foreach($trx->details as $d)
                            {{ $d->produkJasa->nama }} ({{ $d->jumlah }} x Rp {{ number_format($d->harga, 0, ',', '.') }})<br>
                        @endforeach
                    </td>
                    <td>Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
