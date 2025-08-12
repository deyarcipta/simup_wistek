@extends('admin.layouts.app')

@section('title', 'Rekap Transaksi')

@section('content')
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
