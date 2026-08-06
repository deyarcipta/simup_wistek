<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kehadiran Operator</title>
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
        }
        .bg-success {
            background-color: #d4edda;
            color: #155724;
        }
        .bg-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .bg-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Kehadiran & Jam Kerja Operator</h2>
        <p>Unit Produksi (UP) - Filter Shift: {{ $namaShift }} | Dicetak pada: {{ now()->format('d/m/Y H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 25%">Operator</th>
                <th style="width: 15%">Shift</th>
                <th style="width: 13%">Check-In</th>
                <th style="width: 13%">Check-Out</th>
                <th style="width: 14%">Status Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $index => $detail)
                @php
                    $logbook = $detail->logbook;
                    $lateness = $detail->getLatenessInfo();
                    
                    // Tentukan check-in
                    $checkinTime = $detail->waktu_mulai ?? ($detail->shift_id == 1 ? $logbook->created_at : $detail->created_at);

                    // Tentukan jam checkout
                    $checkoutTime = null;
                    $isCheckedOut = false;
                    
                    if ($detail->shift_id == 1) {
                        $checkoutTime = $detail->created_at;
                        $isCheckedOut = true;
                    } elseif ($detail->shift_id == 2) {
                        if (in_array($logbook->status, ['shift_2_selesai', 'tutup_up'])) {
                            $checkoutTime = $detail->updated_at;
                            $isCheckedOut = true;
                        }
                    } elseif ($detail->shift_id == 3) {
                        if ($logbook->status === 'tutup_up') {
                            $checkoutTime = $detail->updated_at;
                            $isCheckedOut = true;
                        }
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $logbook->tanggal->format('d/m/Y') }}</td>
                    <td>
                        <strong>{{ $detail->user->name }}</strong><br>
                        <span style="color: #777; font-size: 9px;">{{ $detail->user->email }}</span>
                    </td>
                    <td>{{ $detail->shift->nama_shift }}</td>
                    <td class="text-center">{{ $checkinTime ? $checkinTime->format('H:i') : '-' }} WIB</td>
                    <td class="text-center">
                        @if($isCheckedOut && $checkoutTime)
                            {{ $checkoutTime->format('H:i') }} WIB
                        @else
                            Sedang Bertugas
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $lateness['is_late'] ? 'bg-danger' : 'bg-success' }}">
                            {{ $lateness['status_text'] }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
