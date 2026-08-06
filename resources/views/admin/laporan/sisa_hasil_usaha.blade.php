@extends('admin.layouts.app')

@section('title', 'Laporan Sisa Hasil Usaha (SHU)')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h5 class="text-white-50 mb-1"><i class="bx bx-calculator me-1"></i> Laporan Laba Rugi</h5>
                    <h2 class="text-white mb-0 fw-bold">Sisa Hasil Usaha (SHU)</h2>
                    <p class="mb-0 mt-1 text-white-50" style="font-size: 0.85rem; max-width: 600px;">
                        Hitung laba kotor, laba bersih operasional, pembagian porsi SHU untuk sekolah/koperasi, serta alokasi bagi hasil karyawan.
                    </p>
                </div>
                <div class="d-none d-md-block text-white" style="font-size: 5rem; opacity: 0.25; line-height: 1;">
                    <i class="bx bx-calculator"></i>
                </div>
            </div>
        </div>
    </div>

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
