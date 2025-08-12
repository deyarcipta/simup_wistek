@extends('admin.layouts.app')

@section('title', 'Stok Barang')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Daftar Stok Barang</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="bx bx-plus"></i> Tambah Stok
                </button>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Stok</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stokBarang as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td>{{ $item->satuan ?? '-' }}</td>
                                    <td>{{ $item->stok }}</td>
                                    <td>Rp {{ number_format($item->harga_beli,0,',','.') }}</td>
                                    <td>Rp {{ number_format($item->harga_jual,0,',','.') }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm btnEdit" data-id="{{ $item->id }}">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <form action="{{ route('stok-barang.destroy', $item->id) }}" method="POST" style="display:inline-block;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Create --}}
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('stok-barang.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Stok Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.stok_barang.partials.form')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Stok Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.stok_barang.partials.form')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btnEdit').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            fetch(`{{ url('admin/stok-barang/data') }}/${id}`)
                .then(res => res.json())
                .then(data => {
                    const formEdit = document.getElementById('formEdit');
                    formEdit.querySelector('input[name="nama_barang"]').value = data.nama_barang;
                    formEdit.querySelector('input[name="satuan"]').value = data.satuan ?? '';
                    formEdit.querySelector('input[name="stok"]').value = data.stok;
                    formEdit.querySelector('input[name="harga_beli"]').value = data.harga_beli ?? '';
                    formEdit.querySelector('input[name="harga_jual"]').value = data.harga_jual ?? '';
                    formEdit.setAttribute('action', `{{ url('admin/stok-barang') }}/${id}`);
                    new bootstrap.Modal(document.getElementById('modalEdit')).show();
                });
        });
    });
});
</script>
@endpush
@endsection
