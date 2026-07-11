@extends('operator.layouts.app')
@section('title', 'Logbook Shift Hari Ini')

@section('content')
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
                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow"><i class="bx bx-play-circle me-1"></i> Mulai Shift 1 Pagi</button>
                    </form>
                </div>
            </div>
        
        {{-- CASE 2: SHIFT 1 PAGI SEDANG BERJALAN --}}
        @elseif($logbook->status === 'aktif')
            @if($isDifferentOperator)
                <div class="card shadow border-0 mt-4 overflow-hidden">
                    <div class="card-header bg-warning text-dark text-center py-4">
                        <i class="bx bx-lock-alt mb-2" style="font-size: 3.5rem;"></i>
                        <h4 class="text-dark mb-0 fw-bold">Shift 1 Pagi Terkunci</h4>
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
                        <h4 class="text-dark mb-0 fw-bold">Shift 1 Pagi</h4>
                        <p class="mb-0 text-dark-50">Sedang Berjalan - Batas Akhir Jam 11.00</p>
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
                            
                            <div class="p-3 bg-light rounded-3 mb-4">
                                <h6 class="fw-bold text-dark mb-3"><i class="bx bx-receipt"></i> Rincian Transaksi Shift 1 <span class="badge bg-label-primary ms-1">Otomatis dari POS</span></h6>
                                
                                {{-- Print --}}
                                <div class="row align-items-center mb-3">
                                    <div class="col-sm-5">
                                        <label class="form-label mb-sm-0">Print Hitam Putih (lbr)</label>
                                        <small class="d-block text-muted">Tarif: Rp {{ number_format($tarifPrint,0,',','.') }}</small>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="number" name="jumlah_print" id="s1-print" class="form-control calc-input bg-white text-dark fw-semibold" 
                                               value="{{ old('jumlah_print', $autoFill['print']) }}" readonly min="0" required>
                                    </div>
                                </div>

                                {{-- Fotokopi --}}
                                <div class="row align-items-center mb-3">
                                    <div class="col-sm-5">
                                        <label class="form-label mb-sm-0">Fotokopi Hitam (lbr)</label>
                                        <small class="d-block text-muted">Tarif: Rp {{ number_format($tarifFotokopi,0,',','.') }}</small>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="number" name="jumlah_fotokopi" id="s1-copy" class="form-control calc-input bg-white text-dark fw-semibold" 
                                               value="{{ old('jumlah_fotokopi', $autoFill['fotokopi']) }}" readonly min="0" required>
                                    </div>
                                </div>

                                {{-- Jilid --}}
                                <div class="row align-items-center mb-3">
                                    <div class="col-sm-5">
                                        <label class="form-label mb-sm-0">Jilid Makalah (buku)</label>
                                        <small class="d-block text-muted">Tarif: Rp {{ number_format($tarifJilid,0,',','.') }}</small>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="number" name="jumlah_jilid" id="s1-jilid" class="form-control calc-input bg-white text-dark fw-semibold" 
                                               value="{{ old('jumlah_jilid', $autoFill['jilid']) }}" readonly min="0" required>
                                    </div>
                                </div>

                                {{-- Pendapatan Lainnya --}}
                                <div class="row align-items-center">
                                    <div class="col-sm-5">
                                        <label class="form-label mb-sm-0">Pendapatan Lain (Retail/Pulpen/dll)</label>
                                        <small class="d-block text-muted">Total transaksi produk & jasa lain</small>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" name="pendapatan_lain" id="s1-lain" class="form-control calc-input bg-white text-dark fw-semibold" 
                                                   value="{{ old('pendapatan_lain', $autoFill['lain']) }}" readonly min="0" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 mb-4 bg-white">
                                <span class="fw-semibold text-muted">Total Pendapatan Shift 1:</span>
                                <span class="h4 mb-0 fw-bold text-warning" id="total-shift-1">Rp 0</span>
                            </div>

                            <button type="submit" class="btn btn-warning btn-lg w-100 text-dark fw-bold shadow">
                                <i class="bx bx-check-circle me-1"></i> Selesai Shift 1
                            </button>
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
                        <h4 class="text-white mb-0 fw-bold">Mulai Shift 2 Siang</h4>
                        <p class="mb-0 text-white-50">Langkah berikutnya: Aktifkan shift siang hari ini</p>
                    </div>
                    <div class="card-body py-4">
                        {{-- Summary Shift 1 --}}
                        <div class="card bg-label-info border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2 text-info"><i class="bx bx-check-double"></i> Laporan Shift 1 Pagi:</h6>
                                <ul class="mb-0 text-dark-50 ps-3" style="font-size: 0.9rem;">
                                    <li>Operator: <strong>{{ $shift1->user->name ?? '-' }}</strong></li>
                                    <li>Pekerjaan: {{ $shift1->jumlah_print }} Print, {{ $shift1->jumlah_fotokopi }} Fotokopi, {{ $shift1->jumlah_jilid }} Jilid</li>
                                    <li>Total Kas Shift 1: <strong>Rp {{ number_format($shift1->total_uang, 0, ',', '.') }}</strong></li>
                                </ul>
                            </div>
                        </div>

                        <form action="{{ route('operator.logbook.start_shift2') }}" method="POST">
                            @csrf
                            <div class="mb-4 text-center">
                                <p class="text-muted" style="font-size: 0.95rem;">
                                    Tekan tombol di bawah untuk memulai Shift 2 Siang. Akun Anda akan didaftarkan sebagai operator penanggung jawab Shift 2 hari ini.
                                </p>
                            </div>
                            <button type="submit" class="btn btn-info btn-lg w-100 shadow">
                                <i class="bx bx-play-circle me-1"></i> Mulai Shift 2 Siang
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($isDifferentOperatorShift2)
                <div class="card shadow border-0 mt-4 overflow-hidden">
                    <div class="card-header bg-info text-white text-center py-4">
                        <i class="bx bx-lock-alt mb-2" style="font-size: 3.5rem;"></i>
                        <h4 class="text-white mb-0 fw-bold">Shift 2 Siang Terkunci</h4>
                        <p class="mb-0 text-white-50">Sedang Berjalan oleh {{ $operatorNameShift2 }}</p>
                    </div>
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bx bx-lock-alt text-info animate-bounce" style="font-size: 5rem;"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Shift 2 Dimulai Oleh Operator Lain!</h4>
                        <p class="text-muted mx-auto" style="max-width: 500px;">
                            Shift 2 Siang hari ini telah diaktifkan oleh operator <strong>{{ $operatorNameShift2 }}</strong>.
                            Anda tidak dapat mengakses, mengisi, atau menutup hari operasional ini sebelum operator <strong>{{ $operatorNameShift2 }}</strong> melakukan Tutup UP.
                        </p>
                    </div>
                </div>
            @else
                <div class="card shadow border-0 overflow-hidden">
                    <div class="card-header bg-info text-white text-center py-4">
                        <i class="bx bx-cloud-light-rain mb-2" style="font-size: 3.5rem;"></i>
                        <h4 class="text-white mb-0 fw-bold">Shift 2 Siang</h4>
                        <p class="mb-0 text-white-50">Sedang Berjalan - Jam Tutup UP: 15.00</p>
                    </div>
                    <div class="card-body py-4">
                        {{-- Summary Shift 1 --}}
                        <div class="card bg-label-info border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2 text-info"><i class="bx bx-check-double"></i> Data Operasional Shift 1 Pagi:</h6>
                                <ul class="mb-0 text-dark-50 ps-3" style="font-size: 0.9rem;">
                                    <li>Operator: <strong>{{ $shift1->user->name ?? '-' }}</strong></li>
                                    <li>Pekerjaan: {{ $shift1->jumlah_print }} Print, {{ $shift1->jumlah_fotokopi }} Fotokopi, {{ $shift1->jumlah_jilid }} Jilid</li>
                                    <li>Uang Diterima: <strong>Rp {{ number_format($shift1->total_uang, 0, ',', '.') }}</strong></li>
                                </ul>
                            </div>
                        </div>

                        <form action="{{ route('operator.logbook.shift2') }}" method="POST" id="form-shift-2">
                            @csrf
                            
                            <div class="p-3 bg-light rounded-3 mb-4">
                                <h6 class="fw-bold text-dark mb-3"><i class="bx bx-receipt"></i> Rincian Transaksi Shift 2 <span class="badge bg-label-primary ms-1">Otomatis dari POS</span></h6>
                                
                                {{-- Print --}}
                                <div class="row align-items-center mb-3">
                                    <div class="col-sm-5">
                                        <label class="form-label mb-sm-0">Print Hitam Putih (lbr)</label>
                                        <small class="d-block text-muted">Tarif: Rp {{ number_format($tarifPrint,0,',','.') }}</small>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="number" name="jumlah_print" id="s2-print" class="form-control calc-input bg-white text-dark fw-semibold" 
                                               value="{{ old('jumlah_print', $autoFill['print']) }}" readonly min="0" required>
                                    </div>
                                </div>

                                {{-- Fotokopi --}}
                                <div class="row align-items-center mb-3">
                                    <div class="col-sm-5">
                                        <label class="form-label mb-sm-0">Fotokopi Hitam (lbr)</label>
                                        <small class="d-block text-muted">Tarif: Rp {{ number_format($tarifFotokopi,0,',','.') }}</small>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="number" name="jumlah_fotokopi" id="s2-copy" class="form-control calc-input bg-white text-dark fw-semibold" 
                                               value="{{ old('jumlah_fotokopi', $autoFill['fotokopi']) }}" readonly min="0" required>
                                    </div>
                                </div>

                                {{-- Jilid --}}
                                <div class="row align-items-center mb-3">
                                    <div class="col-sm-5">
                                        <label class="form-label mb-sm-0">Jilid Makalah (buku)</label>
                                        <small class="d-block text-muted">Tarif: Rp {{ number_format($tarifJilid,0,',','.') }}</small>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="number" name="jumlah_jilid" id="s2-jilid" class="form-control calc-input bg-white text-dark fw-semibold" 
                                               value="{{ old('jumlah_jilid', $autoFill['jilid']) }}" readonly min="0" required>
                                    </div>
                                </div>

                                {{-- Pendapatan Lainnya --}}
                                <div class="row align-items-center">
                                    <div class="col-sm-5">
                                        <label class="form-label mb-sm-0">Pendapatan Lain (Retail/Pulpen/dll)</label>
                                        <small class="d-block text-muted">Total transaksi produk & jasa lain</small>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" name="pendapatan_lain" id="s2-lain" class="form-control calc-input bg-white text-dark fw-semibold" 
                                                   value="{{ old('pendapatan_lain', $autoFill['lain']) }}" readonly min="0" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 mb-4 bg-white">
                                <span class="fw-semibold text-muted">Total Pendapatan Shift 2:</span>
                                <span class="h4 mb-0 fw-bold text-info" id="total-shift-2">Rp 0</span>
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

                            <button type="submit" class="btn btn-info btn-lg w-100 shadow">
                                <i class="bx bx-power-off me-1"></i> Tutup UP Hari Ini
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        {{-- CASE 4: UP SUDAH DITUTUP HARI INI --}}
        @elseif($logbook->status === 'tutup_up')
            @php
                $shift1 = $logbook->details->where('shift_id', 1)->first();
                $shift2 = $logbook->details->where('shift_id', 2)->first();
                $totalOmzet = ($shift1?->total_uang ?? 0) + ($shift2?->total_uang ?? 0);
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
                        <p class="text-muted">Semua laporan logbook shift telah dikunci dan dikirim ke Kaprog secara real-time.</p>
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
                            <div class="col-6 border-end">
                                <small class="text-muted d-block text-center">Shift 1 Pagi</small>
                                <div class="text-center fw-bold text-dark mt-1">Rp {{ number_format($shift1?->total_uang ?? 0, 0, ',', '.') }}</div>
                                <small class="d-block text-center text-muted-50" style="font-size: 0.75rem;">{{ $shift1?->user->name ?? '-' }}</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block text-center">Shift 2 Siang</small>
                                <div class="text-center fw-bold text-dark mt-1">Rp {{ number_format($shift2?->total_uang ?? 0, 0, ',', '.') }}</div>
                                <small class="d-block text-center text-muted-50" style="font-size: 0.75rem;">{{ $shift2?->user->name ?? '-' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

{{-- SCRIPT JAVASCRIPT FOR AUTO CALCULATE --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tarifPrint = {{ $tarifPrint }};
        const tarifCopy = {{ $tarifFotokopi }};
        const tarifJilid = {{ $tarifJilid }};

        // --- SHIFT 1 CALCULATION ---
        const s1Print = document.getElementById('s1-print');
        const s1Copy = document.getElementById('s1-copy');
        const s1Jilid = document.getElementById('s1-jilid');
        const s1Lain = document.getElementById('s1-lain');
        const totalS1Display = document.getElementById('total-shift-1');

        function calculateShift1() {
            if (!totalS1Display) return;
            const printVal = parseInt(s1Print.value) || 0;
            const copyVal = parseInt(s1Copy.value) || 0;
            const jilidVal = parseInt(s1Jilid.value) || 0;
            const lainVal = parseFloat(s1Lain.value) || 0;

            const total = (printVal * tarifPrint) + (copyVal * tarifCopy) + (jilidVal * tarifJilid) + lainVal;
            totalS1Display.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        }

        if (s1Print) {
            s1Print.addEventListener('input', calculateShift1);
            s1Copy.addEventListener('input', calculateShift1);
            s1Jilid.addEventListener('input', calculateShift1);
            s1Lain.addEventListener('input', calculateShift1);
            calculateShift1(); // Initial calc
        }

        // --- SHIFT 2 CALCULATION ---
        const s2Print = document.getElementById('s2-print');
        const s2Copy = document.getElementById('s2-copy');
        const s2Jilid = document.getElementById('s2-jilid');
        const s2Lain = document.getElementById('s2-lain');
        const totalS2Display = document.getElementById('total-shift-2');
        const kasAkhirInput = document.getElementById('kas_akhir');
        const kasDiharapkanInfo = document.getElementById('kas_diharapkan_info');

        // Parameter Kas Awal & Shift 1 Total untuk melengkapi info Kas Akhir
        const kasAwal = @json($logbook ? $logbook->kas_awal : 0);
        const shift1Total = @json($logbook && isset($shift1) ? $shift1->total_uang : 0);

        function calculateShift2() {
            if (!totalS2Display) return;
            const printVal = parseInt(s2Print.value) || 0;
            const copyVal = parseInt(s2Copy.value) || 0;
            const jilidVal = parseInt(s2Jilid.value) || 0;
            const lainVal = parseFloat(s2Lain.value) || 0;

            const totalS2 = (printVal * tarifPrint) + (copyVal * tarifCopy) + (jilidVal * tarifJilid) + lainVal;
            totalS2Display.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalS2);

            // Tampilkan rekomendasi Kas Akhir yang diharapkan
            const expectedCash = parseFloat(kasAwal) + parseFloat(shift1Total) + parseFloat(totalS2);
            kasDiharapkanInfo.innerHTML = `Jumlah uang kas laci yang seharusnya: <strong>Rp ${new Intl.NumberFormat('id-ID').format(expectedCash)}</strong>`;
            
            // Set default value kas_akhir jika belum diedit manual oleh user
            if (kasAkhirInput && kasAkhirInput.dataset.auto !== 'false') {
                kasAkhirInput.value = expectedCash;
                kasAkhirInput.dataset.auto = 'true';
            }
        }

        if (s2Print) {
            s2Print.addEventListener('input', calculateShift2);
            s2Copy.addEventListener('input', calculateShift2);
            s2Jilid.addEventListener('input', calculateShift2);
            s2Lain.addEventListener('input', calculateShift2);
            
            if (kasAkhirInput) {
                kasAkhirInput.addEventListener('input', () => {
                    kasAkhirInput.dataset.auto = 'false';
                });
            }
            
            calculateShift2(); // Initial calc
        }
    });
</script>
@endsection
