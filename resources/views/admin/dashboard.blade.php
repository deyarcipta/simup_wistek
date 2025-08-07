@extends('admin.layouts.app')

@section('title', 'Dashboard SIMUP')

@section('content')
<div class="row">

    {{-- Pendapatan Bulan Ini --}}
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-success border-4">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-success">
                    <i class="bx bx-money-withdraw" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-muted">Pendapatan Bulan Ini</h6>
                    <h4 class="mb-0 fw-bold">Rp {{ number_format($pendapatanBulanIni,0,',','.') }}</h4>
                    <small class="text-success"><i class="bx bx-up-arrow-alt"></i> +{{ $persentasePendapatan }}% dari bulan lalu</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Pengeluaran Bulan Ini --}}
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-danger border-4">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-danger">
                    <i class="bx bx-credit-card" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-muted">Pengeluaran Bulan Ini</h6>
                    <h4 class="mb-0 fw-bold">Rp {{ number_format($pengeluaranBulanIni,0,',','.') }}</h4>
                    <small class="text-danger"><i class="bx bx-down-arrow-alt"></i> {{ $persentasePengeluaran }}% dari bulan lalu</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Laba / Rugi --}}
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-primary border-4">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-primary">
                    <i class="bx bx-line-chart" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-muted">Laba / Rugi</h6>
                    <h4 class="mb-0 fw-bold">Rp {{ number_format($labaRugi,0,',','.') }}</h4>
                    <small class="{{ $labaRugi >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $labaRugi >= 0 ? 'Laba' : 'Rugi' }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Transaksi --}}
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-warning border-4">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-warning">
                    <i class="bx bx-receipt" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-muted">Total Transaksi</h6>
                    <h4 class="mb-0 fw-bold">{{ $totalTransaksi }}</h4>
                    <small class="text-muted text-warning">Bulan ini</small>
                </div>
            </div>
        </div>
    </div>

</div>


<div class="row">
    {{-- Grafik Penjualan --}}
    <div class="col-lg-8 mb-3">
        <div class="card shadow">
            <div class="card-header">
                Grafik Penjualan
            </div>
            <div class="card-body">
                <canvas id="grafikPenjualan" height="120"></canvas>
            </div>
        </div>
    </div>

    {{-- Stok Menipis --}}
    <div class="col-lg-4 mb-3">
        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                Stok Menipis
            </div>
            <ul class="list-group list-group-flush">
                @forelse($stokMenipis as $item)
                    <li class="list-group-item d-flex justify-content-between">
                        {{ $item->nama_barang }}
                        <span class="badge bg-danger">{{ $item->stok }}</span>
                    </li>
                @empty
                    <li class="list-group-item">Semua stok aman</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="row">
    {{-- Transaksi Terbaru --}}
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header">
                Transaksi Terbaru
            </div>
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
                        @foreach($transaksiTerbaru as $trx)
                        <tr>
                            <td>{{ $trx->kode_transaksi }}</td>
                            <td>{{ $trx->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $trx->nama_pembeli }}</td>
                            <td>Rp {{ number_format($trx->total,0,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('transaksi.index') }}" class="btn btn-primary btn-sm">Lihat Semua</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var ctx = document.getElementById('grafikPenjualan').getContext('2d');
var chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($bulanPenjualan) !!},
        datasets: [{
            label: 'Penjualan',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: '#36A2EB',
            data: {!! json_encode($dataPenjualan) !!}
        }]
    }
});
</script>
@endpush
