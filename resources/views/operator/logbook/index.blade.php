@extends('operator.layouts.app')
@section('title', 'Logbook Shift Hari Ini')

@section('content')
@php
    $pengaturan = \App\Models\Pengaturan::first();
    $schedules = $pengaturan?->getShiftSchedules();
    $jumlahShiftSetting = $pengaturan?->jumlah_shift ?? 2;
@endphp
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        
        {{-- CASE 1: HARI BELUM DIMULAI --}}
        @if(!$logbook)
            <div class="card shadow border-0 mt-4 overflow-hidden">
                <div class="card-header bg-primary text-white text-center py-4">
                    <i class="bx bx-run mb-2" style="font-size: 3.5rem;"></i>
                    <h4 class="text-white mb-0">Mulai Hari Operasional UP</h4>
                    <p class="mb-0 text-white-50">Langkah 1: Masukkan uang kas awal di laci hari ini</p>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('operator.logbook.start') }}" method="POST" id="form-start-day">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark" style="font-size: 1rem;">Uang Kas Awal (Kas Laci)</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text fw-bold">Rp</span>
                                <input type="number" name="kas_awal" class="form-control form-control-lg" placeholder="0" min="0" required autofocus>
                            </div>
                            <div class="form-text text-muted">Pastikan Anda telah menghitung uang fisik yang ada di dalam laci kasir sebelum memulai shift.</div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow"><i class="bx bx-play-circle me-1"></i> Mulai {{ $schedules[1]['nama'] ?? 'Shift 1 Pagi' }}</button>
                    </form>
                </div>
            </div>
        
        {{-- CASE 2: SHIFT 1 PAGI SEDANG BERJALAN --}}
        @elseif($logbook->status === 'aktif')
            @if($isDifferentOperator)
                <div class="card shadow border-0 mt-4 overflow-hidden">
                    <div class="card-header bg-warning text-dark text-center py-4">
                        <i class="bx bx-lock-alt mb-2" style="font-size: 3.5rem;"></i>
                        <h4 class="text-dark mb-0 fw-bold">{{ $schedules[1]['nama'] ?? 'Shift 1 Pagi' }} Terkunci</h4>
                        <p class="mb-0 text-dark-50">Sedang Berjalan oleh {{ $operatorName }}</p>
                    </div>
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bx bx-lock-alt text-warning animate-bounce" style="font-size: 5rem;"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Hari Operasional Dimulai Oleh Operator Lain!</h4>
                        <p class="text-muted mx-auto" style="max-width: 500px;">
                            Hari operasional ini (Shift 1) telah diaktifkan oleh operator <strong>{{ $operatorName }}</strong>.
                            Anda tidak dapat mengakses, melihat rincian, atau menyelesaikan Shift 1 sebelum operator <strong>{{ $operatorName }}</strong> menyelesaikannya.
                        </p>
                    </div>
                </div>
            @else
                <div class="card shadow border-0 overflow-hidden">
                    <div class="card-header bg-warning text-dark text-center py-4">
                        <i class="bx bx-sun mb-2" style="font-size: 3.5rem;"></i>
                        <h4 class="text-dark mb-0 fw-bold">{{ $schedules[1]['nama'] ?? 'Shift 1 Pagi' }}</h4>
                        <p class="mb-0 text-dark-50">{{ $schedules[1]['deskripsi'] ?? 'Sedang Berjalan' }}</p>
                    </div>
                    <div class="card-body py-4">
                        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4">
                            <i class="bx bx-info-circle me-2" style="font-size: 1.5rem;"></i>
                            <div>
                                Uang Kas Awal Hari Ini: <strong>Rp {{ number_format($logbook->kas_awal, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        <form action="{{ route('operator.logbook.shift1') }}" method="POST" id="form-shift-1">
                            @csrf
                            
                            @php
                                $s1Start = $logbook->created_at;
                                $s1End = \Carbon\Carbon::now();
                                $s1Items = DB::table('transaksi_detail')
                                    ->join('transaksi', 'transaksi_detail.transaksi_id', '=', 'transaksi.id')
                                    ->join('produk_jasa', 'transaksi_detail.produk_jasa_id', '=', 'produk_jasa.id')
                                    ->whereBetween('transaksi.created_at', [$s1Start, $s1End])
                                    ->select(
                                        'produk_jasa.nama as produk_nama',
                                        'transaksi_detail.harga as unit_harga',
                                        DB::raw('SUM(transaksi_detail.jumlah) as total_qty'),
                                        DB::raw('SUM(transaksi_detail.subtotal) as total_subtotal')
                                    )
                                    ->groupBy('transaksi_detail.produk_jasa_id', 'produk_jasa.nama', 'transaksi_detail.harga')
                                    ->get();
                                $s1Total = $s1Items->sum('total_subtotal');
                            @endphp

                            <div class="p-3 bg-light rounded-3 mb-4">
                                <h6 class="fw-bold text-dark mb-3"><i class="bx bx-receipt"></i> Rincian Transaksi Shift 1 <span class="badge bg-label-primary ms-1">Otomatis dari POS</span></h6>
                                
                                @forelse($s1Items as $item)
                                    <div class="row align-items-center mb-3">
                                        <div class="col-sm-5 col-7">
                                            <div class="fw-semibold text-dark" style="font-size: 0.9rem; line-height: 1.2; max-height: 2.4em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $item->produk_nama }}">
                                                {{ $item->produk_nama }}
                                            </div>
                                            <small class="d-block text-muted">Tarif: Rp {{ number_format($item->unit_harga, 0, ',', '.') }}</small>
                                        </div>
                                        <div class="col-sm-7 col-5">
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control bg-white text-dark fw-bold text-center" 
                                                       value="{{ $item->total_qty }}" readonly>
                                                <span class="input-group-text">pcs</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-3">
                                        <i class="bx bx-info-circle mb-1" style="font-size: 1.5rem;"></i>
                                        <p class="mb-0 italic" style="font-size: 0.85rem;">Belum ada transaksi pada shift ini.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 mb-4 bg-white">
                                <span class="fw-semibold text-muted">Total Pendapatan Shift 1:</span>
                                <span class="h4 mb-0 fw-bold text-warning" id="total-shift-1">Rp {{ number_format($s1Total, 0, ',', '.') }}</span>
                            </div>

                            @if($jumlahShiftSetting == 1)
                                <div class="divider my-4"><div class="divider-text fw-bold">PENUTUPAN UNIT PRODUKSI</div></div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Sisa Stok Kertas HVS</label>
                                    <select name="stok_kertas" class="form-select" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Aman" {{ old('stok_kertas') === 'Aman' ? 'selected' : '' }}>Aman</option>
                                        <option value="Habis" {{ old('stok_kertas') === 'Habis' ? 'selected' : '' }}>Habis / Perlu Beli</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Kondisi Mesin / Printer (Catatan Tambahan)</label>
                                    <textarea name="status_mesin" class="form-control" rows="2" placeholder="Tuliskan kondisi printer/copier jika ada error atau tulis 'Normal'">Normal</textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Total Uang Kas Akhir (Uang Laci Fisik)</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="kas_akhir" id="kas_akhir" class="form-control form-control-lg" placeholder="0" min="0" required>
                                    </div>
                                    <div class="form-text text-muted" id="kas_diharapkan_info"></div>
                                </div>

                                <button type="submit" class="btn btn-warning btn-lg w-100 text-dark fw-bold shadow">
                                    <i class="bx bx-power-off me-1"></i> Tutup UP Hari Ini
                                </button>
                            @else
                                <button type="submit" class="btn btn-warning btn-lg w-100 text-dark fw-bold shadow">
                                    <i class="bx bx-check-circle me-1"></i> Selesai Shift 1
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            @endif

        {{-- CASE 3: SHIFT 1 SELESAI, SHIFT 2 SIANG SEDANG BERJALAN --}}
        @elseif($logbook->status === 'shift_1_selesai')
            @php
                $shift1 = $logbook->details->where('shift_id', 1)->first();
            @endphp

            @if(!$hasStartedShift2)
                <div class="card shadow border-0 overflow-hidden">
                    <div class="card-header bg-info text-white text-center py-4">
                        <i class="bx bx-play-circle mb-2" style="font-size: 3.5rem;"></i>
                        <h4 class="text-white mb-0 fw-bold">Mulai {{ $schedules[2]['nama'] ?? 'Shift 2 Siang' }}</h4>
                        <p class="mb-0 text-white-50">Langkah berikutnya: Aktifkan shift 2 hari ini</p>
                    </div>
                    <div class="card-body py-4">
                        {{-- Summary Shift 1 --}}
                        <div class="card bg-label-info border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2 text-info"><i class="bx bx-check-double"></i> Laporan Shift 1 Pagi:</h6>
                                <ul class="mb-0 text-dark-50 ps-3" style="font-size: 0.9rem;">
                                    <li>Operator: <strong>{{ $shift1->user->name ?? '-' }}</strong></li>
                                    <li>Total Kas Shift 1: <strong>Rp {{ number_format($shift1->total_uang, 0, ',', '.') }}</strong></li>
                                </ul>
                            </div>
                        </div>

                        <form action="{{ route('operator.logbook.start_shift2') }}" method="POST">
                            @csrf
                            <div class="mb-4 text-center">
                                <p class="text-muted" style="font-size: 0.95rem;">
                                    Tekan tombol di bawah untuk memulai {{ $schedules[2]['nama'] ?? 'Shift 2 Siang' }}. Akun Anda akan didaftarkan sebagai operator penanggung jawab Shift 2 hari ini.
                                </p>
                            </div>
                            <button type="submit" class="btn btn-info btn-lg w-100 shadow">
                                <i class="bx bx-play-circle me-1"></i> Mulai {{ $schedules[2]['nama'] ?? 'Shift 2 Siang' }}
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($isDifferentOperatorShift2)
                <div class="card shadow border-0 mt-4 overflow-hidden">
                    <div class="card-header bg-info text-white text-center py-4">
                        <i class="bx bx-lock-alt mb-2" style="font-size: 3.5rem;"></i>
                        <h4 class="text-white mb-0 fw-bold">{{ $schedules[2]['nama'] ?? 'Shift 2 Siang' }} Terkunci</h4>
                        <p class="mb-0 text-white-50">Sedang Berjalan oleh {{ $operatorNameShift2 }}</p>
                    </div>
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bx bx-lock-alt text-info animate-bounce" style="font-size: 5rem;"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Shift 2 Dimulai Oleh Operator Lain!</h4>
                        <p class="text-muted mx-auto" style="max-width: 500px;">
                            Shift 2 hari ini telah diaktifkan oleh operator <strong>{{ $operatorNameShift2 }}</strong>.
                            Anda tidak dapat mengakses, mengisi, atau memproses shift ini sebelum operator <strong>{{ $operatorNameShift2 }}</strong> menyelesaikannya.
                        </p>
                    </div>
                </div>
            @else
                <div class="card shadow border-0 overflow-hidden">
                    <div class="card-header bg-info text-white text-center py-4">
                        <i class="bx bx-cloud-light-rain mb-2" style="font-size: 3.5rem;"></i>
                        <h4 class="text-white mb-0 fw-bold">{{ $schedules[2]['nama'] ?? 'Shift 2 Siang' }}</h4>
                        <p class="mb-0 text-white-50">{{ $schedules[2]['deskripsi'] ?? 'Sedang Berjalan' }}</p>
                    </div>
                    <div class="card-body py-4">
                        {{-- Summary Shift 1 --}}
                        <div class="card bg-label-info border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2 text-info"><i class="bx bx-check-double"></i> Data Operasional Shift 1 Pagi:</h6>
                                <ul class="mb-0 text-dark-50 ps-3" style="font-size: 0.9rem;">
                                    <li>Operator: <strong>{{ $shift1->user->name ?? '-' }}</strong></li>
                                    <li>Uang Diterima: <strong>Rp {{ number_format($shift1->total_uang, 0, ',', '.') }}</strong></li>
                                </ul>
                            </div>
                        </div>

                        <form action="{{ route('operator.logbook.shift2') }}" method="POST" id="form-shift-2">
                            @csrf
                            
                            @php
                                $shift1Detail = $logbook->details->where('shift_id', 1)->first();
                                $s2Start = $shift1Detail ? $shift1Detail->created_at : $logbook->created_at;
                                $s2End = \Carbon\Carbon::now();
                                $s2Items = DB::table('transaksi_detail')
                                    ->join('transaksi', 'transaksi_detail.transaksi_id', '=', 'transaksi.id')
                                    ->join('produk_jasa', 'transaksi_detail.produk_jasa_id', '=', 'produk_jasa.id')
                                    ->whereBetween('transaksi.created_at', [$s2Start, $s2End])
                                    ->select(
                                        'produk_jasa.nama as produk_nama',
                                        'transaksi_detail.harga as unit_harga',
                                        DB::raw('SUM(transaksi_detail.jumlah) as total_qty'),
                                        DB::raw('SUM(transaksi_detail.subtotal) as total_subtotal')
                                    )
                                    ->groupBy('transaksi_detail.produk_jasa_id', 'produk_jasa.nama', 'transaksi_detail.harga')
                                    ->get();
                                $s2Total = $s2Items->sum('total_subtotal');
                            @endphp

                            <div class="p-3 bg-light rounded-3 mb-4">
                                <h6 class="fw-bold text-dark mb-3"><i class="bx bx-receipt"></i> Rincian Transaksi Shift 2 <span class="badge bg-label-primary ms-1">Otomatis dari POS</span></h6>
                                
                                @forelse($s2Items as $item)
                                    <div class="row align-items-center mb-3">
                                        <div class="col-sm-5 col-7">
                                            <div class="fw-semibold text-dark" style="font-size: 0.9rem; line-height: 1.2; max-height: 2.4em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $item->produk_nama }}">
                                                {{ $item->produk_nama }}
                                            </div>
                                            <small class="d-block text-muted">Tarif: Rp {{ number_format($item->unit_harga, 0, ',', '.') }}</small>
                                        </div>
                                        <div class="col-sm-7 col-5">
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control bg-white text-dark fw-bold text-center" 
                                                       value="{{ $item->total_qty }}" readonly>
                                                <span class="input-group-text">pcs</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-3">
                                        <i class="bx bx-info-circle mb-1" style="font-size: 1.5rem;"></i>
                                        <p class="mb-0 italic" style="font-size: 0.85rem;">Belum ada transaksi pada shift ini.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 mb-4 bg-white">
                                <span class="fw-semibold text-muted">Total Pendapatan Shift 2:</span>
                                <span class="h4 mb-0 fw-bold text-info" id="total-shift-2">Rp {{ number_format($s2Total, 0, ',', '.') }}</span>
                            </div>

                            @if($jumlahShiftSetting == 2)
                                <div class="divider my-4"><div class="divider-text fw-bold">PENUTUPAN UNIT PRODUKSI</div></div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Sisa Stok Kertas HVS</label>
                                    <select name="stok_kertas" class="form-select" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Aman" {{ old('stok_kertas') === 'Aman' ? 'selected' : '' }}>Aman</option>
                                        <option value="Habis" {{ old('stok_kertas') === 'Habis' ? 'selected' : '' }}>Habis / Perlu Beli</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Kondisi Mesin / Printer (Catatan Tambahan)</label>
                                    <textarea name="status_mesin" class="form-control" rows="2" placeholder="Tuliskan kondisi printer/copier jika ada error atau tulis 'Normal'">Normal</textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Total Uang Kas Akhir (Uang Laci Fisik)</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="kas_akhir" id="kas_akhir" class="form-control form-control-lg" placeholder="0" min="0" required>
                                    </div>
                                    <div class="form-text text-muted" id="kas_diharapkan_info"></div>
                                </div>

                                <button type="submit" class="btn btn-info btn-lg w-100 shadow">
                                    <i class="bx bx-power-off me-1"></i> Tutup UP Hari Ini
                                </button>
                            @else
                                <button type="submit" class="btn btn-info btn-lg w-100 shadow">
                                    <i class="bx bx-check-circle me-1"></i> Selesai Shift 2
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            @endif

        {{-- CASE 4: SHIFT 2 SELESAI, SHIFT 3 SORE SEDANG BERJALAN --}}
        @elseif($logbook->status === 'shift_2_selesai')
            @php
                $shift1 = $logbook->details->where('shift_id', 1)->first();
                $shift2 = $logbook->details->where('shift_id', 2)->first();
            @endphp

            @if(!$hasStartedShift3)
                <div class="card shadow border-0 overflow-hidden">
                    <div class="card-header bg-dark text-white text-center py-4">
                        <i class="bx bx-play-circle mb-2" style="font-size: 3.5rem;"></i>
                        <h4 class="text-white mb-0 fw-bold">Mulai {{ $schedules[3]['nama'] ?? 'Shift 3 Sore' }}</h4>
                        <p class="mb-0 text-white-50">Langkah berikutnya: Aktifkan shift 3 hari ini</p>
                    </div>
                    <div class="card-body py-4">
                        {{-- Summary Shift 1 & 2 --}}
                        <div class="card bg-label-dark border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2 text-dark"><i class="bx bx-check-double"></i> Ringkasan Laporan Sebelumnya:</h6>
                                <ul class="mb-0 text-dark-50 ps-3" style="font-size: 0.9rem;">
                                    <li>Shift 1: Rp {{ number_format($shift1->total_uang, 0, ',', '.') }}</li>
                                    <li>Shift 2: Rp {{ number_format($shift2->total_uang, 0, ',', '.') }}</li>
                                </ul>
                            </div>
                        </div>

                        <form action="{{ route('operator.logbook.start_shift3') }}" method="POST">
                            @csrf
                            <div class="mb-4 text-center">
                                <p class="text-muted" style="font-size: 0.95rem;">
                                    Tekan tombol di bawah untuk memulai {{ $schedules[3]['nama'] ?? 'Shift 3 Sore' }}. Akun Anda akan didaftarkan sebagai operator penanggung jawab Shift 3 hari ini.
                                </p>
                            </div>
                            <button type="submit" class="btn btn-dark btn-lg w-100 shadow">
                                <i class="bx bx-play-circle me-1"></i> Mulai {{ $schedules[3]['nama'] ?? 'Shift 3 Sore' }}
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($isDifferentOperatorShift3)
                <div class="card shadow border-0 mt-4 overflow-hidden">
                    <div class="card-header bg-dark text-white text-center py-4">
                        <i class="bx bx-lock-alt mb-2" style="font-size: 3.5rem;"></i>
                        <h4 class="text-white mb-0 fw-bold">{{ $schedules[3]['nama'] ?? 'Shift 3 Sore' }} Terkunci</h4>
                        <p class="mb-0 text-white-50">Sedang Berjalan oleh {{ $operatorNameShift3 }}</p>
                    </div>
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bx bx-lock-alt text-dark animate-bounce" style="font-size: 5rem;"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Shift 3 Dimulai Oleh Operator Lain!</h4>
                        <p class="text-muted mx-auto" style="max-width: 500px;">
                            Shift 3 hari ini telah diaktifkan oleh operator <strong>{{ $operatorNameShift3 }}</strong>.
                            Anda tidak dapat mengakses, mengisi, atau menutup hari operasional ini sebelum operator <strong>{{ $operatorNameShift3 }}</strong> melakukan Tutup UP.
                        </p>
                    </div>
                </div>
            @else
                <div class="card shadow border-0 overflow-hidden">
                    <div class="card-header bg-dark text-white text-center py-4">
                        <i class="bx bx-moon mb-2" style="font-size: 3.5rem;"></i>
                        <h4 class="text-white mb-0 fw-bold">{{ $schedules[3]['nama'] ?? 'Shift 3 Sore' }}</h4>
                        <p class="mb-0 text-white-50">{{ $schedules[3]['deskripsi'] ?? 'Sedang Berjalan' }}</p>
                    </div>
                    <div class="card-body py-4">
                        {{-- Summary Shift 1 & 2 --}}
                        <div class="card bg-label-dark border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2 text-dark"><i class="bx bx-check-double"></i> Data Operasional Sebelumnya:</h6>
                                <ul class="mb-0 text-dark-50 ps-3" style="font-size: 0.9rem;">
                                    <li>Shift 1: <strong>{{ $shift1->user->name ?? '-' }}</strong> (Rp {{ number_format($shift1->total_uang, 0, ',', '.') }})</li>
                                    <li>Shift 2: <strong>{{ $shift2->user->name ?? '-' }}</strong> (Rp {{ number_format($shift2->total_uang, 0, ',', '.') }})</li>
                                </ul>
                            </div>
                        </div>

                        <form action="{{ route('operator.logbook.shift3') }}" method="POST" id="form-shift-3">
                            @csrf
                            
                            @php
                                $shift2Detail = $logbook->details->where('shift_id', 2)->first();
                                $s3Start = $shift2Detail ? $shift2Detail->created_at : $logbook->created_at;
                                $s3End = \Carbon\Carbon::now();
                                $s3Items = DB::table('transaksi_detail')
                                    ->join('transaksi', 'transaksi_detail.transaksi_id', '=', 'transaksi.id')
                                    ->join('produk_jasa', 'transaksi_detail.produk_jasa_id', '=', 'produk_jasa.id')
                                    ->whereBetween('transaksi.created_at', [$s3Start, $s3End])
                                    ->select(
                                        'produk_jasa.nama as produk_nama',
                                        'transaksi_detail.harga as unit_harga',
                                        DB::raw('SUM(transaksi_detail.jumlah) as total_qty'),
                                        DB::raw('SUM(transaksi_detail.subtotal) as total_subtotal')
                                    )
                                    ->groupBy('transaksi_detail.produk_jasa_id', 'produk_jasa.nama', 'transaksi_detail.harga')
                                    ->get();
                                $s3Total = $s3Items->sum('total_subtotal');
                            @endphp

                            <div class="p-3 bg-light rounded-3 mb-4">
                                <h6 class="fw-bold text-dark mb-3"><i class="bx bx-receipt"></i> Rincian Transaksi Shift 3 <span class="badge bg-label-primary ms-1">Otomatis dari POS</span></h6>
                                
                                @forelse($s3Items as $item)
                                    <div class="row align-items-center mb-3">
                                        <div class="col-sm-5 col-7">
                                            <div class="fw-semibold text-dark" style="font-size: 0.9rem; line-height: 1.2; max-height: 2.4em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $item->produk_nama }}">
                                                {{ $item->produk_nama }}
                                            </div>
                                            <small class="d-block text-muted">Tarif: Rp {{ number_format($item->unit_harga, 0, ',', '.') }}</small>
                                        </div>
                                        <div class="col-sm-7 col-5">
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control bg-white text-dark fw-bold text-center" 
                                                       value="{{ $item->total_qty }}" readonly>
                                                <span class="input-group-text">pcs</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-3">
                                        <i class="bx bx-info-circle mb-1" style="font-size: 1.5rem;"></i>
                                        <p class="mb-0 italic" style="font-size: 0.85rem;">Belum ada transaksi pada shift ini.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 mb-4 bg-white">
                                <span class="fw-semibold text-muted">Total Pendapatan Shift 3:</span>
                                <span class="h4 mb-0 fw-bold text-dark" id="total-shift-3">Rp {{ number_format($s3Total, 0, ',', '.') }}</span>
                            </div>

                            <div class="divider my-4"><div class="divider-text fw-bold">PENUTUPAN UNIT PRODUKSI</div></div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Sisa Stok Kertas HVS</label>
                                <select name="stok_kertas" class="form-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Aman" {{ old('stok_kertas') === 'Aman' ? 'selected' : '' }}>Aman</option>
                                    <option value="Habis" {{ old('stok_kertas') === 'Habis' ? 'selected' : '' }}>Habis / Perlu Beli</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Kondisi Mesin / Printer (Catatan Tambahan)</label>
                                <textarea name="status_mesin" class="form-control" rows="2" placeholder="Tuliskan kondisi printer/copier jika ada error atau tulis 'Normal'">Normal</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Total Uang Kas Akhir (Uang Laci Fisik)</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="kas_akhir" id="kas_akhir" class="form-control form-control-lg" placeholder="0" min="0" required>
                                </div>
                                <div class="form-text text-muted" id="kas_diharapkan_info"></div>
                            </div>

                            <button type="submit" class="btn btn-dark btn-lg w-100 shadow">
                                <i class="bx bx-power-off me-1"></i> Tutup UP Hari Ini
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        {{-- CASE 5: UP SUDAH DITUTUP HARI INI --}}
        @elseif($logbook->status === 'tutup_up')
            @php
                $shift1 = $logbook->details->where('shift_id', 1)->first();
                $shift2 = $logbook->details->where('shift_id', 2)->first();
                $shift3 = $logbook->details->where('shift_id', 3)->first();

                $totalOmzet = ($shift1?->total_uang ?? 0);
                if ($jumlahShiftSetting >= 2) {
                    $totalOmzet += ($shift2?->total_uang ?? 0);
                }
                if ($jumlahShiftSetting == 3) {
                    $totalOmzet += ($shift3?->total_uang ?? 0);
                }
            @endphp
            <div class="card shadow border-0 overflow-hidden mt-4">
                <div class="card-header bg-success text-white text-center py-4">
                    <i class="bx bx-check-shield mb-2" style="font-size: 3.5rem;"></i>
                    <h4 class="text-white mb-0 fw-bold">Unit Produksi Ditutup</h4>
                    <p class="mb-0 text-white-50">Operasional tanggal {{ $logbook->tanggal->format('d F Y') }} selesai</p>
                </div>
                <div class="card-body py-4">
                    <div class="text-center mb-4">
                        <h5 class="text-success fw-bold">Terima kasih atas kerja keras Anda hari ini!</h5>
                        <p class="text-muted">Semua laporan logbook shift telah dikunci dan dikirim ke Pembina/Kaprog secara real-time.</p>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bx bx-detail"></i> Ringkasan Keuangan Logbook:</h6>
                    
                    <div class="row mb-3">
                        <div class="col-6 text-muted">Kas Awal Laci:</div>
                        <div class="col-6 text-end fw-bold text-dark">Rp {{ number_format($logbook->kas_awal, 0, ',', '.') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6 text-muted">Omzet Pemasukan:</div>
                        <div class="col-6 text-end fw-bold text-primary">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6 text-muted">Kas Akhir Laci:</div>
                        <div class="col-6 text-end fw-bold text-success">Rp {{ number_format($logbook->kas_akhir, 0, ',', '.') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6 text-muted">Status Stok Kertas:</div>
                        <div class="col-6 text-end fw-bold text-{{ $logbook->stok_kertas === 'Aman' ? 'success' : 'danger' }}">{{ $logbook->stok_kertas }}</div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-6 text-muted">Kondisi Mesin:</div>
                        <div class="col-6 text-end text-dark italic">"{{ $logbook->status_mesin }}"</div>
                    </div>

                    <div class="p-3 bg-light rounded-3">
                        <div class="row">
                            @if($jumlahShiftSetting == 1)
                                <div class="col-12 text-center">
                                    <small class="text-muted d-block">Shift 1 Pagi</small>
                                    <div class="fw-bold text-dark mt-1">Rp {{ number_format($shift1?->total_uang ?? 0, 0, ',', '.') }}</div>
                                    <small class="d-block text-muted-50" style="font-size: 0.75rem;">{{ $shift1?->user->name ?? '-' }}</small>
                                </div>
                            @elseif($jumlahShiftSetting == 2)
                                <div class="col-6 border-end text-center">
                                    <small class="text-muted d-block">Shift 1 Pagi</small>
                                    <div class="fw-bold text-dark mt-1">Rp {{ number_format($shift1?->total_uang ?? 0, 0, ',', '.') }}</div>
                                    <small class="d-block text-muted-50" style="font-size: 0.75rem;">{{ $shift1?->user->name ?? '-' }}</small>
                                </div>
                                <div class="col-6 text-center">
                                    <small class="text-muted d-block">Shift 2 Siang</small>
                                    <div class="fw-bold text-dark mt-1">Rp {{ number_format($shift2?->total_uang ?? 0, 0, ',', '.') }}</div>
                                    <small class="d-block text-muted-50" style="font-size: 0.75rem;">{{ $shift2?->user->name ?? '-' }}</small>
                                </div>
                            @else
                                <div class="col-4 border-end text-center">
                                    <small class="text-muted d-block">Shift 1 Pagi</small>
                                    <div class="fw-bold text-dark mt-1">Rp {{ number_format($shift1?->total_uang ?? 0, 0, ',', '.') }}</div>
                                    <small class="d-block text-muted-50" style="font-size: 0.75rem;">{{ $shift1?->user->name ?? '-' }}</small>
                                </div>
                                <div class="col-4 border-end text-center">
                                    <small class="text-muted d-block">Shift 2 Siang</small>
                                    <div class="fw-bold text-dark mt-1">Rp {{ number_format($shift2?->total_uang ?? 0, 0, ',', '.') }}</div>
                                    <small class="d-block text-muted-50" style="font-size: 0.75rem;">{{ $shift2?->user->name ?? '-' }}</small>
                                </div>
                                <div class="col-4 text-center">
                                    <small class="text-muted d-block">Shift 3 Sore</small>
                                    <div class="fw-bold text-dark mt-1">Rp {{ number_format($shift3?->total_uang ?? 0, 0, ',', '.') }}</div>
                                    <small class="d-block text-muted-50" style="font-size: 0.75rem;">{{ $shift3?->user->name ?? '-' }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const kasAkhirInput = document.getElementById('kas_akhir');
        const kasDiharapkanInfo = document.getElementById('kas_diharapkan_info');

        const kasAwal = parseFloat(@json($logbook ? $logbook->kas_awal : 0));
        const shift1Total = parseFloat(@json($logbook && isset($shift1) ? $shift1->total_uang : 0));
        const shift2Total = parseFloat(@json($logbook && isset($shift2) ? $shift2->total_uang : 0));
        const shift3Total = parseFloat(@json($logbook && isset($shift3) ? $shift3->total_uang : 0));

        const currentShift1 = parseFloat(@json($logbook && isset($s1Total) ? $s1Total : 0));
        const currentShift2 = parseFloat(@json($logbook && isset($s2Total) ? $s2Total : 0));
        const currentShift3 = parseFloat(@json($logbook && isset($s3Total) ? $s3Total : 0));

        const status = @json($logbook ? $logbook->status : '');
        const jumlahShiftSetting = @json($jumlahShiftSetting);

        let expectedCash = kasAwal;
        if (status === 'aktif' && jumlahShiftSetting == 1) {
            expectedCash = kasAwal + currentShift1;
        } else if (status === 'shift_1_selesai' && jumlahShiftSetting == 2) {
            expectedCash = kasAwal + shift1Total + currentShift2;
        } else if (status === 'shift_2_selesai') {
            expectedCash = kasAwal + shift1Total + shift2Total + currentShift3;
        }

        if (kasDiharapkanInfo) {
            kasDiharapkanInfo.innerHTML = `Jumlah uang kas laci yang seharusnya: <strong>Rp ${new Intl.NumberFormat('id-ID').format(expectedCash)}</strong>`;
        }
        
        if (kasAkhirInput) {
            if (kasAkhirInput.dataset.auto !== 'false') {
                kasAkhirInput.value = expectedCash;
                kasAkhirInput.dataset.auto = 'true';
            }
            kasAkhirInput.addEventListener('input', () => {
                kasAkhirInput.dataset.auto = 'false';
            });
        }
    });
</script>
@endsection
