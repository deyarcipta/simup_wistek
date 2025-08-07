@extends('admin.layouts.app')

@section('title', 'Laporan Sisa Hasil Usaha (SHU)')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Filter Tanggal & Download --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('laporan.shu') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('laporan.shu.download', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                           class="btn btn-success w-100">
                            <i class="bx bx-download"></i> Download
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Ringkasan SHU --}}
        <div class="card mb-3">
            <div class="card-body">
                <p><strong>Total Pemasukan:</strong> Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                <p><strong>Total Pengeluaran:</strong> Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                <p><strong>Sisa Hasil Usaha (SHU):</strong> Rp {{ number_format($shu, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Tabel Pembagian SHU --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Pembagian SHU</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
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
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td class="text-center">100%</td>
                            <td class="text-end">{{ number_format($shu, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
