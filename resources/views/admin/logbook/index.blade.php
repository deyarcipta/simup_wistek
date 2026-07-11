@extends('admin.layouts.app')
@section('title', 'Riwayat Logbook Unit Produksi')

@section('content')
<div class="row">
    {{-- STATS CARD --}}
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h5 class="text-white-50 mb-1">Total Omzet Logbook UP</h5>
                    <h2 class="text-white mb-0 fw-bold">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h2>
                    <p class="mb-0 mt-1 text-white-50" style="font-size: 0.85rem;">
                        Periode: <strong>{{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</strong>
                    </p>
                </div>
                <div class="d-none d-md-block" style="font-size: 4rem; opacity: 0.25;">
                    <i class="bx bx-wallet"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER & LIST CARD --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header pb-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-list-ul me-1"></i> Logbook Harian UP</h5>
                
                {{-- Form Filter & Export --}}
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <form method="GET" action="{{ route('admin.logbook.index') }}" class="d-flex gap-2 align-items-center">
                        <select name="bulan" class="form-select form-select-sm" onchange="this.form.submit()">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach($listTahun as $t)
                                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </form>

                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.logbook.download-pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-sm btn-danger shadow-sm">
                            <i class="bx bxs-file-pdf me-1"></i> PDF
                        </a>
                        <a href="{{ route('admin.logbook.download-excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-sm btn-success shadow-sm">
                            <i class="bx bxs-spreadsheet me-1"></i> Excel/CSV
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Status Hari</th>
                                <th>Kas Awal</th>
                                <th>Kas Akhir</th>
                                <th>Omzet Shift 1</th>
                                <th>Omzet Shift 2</th>
                                <th>Total Omzet</th>
                                <th>Kertas</th>
                                <th>Mesin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($logbooks as $logbook)
                            @php
                                $s1 = $logbook->details->where('shift_id', 1)->first();
                                $s2 = $logbook->details->where('shift_id', 2)->first();
                                $totalHarian = ($s1?->total_uang ?? 0) + ($s2?->total_uang ?? 0);
                            @endphp
                            <tr>
                                <td class="fw-semibold text-dark">{{ $logbook->tanggal->format('d/m/Y') }}</td>
                                <td>
                                    @if($logbook->status === 'aktif')
                                        <span class="badge bg-warning text-dark"><i class="bx bx-sun"></i> Shift 1 Aktif</span>
                                    @elseif($logbook->status === 'shift_1_selesai')
                                        <span class="badge bg-info"><i class="bx bx-cloud-light-rain"></i> Shift 2 Aktif</span>
                                    @else
                                        <span class="badge bg-success"><i class="bx bx-check-double"></i> Tutup UP</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($logbook->kas_awal, 0, ',', '.') }}</td>
                                <td>{{ $logbook->kas_akhir ? 'Rp ' . number_format($logbook->kas_akhir, 0, ',', '.') : '-' }}</td>
                                <td>Rp {{ number_format($s1?->total_uang ?? 0, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($s2?->total_uang ?? 0, 0, ',', '.') }}</td>
                                <td class="fw-bold text-primary">Rp {{ number_format($totalHarian, 0, ',', '.') }}</td>
                                <td>
                                    @if($logbook->stok_kertas === 'Aman')
                                        <span class="badge bg-label-success">Aman</span>
                                    @elseif($logbook->stok_kertas === 'Habis')
                                        <span class="badge bg-label-danger">Habis</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 100px;" title="{{ $logbook->status_mesin ?? '-' }}">
                                        {{ $logbook->status_mesin ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.logbook.show', $logbook->id) }}" class="btn btn-xs btn-outline-primary">
                                        <i class="bx bx-show me-1"></i> Rincian
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="bx bx-calendar-x mb-2" style="font-size: 2.5rem;"></i>
                                    <p class="mb-0">Tidak ada riwayat logbook pada periode ini.</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
