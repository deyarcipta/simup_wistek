@extends('admin.layouts.app')

@section('title', 'Pengaturan')

@section('content')
<form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

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
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $pengaturan->email ?? '') }}">
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
                                    <th style="width: 130px;">Persentase</th>
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

        hitungTotal();
    });
</script>
@endpush
@endsection
