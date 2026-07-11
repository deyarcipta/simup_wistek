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
                    $totalOmzet = ($s1?->total_uang ?? 0) + ($s2?->total_uang ?? 0);
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
                            <span class="badge bg-label-warning text-dark">Petugas: {{ $s1->user->name ?? '-' }}</span>
                        @else
                            <span class="badge bg-label-secondary">Belum Diisi</span>
                        @endif
                    </div>
                    <div class="card-body py-3">
                        @if($s1)
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Layanan</th>
                                            <th class="text-center">Kuantitas</th>
                                            <th class="text-end">Tarif Riwayat</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Print Hitam Putih</td>
                                            <td class="text-center fw-bold">{{ $s1->jumlah_print }}</td>
                                            <td class="text-end">Rp {{ number_format($s1->harga_print, 0, ',', '.') }}</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($s1->jumlah_print * $s1->harga_print, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Fotokopi Hitam</td>
                                            <td class="text-center fw-bold">{{ $s1->jumlah_fotokopi }}</td>
                                            <td class="text-end">Rp {{ number_format($s1->harga_fotokopi, 0, ',', '.') }}</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($s1->jumlah_fotokopi * $s1->harga_fotokopi, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jilid Makalah</td>
                                            <td class="text-center fw-bold">{{ $s1->jumlah_jilid }}</td>
                                            <td class="text-end">Rp {{ number_format($s1->harga_jilid, 0, ',', '.') }}</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($s1->jumlah_jilid * $s1->harga_jilid, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Pendapatan Lain-lain (Retail/Pulpen/dll)</td>
                                            <td class="text-end fw-bold text-success">Rp {{ number_format($s1->pendapatan_lain ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <th colspan="3" class="text-end">Total Pendapatan Shift 1:</th>
                                            <th class="text-end text-primary h6 fw-bold">Rp {{ number_format($s1->total_uang, 0, ',', '.') }}</th>
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
                            <span class="badge bg-label-info">Petugas: {{ $s2->user->name ?? '-' }}</span>
                        @else
                            <span class="badge bg-label-secondary">Belum Diisi</span>
                        @endif
                    </div>
                    <div class="card-body py-3">
                        @if($s2)
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Layanan</th>
                                            <th class="text-center">Kuantitas</th>
                                            <th class="text-end">Tarif Riwayat</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Print Hitam Putih</td>
                                            <td class="text-center fw-bold">{{ $s2->jumlah_print }}</td>
                                            <td class="text-end">Rp {{ number_format($s2->harga_print, 0, ',', '.') }}</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($s2->jumlah_print * $s2->harga_print, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Fotokopi Hitam</td>
                                            <td class="text-center fw-bold">{{ $s2->jumlah_fotokopi }}</td>
                                            <td class="text-end">Rp {{ number_format($s2->harga_fotokopi, 0, ',', '.') }}</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($s2->jumlah_fotokopi * $s2->harga_fotokopi, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jilid Makalah</td>
                                            <td class="text-center fw-bold">{{ $s2->jumlah_jilid }}</td>
                                            <td class="text-end">Rp {{ number_format($s2->harga_jilid, 0, ',', '.') }}</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($s2->jumlah_jilid * $s2->harga_jilid, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Pendapatan Lain-lain (Retail/Pulpen/dll)</td>
                                            <td class="text-end fw-bold text-success">Rp {{ number_format($s2->pendapatan_lain ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <th colspan="3" class="text-end">Total Pendapatan Shift 2:</th>
                                            <th class="text-end text-primary h6 fw-bold">Rp {{ number_format($s2->total_uang, 0, ',', '.') }}</th>
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

        </div>
    </div>
</div>
@endsection
