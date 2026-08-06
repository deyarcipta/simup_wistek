@extends('admin.layouts.app')

@section('title', 'Rekap Transaksi')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h5 class="text-white-50 mb-1"><i class="bx bx-receipt me-1"></i> Laporan Penjualan</h5>
                    <h2 class="text-white mb-0 fw-bold">Rekapitulasi Transaksi POS</h2>
                    <p class="mb-0 mt-1 text-white-50" style="font-size: 0.85rem; max-width: 600px;">
                        Filter dan rekap seluruh penjualan produk/jasa berdasarkan rentang tanggal, lihat total omzet, serta ekspor ke Excel.
                    </p>
                </div>
                <div class="d-none d-md-block text-white" style="font-size: 5rem; opacity: 0.25; line-height: 1;">
                    <i class="bx bx-receipt"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Rekap Transaksi</h5>

        <form method="GET" action="{{ route('transaksi.rekap') }}" class="row g-2 align-items-center">
            {{-- Tanggal mulai --}}
            <div class="col-md-4">
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-control" required>
            </div>

            {{-- Tanggal akhir --}}
            <div class="col-md-4">
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-control" required>
            </div>

            {{-- Tombol tampilkan --}}
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
            </div>

            {{-- Tombol download --}}
            <div class="col-md-2">
                <a href="{{ route('transaksi.rekap.download', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                   class="btn btn-success w-100">
                    <i class="bx bx-download"></i> Download
                </a>
            </div>
        </form>
    </div>

    <div class="card-body">
        @if(count($rekap) > 0)
            <p><strong>Total Penjualan:</strong> Rp {{ number_format($rekap->sum('total'), 0, ',', '.') }}</p>
            <p><strong>Jumlah Transaksi:</strong> {{ $rekap->count() }}</p>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Nama Pembeli</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $t)
                        <tr>
                            <td>{{ $t->kode_transaksi }}</td>
                            <td>{{ $t->tanggal->format('d-m-Y') }}</td>
                            <td>{{ $t->nama_pembeli ?? '-' }}</td>
                            <td>Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
