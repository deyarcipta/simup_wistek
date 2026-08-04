@extends('admin.layouts.app')
@section('title', 'Detail Logbook UP')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <a href="{{ route('admin.logbook.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-chevron-left"></i> Kembali ke Daftar
        </a>
    </div>

    {{-- CARD RINGKASAN HARI --}}
    <div class="col-12 col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title text-white mb-0"><i class="bx bx-calendar"></i> Ringkasan Harian</h5>
                <small class="text-white-50">{{ $logbook->tanggal->format('d F Y') }}</small>
            </div>
            <div class="card-body pt-4">
                <div class="mb-3">
                    <label class="text-muted d-block" style="font-size: 0.85rem;">Status Operasional:</label>
                    @if($logbook->status === 'aktif')
                        <span class="badge bg-warning text-dark"><i class="bx bx-sun"></i> Shift 1 Aktif</span>
                    @elseif($logbook->status === 'shift_1_selesai')
                        <span class="badge bg-info"><i class="bx bx-cloud-light-rain"></i> Shift 2 Aktif</span>
                    @else
                        <span class="badge bg-success"><i class="bx bx-check-double"></i> Tutup UP</span>
                    @endif
                </div>

                <div class="mb-3 border-top pt-2">
                    <label class="text-muted d-block" style="font-size: 0.85rem;">Uang Kas Awal:</label>
                    <span class="fw-bold text-dark h5">Rp {{ number_format($logbook->kas_awal, 0, ',', '.') }}</span>
                </div>

                <div class="mb-3 border-top pt-2">
                    <label class="text-muted d-block" style="font-size: 0.85rem;">Uang Kas Akhir Fisik:</label>
                    <span class="fw-bold text-success h5">{{ $logbook->kas_akhir ? 'Rp ' . number_format($logbook->kas_akhir, 0, ',', '.') : 'Belum Ditutup' }}</span>
                </div>

                @php
                    $s1 = $logbook->details->where('shift_id', 1)->first();
                    $s2 = $logbook->details->where('shift_id', 2)->first();
                    $s3 = $logbook->details->where('shift_id', 3)->first();
                    $totalOmzet = ($shift1Real['summary']['total_uang'] ?? 0) 
                        + ($shift2Real ? $shift2Real['summary']['total_uang'] : 0)
                        + ($shift3Real ? $shift3Real['summary']['total_uang'] : 0);
                    $expectedCash = $logbook->kas_awal + $totalOmzet;
                    $diff = $logbook->kas_akhir ? ($logbook->kas_akhir - $expectedCash) : 0;
                @endphp

                <div class="mb-3 border-top pt-2">
                    <label class="text-muted d-block" style="font-size: 0.85rem;">Uang Kas Diharapkan (Sistem):</label>
                    <span class="fw-bold text-dark" style="font-size: 1rem;">Rp {{ number_format($expectedCash, 0, ',', '.') }}</span>
                </div>

                @if($logbook->status === 'tutup_up')
                    <div class="mb-3 border-top pt-2">
                        <label class="text-muted d-block" style="font-size: 0.85rem;">Selisih Kas Laci:</label>
                        @if($diff == 0)
                            <span class="badge bg-label-success fw-bold"><i class="bx bx-check"></i> Cocok (Pas)</span>
                        @elseif($diff > 0)
                            <span class="badge bg-label-success fw-bold"><i class="bx bx-plus"></i> Lebih (+ Rp {{ number_format($diff, 0, ',', '.') }})</span>
                        @else
                            <span class="badge bg-label-danger fw-bold"><i class="bx bx-minus"></i> Kurang (- Rp {{ number_format(abs($diff), 0, ',', '.') }})</span>
                        @endif
                    </div>
                @endif

                <div class="mb-3 border-top pt-2">
                    <label class="text-muted d-block" style="font-size: 0.85rem;">Stok Kertas HVS:</label>
                    <span class="badge {{ $logbook->stok_kertas === 'Aman' ? 'bg-label-success' : 'bg-label-danger' }}">{{ $logbook->stok_kertas ?? '-' }}</span>
                </div>

                <div class="mb-0 border-top pt-2">
                    <label class="text-muted d-block" style="font-size: 0.85rem;">Kondisi Mesin / Printer:</label>
                    <span class="text-dark italic fw-semibold">"{{ $logbook->status_mesin ?? '-' }}"</span>
                </div>
            </div>
        </div>
    </div>

    {{-- CARDS PER SHIFT --}}
    <div class="col-12 col-md-8 mb-4">
        <div class="row g-4">
            
            {{-- SHIFT 1 CARD --}}
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bx bx-sun text-warning me-1"></i> Shift 1 (Pagi)</h6>
                        @if($s1)
                            @php
                                $s1Lateness = $s1->getLatenessInfo();
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-label-warning text-dark">Petugas: {{ $s1->user->name ?? '-' }}</span>
                                <span class="badge {{ $s1Lateness['badge_class'] }}">
                                    <i class="bx bx-time me-1"></i> {{ $s1Lateness['status_text'] }}
                                </span>
                            </div>
                        @else
                            <span class="badge bg-label-secondary">Belum Diisi</span>
                        @endif
                    </div>
                    <div class="card-body py-3">
                        @if($s1)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Transaksi</th>
                                            <th>Waktu</th>
                                            <th>Nama Pembeli</th>
                                            <th>Operator</th>
                                            <th>Detail Item</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($shift1Real['transaksi']->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-muted text-center py-3">Tidak ada transaksi POS pada shift ini.</td>
                                            </tr>
                                        @else
                                            @foreach($shift1Real['transaksi'] as $tx)
                                                <tr>
                                                    <td class="fw-semibold text-primary">{{ $tx->kode_transaksi }}</td>
                                                    <td>{{ $tx->created_at->format('H:i') }}</td>
                                                    <td>{{ $tx->nama_pembeli ?: '-' }}</td>
                                                    <td>{{ $tx->user->name ?? '-' }}</td>
                                                    <td>
                                                        @foreach($tx->details as $d)
                                                            <div class="small">
                                                                {{ $d->produkJasa->nama ?? 'Layanan' }} 
                                                                <span class="text-muted">({{ $d->jumlah }}x @ Rp {{ number_format($d->harga, 0, ',', '.') }})</span>
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                    <td class="text-end fw-semibold">Rp {{ number_format($tx->total, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light fw-bold">
                                            <td colspan="5" class="text-end">Total Pendapatan Shift 1:</td>
                                            <td class="text-end text-primary h6 fw-bold">Rp {{ number_format($shift1Real['summary']['total_uang'], 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center my-3">Data untuk Shift 1 Pagi belum dimasukkan.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- SHIFT 2 CARD --}}
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bx bx-cloud-light-rain text-info me-1"></i> Shift 2 (Siang)</h6>
                        @if($s2)
                            @php
                                $s2Lateness = $s2->getLatenessInfo();
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-label-info text-dark">Petugas: {{ $s2->user->name ?? '-' }}</span>
                                <span class="badge {{ $s2Lateness['badge_class'] }}">
                                    <i class="bx bx-time me-1"></i> {{ $s2Lateness['status_text'] }}
                                </span>
                            </div>
                        @else
                            <span class="badge bg-label-secondary">Belum Diisi</span>
                        @endif
                    </div>
                    <div class="card-body py-3">
                        @if($s2)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Transaksi</th>
                                            <th>Waktu</th>
                                            <th>Nama Pembeli</th>
                                            <th>Operator</th>
                                            <th>Detail Item</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($shift2Real['transaksi']->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-muted text-center py-3">Tidak ada transaksi POS pada shift ini.</td>
                                            </tr>
                                        @else
                                            @foreach($shift2Real['transaksi'] as $tx)
                                                <tr>
                                                    <td class="fw-semibold text-primary">{{ $tx->kode_transaksi }}</td>
                                                    <td>{{ $tx->created_at->format('H:i') }}</td>
                                                    <td>{{ $tx->nama_pembeli ?: '-' }}</td>
                                                    <td>{{ $tx->user->name ?? '-' }}</td>
                                                    <td>
                                                        @foreach($tx->details as $d)
                                                            <div class="small">
                                                                {{ $d->produkJasa->nama ?? 'Layanan' }} 
                                                                <span class="text-muted">({{ $d->jumlah }}x @ Rp {{ number_format($d->harga, 0, ',', '.') }})</span>
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                    <td class="text-end fw-semibold">Rp {{ number_format($tx->total, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light fw-bold">
                                            <td colspan="5" class="text-end">Total Pendapatan Shift 2:</td>
                                            <td class="text-end text-primary h6 fw-bold">Rp {{ number_format($shift2Real['summary']['total_uang'], 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center my-3">Data untuk Shift 2 Siang belum dimasukkan.</p>
                        @endif
                    </div>
                </div>
            </div>

            @if($jumlahShiftSetting == 3)
            {{-- SHIFT 3 CARD --}}
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bx bx-moon text-secondary me-1"></i> Shift 3 (Sore)</h6>
                        @if($s3)
                            @php
                                $s3Lateness = $s3->getLatenessInfo();
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-label-secondary text-dark">Petugas: {{ $s3->user->name ?? '-' }}</span>
                                <span class="badge {{ $s3Lateness['badge_class'] }}">
                                    <i class="bx bx-time me-1"></i> {{ $s3Lateness['status_text'] }}
                                </span>
                            </div>
                        @else
                            <span class="badge bg-label-secondary">Belum Diisi</span>
                        @endif
                    </div>
                    <div class="card-body py-3">
                        @if($s3)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Transaksi</th>
                                            <th>Waktu</th>
                                            <th>Nama Pembeli</th>
                                            <th>Operator</th>
                                            <th>Detail Item</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($shift3Real['transaksi']->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-muted text-center py-3">Tidak ada transaksi POS pada shift ini.</td>
                                            </tr>
                                        @else
                                            @foreach($shift3Real['transaksi'] as $tx)
                                                <tr>
                                                    <td class="fw-semibold text-primary">{{ $tx->kode_transaksi }}</td>
                                                    <td>{{ $tx->created_at->format('H:i') }}</td>
                                                    <td>{{ $tx->nama_pembeli ?: '-' }}</td>
                                                    <td>{{ $tx->user->name ?? '-' }}</td>
                                                    <td>
                                                        @foreach($tx->details as $d)
                                                            <div class="small">
                                                                {{ $d->produkJasa->nama ?? 'Layanan' }} 
                                                                <span class="text-muted">({{ $d->jumlah }}x @ Rp {{ number_format($d->harga, 0, ',', '.') }})</span>
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                    <td class="text-end fw-semibold">Rp {{ number_format($tx->total, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light fw-bold">
                                            <td colspan="5" class="text-end">Total Pendapatan Shift 3:</td>
                                            <td class="text-end text-primary h6 fw-bold">Rp {{ number_format($shift3Real['summary']['total_uang'], 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center my-3">Data untuk Shift 3 Sore belum dimasukkan.</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
