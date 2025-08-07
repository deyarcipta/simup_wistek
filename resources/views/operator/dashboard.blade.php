@extends('operator.layouts.app')

@section('title', 'Dashboard Operator')

@section('content')
<div class="row">

    {{-- Ringkasan Keuangan --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-success border-4">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-success">
                    <i class="bx bx-money-withdraw" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-muted">Pendapatan Hari Ini</h6>
                    <h4 class="fw-bold">Rp {{ number_format($pendapatanHariIni,0,',','.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-primary border-4">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-primary">
                    <i class="bx bx-receipt" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-muted">Transaksi Hari Ini</h6>
                    <h4 class="fw-bold">{{ $totalTransaksiHariIni }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-warning border-4">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-warning">
                    <i class="bx bx-box" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-muted">Produk/Jasa Tersedia</h6>
                    <h4 class="fw-bold">{{ $totalProdukJasa }}</h4>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">
    {{-- Stok Menipis --}}
    <div class="col-lg-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white fw-bold">
                Stok Menipis
            </div>
            <ul class="list-group list-group-flush">
                @forelse($stokMenipis as $item)
                    <li class="list-group-item d-flex justify-content-between">
                        {{ $item->nama_barang }}
                        <span class="badge bg-danger">{{ $item->stok }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Semua stok aman</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Transaksi Terbaru (Kiri) --}}
    <div class="col-lg-8 mb-3">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">Transaksi Terbaru</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Pembeli</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiTerbaru as $trx)
                        <tr>
                            <td>{{ $trx->kode_transaksi }}</td>
                            <td>{{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $trx->nama_pembeli ?? '-' }}</td>
                            <td>Rp {{ number_format($trx->total,0,',','.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('operator.transaksi.index') }}" class="btn btn-primary btn-sm">Lihat Semua</a>
            </div>
        </div>
    </div>
</div>
@endsection
