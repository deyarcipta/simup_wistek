@extends('admin.layouts.app')

@section('title', 'Pengaturan')

@section('content')
<form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h5 class="text-white-50 mb-1"><i class="bx bx-slider-alt me-1"></i> Pengaturan Aplikasi</h5>
                        <h2 class="text-white mb-0 fw-bold">Pengaturan Sistem UP</h2>
                        <p class="mb-0 mt-1 text-white-50" style="font-size: 0.85rem; max-width: 600px;">
                            Konfigurasi nama aplikasi, logo unit usaha, batas toleransi keterlambatan shift, jam mulai kerja, serta persentase bagi hasil SHU.
                        </p>
                    </div>
                    <div class="d-none d-md-block text-white" style="font-size: 5rem; opacity: 0.25; line-height: 1;">
                        <i class="bx bx-slider-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Kolom Pengaturan Umum --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Pengaturan Umum</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Aplikasi</label>
                        <input type="text" name="nama_aplikasi" class="form-control" value="{{ old('nama_aplikasi', $pengaturan->nama_aplikasi ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Sekolah</label>
                        <input type="text" name="nama_sekolah" class="form-control" value="{{ old('nama_sekolah', $pengaturan->nama_sekolah ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $pengaturan->alamat ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $pengaturan->telepon ?? '') }}">
                    </div>

                    
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="bx bx-key me-1"></i> Wistek Topup Webhook Secret Key</label>
                        <input type="text" name="wistek_webhook_secret" class="form-control" value="{{ old('wistek_webhook_secret', $pengaturan->wistek_webhook_secret ?? 'wistek_simup_secret_key_2026') }}" placeholder="wistek_simup_secret_key_2026">
                        <small class="text-muted">Token rahasia autentikasi webhook transaksi otomatis dari Wistek Topup.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $pengaturan->email ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Shift Logbook</label>
                        <select name="jumlah_shift" id="jumlah_shift_select" class="form-select">
                            <option value="1" {{ old('jumlah_shift', $pengaturan->jumlah_shift ?? 2) == 1 ? 'selected' : '' }}>1 Shift (Langsung Tutup Hari)</option>
                            <option value="2" {{ old('jumlah_shift', $pengaturan->jumlah_shift ?? 2) == 2 ? 'selected' : '' }}>2 Shift (Shift 1 & Shift 2)</option>
                            <option value="3" {{ old('jumlah_shift', $pengaturan->jumlah_shift ?? 2) == 3 ? 'selected' : '' }}>3 Shift (Shift 1, Shift 2, & Shift 3)</option>
                        </select>
                        <small class="text-muted d-block mt-1">Menentukan alur logbook harian unit produksi Wistek.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="bx bx-time me-1"></i> Toleransi Spare Waktu Keterlambatan (Menit)</label>
                        <div class="input-group">
                            <input type="number" name="toleransi_keterlambatan" class="form-control" value="{{ old('toleransi_keterlambatan', $pengaturan->toleransi_keterlambatan ?? 15) }}" min="0" max="120" required>
                            <span class="input-group-text">Menit</span>
                        </div>
                        <small class="text-muted">Batas spare waktu toleransi sebelum operator dicatat keterlambatan.</small>
                    </div>

                    @php
                        $jumlahShiftVal = old('jumlah_shift', $pengaturan->jumlah_shift ?? 2);
                    @endphp
                    <div class="card bg-light border mb-3">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-2"><i class="bx bx-calendar-event me-1"></i> Jam Mulai Shift Operator</h6>
                            <div class="row g-2" id="shift_start_inputs_row">
                                <div class="{{ $jumlahShiftVal == 1 ? 'col-md-12' : ($jumlahShiftVal == 2 ? 'col-md-6' : 'col-md-4') }}" id="col_shift1_mulai">
                                    <label class="form-label small mb-1 fw-semibold">Shift 1 Mulai</label>
                                    <input type="time" name="shift1_mulai" class="form-control form-control-sm" value="{{ old('shift1_mulai', $pengaturan->shift1_mulai ?? '07:00') }}" required>
                                </div>
                                <div class="{{ $jumlahShiftVal == 2 ? 'col-md-6' : 'col-md-4' }}" id="col_shift2_mulai" style="{{ $jumlahShiftVal < 2 ? 'display: none;' : '' }}">
                                    <label class="form-label small mb-1 fw-semibold">Shift 2 Mulai</label>
                                    <input type="time" name="shift2_mulai" class="form-control form-control-sm" value="{{ old('shift2_mulai', $pengaturan->shift2_mulai ?? '11:00') }}">
                                </div>
                                <div class="col-md-4" id="col_shift3_mulai" style="{{ $jumlahShiftVal < 3 ? 'display: none;' : '' }}">
                                    <label class="form-label small mb-1 fw-semibold">Shift 3 Mulai</label>
                                    <input type="time" name="shift3_mulai" class="form-control form-control-sm" value="{{ old('shift3_mulai', $pengaturan->shift3_mulai ?? '12:00') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" class="form-control">
                        @if(!empty($pengaturan->logo))
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$pengaturan->logo) }}" alt="Logo" style="max-height: 80px;" class="rounded border">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Pengaturan SHU --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pengaturan Pembagian SHU</h5>
                    <button type="button" class="btn btn-sm btn-success" id="btn-tambah-penerima">
                        <i class="bx bx-plus"></i> Tambah
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Penerima</th>
                                    <th style="width: 165px;">Persentase</th>
                                    <th style="width: 60px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="shu-container">
                                @php
                                    $shuPembagian = $pengaturan ? $pengaturan->getShuPembagianOrDefault() : [
                                        ['penerima' => 'Jurusan TKJ',   'persentase' => 40],
                                        ['penerima' => 'Unit Produksi', 'persentase' => 30],
                                        ['penerima' => 'Sekolah',       'persentase' => 20],
                                        ['penerima' => 'Honor Pegawai', 'persentase' => 10],
                                    ];
                                @endphp
                                @foreach($shuPembagian as $index => $item)
                                <tr>
                                    <td>
                                        <input type="text" name="shu_penerima[]" class="form-control" value="{{ old('shu_penerima.'.$index, $item['penerima']) }}" required placeholder="Nama Penerima">
                                    </td>
                                    <td>
                                        <style>
                                            .shu-persentase-input::-webkit-outer-spin-button,
                                            .shu-persentase-input::-webkit-inner-spin-button {
                                                -webkit-appearance: none;
                                                margin: 0;
                                            }
                                            .shu-persentase-input {
                                                -moz-appearance: textfield;
                                                padding-left: 6px !important;
                                                padding-right: 6px !important;
                                                font-weight: 600;
                                            }
                                        </style>
                                        <div class="input-group">
                                            <input type="number" name="shu_persentase[]" class="form-control text-center shu-persentase-input" value="{{ old('shu_persentase.'.$index, $item['persentase']) }}" required min="0" max="100" step="any" placeholder="0">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger btn-hapus-penerima">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center bg-light p-2 rounded">
                        <div>
                            <strong>Total Persentase: </strong><span id="total-persentase" class="fw-bold fs-5 text-danger">0</span>%
                        </div>
                        <span id="total-warning" class="badge bg-label-danger">Total harus 100%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary btn-lg px-5">Simpan Pengaturan</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('shu-container');
        const btnTambah = document.getElementById('btn-tambah-penerima');
        const totalSpan = document.getElementById('total-persentase');
        const totalWarning = document.getElementById('total-warning');

        function hitungTotal() {
            let total = 0;
            const inputs = document.querySelectorAll('.shu-persentase-input');
            inputs.forEach(input => {
                const val = parseFloat(input.value) || 0;
                total += val;
            });
            total = Math.round(total * 10000) / 10000;
            totalSpan.textContent = total;
            
            if (Math.abs(total - 100) > 0.0001) {
                totalSpan.classList.add('text-danger');
                totalSpan.classList.remove('text-success');
                if (totalWarning) {
                    totalWarning.className = 'badge bg-label-danger';
                    totalWarning.textContent = 'Total harus 100%';
                }
            } else {
                totalSpan.classList.add('text-success');
                totalSpan.classList.remove('text-danger');
                if (totalWarning) {
                    totalWarning.className = 'badge bg-label-success';
                    totalWarning.textContent = 'Total Sesuai (100%)';
                }
            }
        }

        btnTambah.addEventListener('click', function () {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <input type="text" name="shu_penerima[]" class="form-control" value="" required placeholder="Nama Penerima">
                </td>
                <td>
                    <div class="input-group">
                        <input type="number" name="shu_persentase[]" class="form-control text-center shu-persentase-input" value="0" required min="0" max="100" step="any" placeholder="0">
                        <span class="input-group-text">%</span>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger btn-hapus-penerima">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            `;
            container.appendChild(tr);
            hitungTotal();
        });

        container.addEventListener('click', function (e) {
            if (e.target.closest('.btn-hapus-penerima')) {
                const tr = e.target.closest('tr');
                tr.remove();
                hitungTotal();
            }
        });

        container.addEventListener('input', function (e) {
            if (e.target.classList.contains('shu-persentase-input')) {
                hitungTotal();
            }
        });

        // --- SHIFT HOURS & INPUT VISIBILITY PREVIEW ---
        const shiftSelect = document.getElementById('jumlah_shift_select');
        const colShift1 = document.getElementById('col_shift1_mulai');
        const colShift2 = document.getElementById('col_shift2_mulai');
        const colShift3 = document.getElementById('col_shift3_mulai');
        
        function updateShiftVisibility() {
            if (!shiftSelect) return;
            const val = parseInt(shiftSelect.value);

            if (val === 1) {
                if (colShift1) { colShift1.className = 'col-md-12'; colShift1.style.display = 'block'; }
                if (colShift2) { colShift2.style.display = 'none'; }
                if (colShift3) { colShift3.style.display = 'none'; }
            } else if (val === 2) {
                if (colShift1) { colShift1.className = 'col-md-6'; colShift1.style.display = 'block'; }
                if (colShift2) { colShift2.className = 'col-md-6'; colShift2.style.display = 'block'; }
                if (colShift3) { colShift3.style.display = 'none'; }
            } else {
                if (colShift1) { colShift1.className = 'col-md-4'; colShift1.style.display = 'block'; }
                if (colShift2) { colShift2.className = 'col-md-4'; colShift2.style.display = 'block'; }
                if (colShift3) { colShift3.className = 'col-md-4'; colShift3.style.display = 'block'; }
            }
        }
        
        if (shiftSelect) {
            shiftSelect.addEventListener('change', updateShiftVisibility);
            updateShiftVisibility();
        }

        hitungTotal();
    });
</script>
@endpush
@endsection
