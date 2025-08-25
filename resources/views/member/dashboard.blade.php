@extends('member.layouts.app')

@section('title', 'Dashboard Member')

@section('content')
<div class="row">

    {{-- Saldo Total --}}
    <div class="col-lg-6 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-success border-4">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-success">
                    <i class="bx bx-wallet" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-muted">Saldo Total</h6>
                    <h4 class="fw-bold">
                        Rp {{ number_format($saldoTotal ?? 0, 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Jumlah Transaksi Hari Ini --}}
    <div class="col-lg-6 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-warning border-4">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-warning">
                    <i class="bx bx-receipt" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-muted">Total Transaksi</h6>
                    <h4 class="fw-bold">{{ $jumlahTransaksi ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">
    {{-- Kolom Transaksi --}}
    <div class="col-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Riwayat Transaksi Terbaru</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Nominal</th>
                            <th>Bonus (10–15%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiTerbaru as $trx)
                            <tr>
                                <td>{{ $trx->created_at->format('d/m/Y') }}</td>
                                <td>
                                    {{ $trx->kode_transaksi }} -
                                    @foreach($trx->details ?? [] as $d)
                                        {{ $d->produkJasa->nama ?? '-' }}
                                        ({{ $d->jumlah ?? 0 }} {{ $d->produkJasa->satuan ?? '' }})
                                        @if(!$loop->last), @endif
                                    @endforeach
                                </td>
                                <td>Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                                <td class="text-success">
                                    @php
                                        if ($trx->total < 30000) {
                                            $persen = 0.10; // 10%
                                        } elseif ($trx->total <= 50000) {
                                            $persen = 0.12; // 12%
                                        } else {
                                            $persen = 0.15; // 15%
                                        }
                                    @endphp

                                    Rp {{ number_format($trx->total * $persen, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Kolom Pencairan Saldo --}}
    <div class="col-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Riwayat Pencairan Saldo Terbaru</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pencairanSaldoTerbaru as $ps)
                            <tr>
                                <td>{{ $ps->created_at->format('d/m/Y') }}</td>
                                <td>Rp {{ number_format($ps->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Belum ada pencairan saldo</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
