@extends('admin.layouts.app')

@section('title', 'Dashboard Analytics')

@section('content')
<div class="container-fluid px-0 animate-fade-in-up">

    {{-- HERO WELCOME CARD --}}
    <div class="hero-welcome-card mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge hero-glass-pill px-3 py-1 rounded-pill">
                        <i class="bx bx-sparkles me-1"></i> SIMUP Wistek Analytics
                    </span>
                    <span class="text-white-50 small">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</span>
                </div>
                <h2 class="text-white fw-bold mb-2">Selamat Datang Kembali, {{ Auth::user()->name ?? 'Administrator' }}! 👋</h2>
                <p class="text-white-50 mb-0" style="font-size: 0.95rem; max-width: 600px;">
                    Berikut adalah ringkasan kinerja keuangan, omzet logbook harian, dan tren operasional Unit Produksi Wistek bulan ini.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-inline-flex flex-column align-items-lg-end hero-glass-box">
                    <small class="text-white-50 mb-1">Total Transaksi Bulan Ini</small>
                    <span class="h3 text-white fw-bold mb-0">{{ number_format($totalTransaksi) }} <small class="fs-6 text-white-50">Trx</small></span>
                </div>
            </div>
        </div>
    </div>

    {{-- TODAY'S METRICS ROW --}}
    <div class="row g-3 mb-4">
        {{-- Pemasukan Hari Ini --}}
        <div class="col-md-6">
            <div class="modern-card p-4 h-100 border-start border-success border-4 shadow-sm" style="border-radius: 8px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-success fw-bold d-block mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                            <i class="bx bx-calendar-check me-1"></i> PEMASUKAN HARI INI
                        </span>
                        <h3 class="fw-bold text-dark mb-0">Rp {{ number_format($pemasukanHariIni, 0, ',', '.') }}</h3>
                    </div>
                    <div class="stat-icon-wrapper d-flex align-items-center justify-content-center" style="background-color: rgba(40, 167, 69, 0.1); color: #28a745; width: 48px; height: 48px; border-radius: 50%;">
                        <i class="bx bx-wallet" style="font-size: 1.75rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaksi Hari Ini --}}
        <div class="col-md-6">
            <div class="modern-card p-4 h-100 border-start border-warning border-4 shadow-sm" style="border-radius: 8px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-warning fw-bold d-block mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                            <i class="bx bx-shopping-bag me-1"></i> TRANSAKSI HARI INI
                        </span>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($transaksiHariIni) }} <span class="fs-6 text-muted fw-normal">Transaksi</span></h3>
                    </div>
                    <div class="stat-icon-wrapper d-flex align-items-center justify-content-center" style="background-color: rgba(255, 193, 7, 0.1); color: #ffc107; width: 48px; height: 48px; border-radius: 50%;">
                        <i class="bx bx-cart" style="font-size: 1.75rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FINANCIAL METRICS ROW --}}
    <div class="row g-3 mb-4">
        {{-- Pendapatan Bulan Ini --}}
        <div class="col-xl-3 col-md-6">
            <div class="modern-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.85rem;">Pendapatan Bulan Ini</span>
                        <h3 class="fw-bold text-dark mb-0">Rp {{ number_format($pendapatanBulanIni,0,',','.') }}</h3>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-primary">
                        <i class="bx bx-wallet-alt"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="trend-badge-success">
                        <i class="bx bx-trending-up"></i> +{{ number_format($persentasePendapatan, 1) }}%
                    </span>
                    <span class="text-muted small">vs bulan lalu</span>
                </div>
            </div>
        </div>

        {{-- Pengeluaran Bulan Ini --}}
        <div class="col-xl-3 col-md-6">
            <div class="modern-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.85rem;">Pengeluaran Bulan Ini</span>
                        <h3 class="fw-bold text-dark mb-0">Rp {{ number_format($pengeluaranBulanIni,0,',','.') }}</h3>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-danger">
                        <i class="bx bx-credit-card-front"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="trend-badge-danger">
                        <i class="bx bx-trending-down"></i> {{ number_format($persentasePengeluaran, 1) }}%
                    </span>
                    <span class="text-muted small">vs bulan lalu</span>
                </div>
            </div>
        </div>

        {{-- Laba / Rugi --}}
        <div class="col-xl-3 col-md-6">
            <div class="modern-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.85rem;">Laba / Rugi Bersih</span>
                        <h3 class="fw-bold text-dark mb-0">Rp {{ number_format($labaRugi,0,',','.') }}</h3>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-success">
                        <i class="bx bx-line-chart"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="{{ $labaRugi >= 0 ? 'trend-badge-success' : 'trend-badge-danger' }}">
                        <i class="bx {{ $labaRugi >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }}"></i> {{ $labaRugi >= 0 ? 'Surplus (Laba)' : 'Defisit (Rugi)' }}
                    </span>
                    <span class="text-muted small">Buku Besar</span>
                </div>
            </div>
        </div>

        {{-- Total Transaksi Card --}}
        <div class="col-xl-3 col-md-6">
            <div class="modern-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.85rem;">Total Transaksi POS</span>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($totalTransaksi) }}</h3>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-warning">
                        <i class="bx bx-shopping-bag"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="trend-badge-info">
                        <i class="bx bx-check-double"></i> Terproses
                    </span>
                    <span class="text-muted small">Bulan ini</span>
                </div>
            </div>
        </div>
    </div>

    {{-- LOGBOOK & OPERATIONAL STATS ROW --}}
    <div class="row g-3 mb-4">
        {{-- Omzet Logbook UP --}}
        <div class="col-lg-4 col-md-6">
            <div class="modern-card p-4 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper stat-icon-info">
                        <i class="bx bx-book-content"></i>
                    </div>
                    <div>
                        <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.82rem;">Omzet Logbook UP (Bulan Ini)</span>
                        <h4 class="fw-bold text-dark mb-0">Rp {{ number_format($logbookPendapatanBulanIni,0,',','.') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kas Laci UP --}}
        <div class="col-lg-4 col-md-6">
            <div class="modern-card p-4 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper stat-icon-success">
                        <i class="bx bx-lock-alt"></i>
                    </div>
                    <div>
                        <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.82rem;">Kas Laci UP Terakhir</span>
                        <h4 class="fw-bold text-dark mb-0">Rp {{ number_format($latestLogbookKas,0,',','.') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stok Kertas HVS Status --}}
        <div class="col-lg-4 col-md-12">
            <div class="modern-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper {{ $stokKertasStatus === 'Aman' ? 'stat-icon-success' : 'stat-icon-danger' }}">
                            <i class="bx bx-printer"></i>
                        </div>
                        <div>
                            <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.82rem;">Stok Kertas HVS Hari Terakhir</span>
                            <h4 class="fw-bold text-dark mb-0">{{ $stokKertasStatus }}</h4>
                        </div>
                    </div>
                    <span class="badge {{ $stokKertasStatus === 'Aman' ? 'badge-status-safe' : 'badge-status-danger' }} px-3 py-2 rounded-pill">
                        {{ $stokKertasStatus === 'Aman' ? 'Kondisi Baik' : 'Perlu Beli' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- CHARTS ROW --}}
    <div class="row g-3 mb-4">
        {{-- Grafik Penjualan POS --}}
        <div class="col-lg-8">
            <div class="modern-card h-100">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="bx bx-chart text-primary"></i> Grafik Penjualan POS (6 Bulan Terakhir)
                    </h5>
                    <span class="badge bg-light text-dark border">Realtime</span>
                </div>
                <div class="modern-card-body">
                    <div style="height: 280px; position: relative;">
                        <canvas id="grafikPenjualan"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stok Menipis Alert Widget --}}
        <div class="col-lg-4">
            <div class="modern-card h-100">
                <div class="modern-card-header">
                    <h5 class="modern-card-title text-danger">
                        <i class="bx bx-error-circle text-danger"></i> Peringatan Stok Menipis
                    </h5>
                    <span class="badge badge-status-danger fw-bold px-3 py-1 rounded-pill">{{ count($stokMenipis) }} Item</span>
                </div>
                <div class="modern-card-body p-0">
                    <div class="list-group list-group-flush rounded-bottom-4">
                        @forelse($stokMenipis as $item)
                            <div class="list-group-item p-3 border-bottom border-light">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold text-dark" style="font-size: 0.9rem;">{{ $item->nama_barang }}</span>
                                    <span class="badge bg-danger text-white rounded-pill px-2 py-1">{{ $item->stok }} pcs</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    @php
                                        $percent = min(100, max(10, ($item->stok / 10) * 100));
                                    @endphp
                                    <div class="progress-bar progress-bar-danger" role="progressbar" style="width: {{ $percent }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bx bx-check-circle text-success mb-2" style="font-size: 3rem;"></i>
                                <p class="mb-0 fw-semibold text-dark">Semua Stok Aman!</p>
                                <small class="text-muted">Tidak ada barang dengan stok <= 5 pcs.</small>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- LOGBOOK UP TREND CHART & RECENT TRANSACTIONS --}}
    <div class="row g-3 mb-4">
        {{-- Grafik Omzet Logbook UP --}}
        <div class="col-lg-7">
            <div class="modern-card h-100">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="bx bx-bar-chart-alt-2 text-info"></i> Omzet Logbook UP (6 Bulan Terakhir)
                    </h5>
                    <a href="{{ route('admin.logbook.index') }}" class="btn btn-sm btn-light border text-dark fw-semibold">Lihat Detail Logbook</a>
                </div>
                <div class="modern-card-body">
                    <div style="height: 250px; position: relative;">
                        <canvas id="grafikLogbook"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaksi Terbaru --}}
        <div class="col-lg-5">
            <div class="modern-card h-100">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="bx bx-receipt text-success"></i> Transaksi Terbaru
                    </h5>
                    <a href="{{ route('transaksi.index') }}" class="btn btn-sm btn-primary">Semua Transaksi</a>
                </div>
                <div class="modern-card-body p-0">
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Pembeli</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksiTerbaru as $trx)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary" style="font-size: 0.85rem;">{{ $trx->kode_transaksi }}</span>
                                        <small class="d-block text-muted" style="font-size: 0.72rem;">{{ $trx->tanggal->format('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark" style="font-size: 0.85rem;">{{ $trx->nama_pembeli }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-success" style="font-size: 0.88rem;">Rp {{ number_format($trx->total,0,',','.') }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Belum ada transaksi recorded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- GRAFIK PENJUALAN LINE CHART ---
    const ctxPenjualan = document.getElementById('grafikPenjualan').getContext('2d');
    
    // Create gradient fill for area chart
    const gradientPenjualan = ctxPenjualan.createLinearGradient(0, 0, 0, 260);
    gradientPenjualan.addColorStop(0, 'rgba(79, 70, 229, 0.35)');
    gradientPenjualan.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

    new Chart(ctxPenjualan, {
        type: 'line',
        data: {
            labels: {!! json_encode($bulanPenjualan) !!},
            datasets: [{
                label: 'Penjualan POS',
                data: {!! json_encode($dataPenjualan) !!},
                backgroundColor: gradientPenjualan,
                borderColor: '#4f46e5',
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#4f46e5',
                pointBorderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { family: 'Plus Jakarta Sans', size: 13, weight: '700' },
                    bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(context) {
                            return ' Total: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#64748b' }
                },
                y: {
                    grid: { color: 'rgba(226, 232, 240, 0.6)', borderDash: [4, 4] },
                    ticks: {
                        font: { family: 'Plus Jakarta Sans', size: 11 },
                        color: '#64748b',
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'M';
                            if (value >= 1000) return 'Rp ' + (value/1000).toFixed(0) + 'k';
                            return 'Rp ' + value;
                        }
                    }
                }
            }
        }
    });

    // --- GRAFIK LOGBOOK BAR CHART ---
    const ctxLogbook = document.getElementById('grafikLogbook').getContext('2d');
    
    const gradientLogbook = ctxLogbook.createLinearGradient(0, 0, 0, 240);
    gradientLogbook.addColorStop(0, '#06b6d4');
    gradientLogbook.addColorStop(1, '#0284c7');

    new Chart(ctxLogbook, {
        type: 'bar',
        data: {
            labels: {!! json_encode($bulanPenjualan) !!},
            datasets: [{
                label: 'Omzet Logbook UP',
                data: {!! json_encode($dataLogbook) !!},
                backgroundColor: gradientLogbook,
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { family: 'Plus Jakarta Sans', size: 13, weight: '700' },
                    bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(context) {
                            return ' Omzet Logbook: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#64748b' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(226, 232, 240, 0.6)', borderDash: [4, 4] },
                    ticks: {
                        font: { family: 'Plus Jakarta Sans', size: 11 },
                        color: '#64748b',
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'M';
                            if (value >= 1000) return 'Rp ' + (value/1000).toFixed(0) + 'k';
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
