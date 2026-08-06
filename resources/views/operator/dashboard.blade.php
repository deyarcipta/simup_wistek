@extends('operator.layouts.app')

@section('title', 'Dashboard Operator')

@section('content')
<div class="container-fluid px-0 animate-fade-in-up">

    {{-- HERO WELCOME CARD --}}
    <div class="hero-welcome-card mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge hero-glass-pill px-3 py-1 rounded-pill">
                        <i class="bx bx-run me-1"></i> Operator Portal
                    </span>
                    <span class="text-white-50 small">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</span>
                </div>
                <h2 class="text-white fw-bold mb-2">Selamat Datang Kembali, {{ Auth::user()->name ?? 'Operator' }}! <span class="wave-emoji">👋</span></h2>
                <p class="text-white-50 mb-0" style="font-size: 0.95rem; max-width: 600px;">
                    Kelola operasional harian kasir, pencatatan logbook shift, serta pemantauan stok produk dan transaksi unit produksi Wistek.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ url('/operator/transaksi') }}" class="btn btn-light btn-lg rounded-pill shadow-sm text-primary fw-bold px-4">
                    <i class="bx bx-shopping-bag me-1"></i> Buka Kasir Transaksi
                </a>
            </div>
        </div>
    </div>

    {{-- STATISTIK UTAMA --}}
    <div class="row g-3 mb-4">
        {{-- Pendapatan Hari Ini --}}
        <div class="col-xl-4 col-md-6">
            <div class="modern-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.85rem;">Pendapatan Hari Ini</span>
                        <h3 class="fw-bold text-dark mb-0">Rp {{ number_format($pendapatanHariIni,0,',','.') }}</h3>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-success">
                        <i class="bx bx-money-withdraw"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="trend-badge-success">
                        <i class="bx bx-calendar"></i> Realtime POS
                    </span>
                    <span class="text-muted small">Shift Harian</span>
                </div>
            </div>
        </div>

        {{-- Total Transaksi Hari Ini --}}
        <div class="col-xl-4 col-md-6">
            <div class="modern-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.85rem;">Transaksi Hari Ini</span>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($totalTransaksiHariIni) }} <small class="fs-6 text-muted">Trx</small></h3>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-primary">
                        <i class="bx bx-receipt"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="trend-badge-primary">
                        <i class="bx bx-cart"></i> Transaksi Berhasil
                    </span>
                    <span class="text-muted small">Hari Ini</span>
                </div>
            </div>
        </div>

        {{-- Produk / Jasa Tersedia --}}
        <div class="col-xl-4 col-md-6">
            <div class="modern-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.85rem;">Produk &amp; Jasa Tersedia</span>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($totalProdukJasa) }} <small class="fs-6 text-muted">Kategori</small></h3>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-warning">
                        <i class="bx bx-layer"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="trend-badge-warning">
                        <i class="bx bx-box"></i> Katalog Aktif
                    </span>
                    <span class="text-muted small">Unit Produksi</span>
                </div>
            </div>
        </div>
    </div>

    {{-- GRAFIK TREN OMZET & RINGKASAN --}}
    <div class="row g-4 mb-4">
        {{-- Area Chart Sales Trend --}}
        <div class="col-lg-8">
            <div class="modern-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-1"><i class="bx bx-line-chart text-primary me-2"></i> Grafik Omzet Penjualan</h5>
                        <small class="text-muted">Tren pendapatan transaksi 6 bulan terakhir</small>
                    </div>
                    <span class="badge bg-label-primary px-3 py-2 rounded-pill fw-semibold">
                        <i class="bx bx-pulse me-1"></i> Data Realtime
                    </span>
                </div>
                <div style="height: 320px;">
                    <canvas id="operatorSalesChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Stok Menipis Sidebar Widget --}}
        <div class="col-lg-4">
            <div class="modern-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bx bx-error text-danger me-2"></i> Stok Menipis</h5>
                    <span class="badge bg-danger text-white rounded-pill px-2 py-1">{{ $stokMenipis->count() }} Item</span>
                </div>
                <p class="text-muted small mb-3">Barang dengan sisa stok &le; 5 unit perlu segera ditambah.</p>

                <div class="list-group list-group-flush border-top">
                    @forelse($stokMenipis as $item)
                        <div class="list-group-item px-0 py-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-label-danger p-2 rounded-3 text-danger">
                                    <i class="bx bx-box fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold text-dark" style="font-size: 0.9rem;">{{ $item->nama_barang }}</h6>
                                    <small class="text-muted">Sisa: <strong class="text-danger">{{ $item->stok }} {{ $item->satuan ?? 'unit' }}</strong></small>
                                </div>
                            </div>
                            <span class="badge bg-label-danger rounded-pill">Restock</span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bx bx-check-shield text-success mb-2" style="font-size: 2.5rem;"></i>
                            <p class="mb-0 fw-semibold text-dark" style="font-size: 0.9rem;">Semua Stok Aman</p>
                            <small class="text-muted">Tidak ada barang yang menipis.</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- TRANSAKSI TERBARU TABLE --}}
    <div class="modern-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold text-dark mb-1"><i class="bx bx-time-five text-primary me-2"></i> Transaksi Terbaru</h5>
                <small class="text-muted">5 Transaksi penjualan paling baru</small>
            </div>
            <a href="{{ route('operator.transaksi.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                Lihat Semua Kasir <i class="bx bx-right-arrow-alt ms-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">Kode Transaksi</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Nama Pembeli</th>
                        <th class="py-3 text-end">Total Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksiTerbaru as $trx)
                    <tr>
                        <td class="fw-bold text-primary py-3">
                            <i class="bx bx-barcode-reader me-1"></i> {{ $trx->kode_transaksi }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $trx->nama_pembeli ?: 'Umum / Non-Member' }}</span>
                        </td>
                        <td class="text-end fw-bold text-success fs-6">
                            Rp {{ number_format($trx->total,0,',','.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bx bx-receipt mb-2" style="font-size: 2.5rem;"></i>
                            <p class="mb-0">Belum ada transaksi hari ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- SCRIPT CHART JS --}}
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('operatorSalesChart');
    if (!ctx) return;

    const labels = {!! json_encode($bulanPenjualan) !!};
    const dataValues = {!! json_encode($dataPenjualan) !!};

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(79, 70, 229, 0.35)');
    gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Omzet Penjualan (Rp)',
                data: dataValues,
                borderColor: '#4F46E5',
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#4F46E5',
                pointBorderColor: '#FFFFFF',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1E293B',
                    titleFont: { family: 'Plus Jakarta Sans', size: 13 },
                    bodyFont: { family: 'Plus Jakarta Sans', size: 14, weight: 'bold' },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return ' Omzet: Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 12 }, color: '#64748B' }
                },
                y: {
                    grid: { color: '#E2E8F0', borderDash: [4, 4] },
                    ticks: {
                        font: { family: 'Plus Jakarta Sans', size: 11 },
                        color: '#64748B',
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value/1000000) + ' Jt';
                            if (value >= 1000) return 'Rp ' + (value/1000) + ' Rb';
                            return 'Rp ' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
