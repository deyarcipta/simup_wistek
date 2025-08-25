@extends('admin.layouts.app')
@section('title', 'Piutang')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Daftar Piutang</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bx bx-plus"></i> Tambah Piutang
            </button>
        </div>
    </div>
    <div class="card-body">
        {{-- @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif --}}


        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Barang / Kebutuhan</th>
                        <th>Jumlah Barang</th>
                        <th>Nominal</th>
                        <th>Sisa Nominal</th>
                        <th>Kepada</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td>{{ $item->tanggal_peminjaman }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->jumlah_barang }}</td>
                        <td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->sisa_nominal, 0, ',', '.') }}</td>
                        <td>{{ $item->kepada }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $item->id }}">
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('piutang.update', $item->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Piutang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Tanggal Peminjaman</label>
                                            <input type="date" name="tanggal_peminjaman" class="form-control" value="{{ $item->tanggal_peminjaman }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Nama Barang / Kebutuhan</label>
                                            <input type="text" name="nama_barang" class="form-control" value="{{ $item->nama_barang }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Jumlah Barang</label>
                                            <input type="number" name="jumlah_barang" class="form-control" value="{{ $item->jumlah_barang }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Nominal</label>
                                            <input type="number" name="nominal" class="form-control" value="{{ $item->nominal }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Kepada Siapa</label>
                                            <input type="text" name="kepada" class="form-control" value="{{ $item->kepada }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Hapus -->
                    <div class="modal fade" id="modalHapus{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('piutang.destroy', $item->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Hapus Piutang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Yakin ingin menghapus data piutang <strong>{{ $item->nama_barang }}</strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-danger">Hapus</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data piutang</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('piutang.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Piutang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Tanggal Peminjaman</label>
                        <input type="date" name="tanggal_peminjaman" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Barang / Kebutuhan</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Barang</label>
                        <input type="number" name="jumlah_barang" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label>Nominal</label>
                        <input type="number" name="nominal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Kepada Siapa</label>
                        <input type="text" name="kepada" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
