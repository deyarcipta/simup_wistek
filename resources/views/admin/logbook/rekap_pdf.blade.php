<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekap Kehadiran Operator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #2b579a;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 3px;
            background-color: #e2f0d9;
            color: #385723;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Rekapitulasi Kehadiran Operator</h2>
        <p>Unit Produksi (UP) | Dicetak pada: {{ now()->format('d/m/Y H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%" class="text-center">No</th>
                <th style="width: 35%">Nama Operator</th>
                <th style="width: 20%" class="text-center">Jumlah Shift Dijaga</th>
                <th style="width: 40%">Tanggal Menjaga</th>
            </tr>
        </thead>
        <tbody>
            @php $idx = 1; @endphp
            @foreach($rekapOperator as $item)
                <tr>
                    <td class="text-center">{{ $idx++ }}</td>
                    <td>
                        <strong>{{ $item['user']->name }}</strong><br>
                        <span style="color: #777; font-size: 9px;">{{ $item['user']->email }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge">{{ $item['jumlah_shift'] }} Shift</span>
                    </td>
                    <td>{{ $item['tanggal_menjaga'] ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
