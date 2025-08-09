@extends('admin.layouts.app')

@section('title', 'Produk & Jasa')

@section('content')
<div class="row">
    <div class="col-12">
        {{-- Card Utama --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Daftar Produk & Jasa</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="bx bx-plus"></i> Tambah Produk/Jasa
                </button>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success mb-3">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Harga</th>
                                <th>Stok/Jumlah</th>
                                <th>Satuan</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($produkJasa as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td><span class="badge bg-label-info">{{ ucfirst($item->jenis) }}</span></td>
                                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td>
                                        @if($item->jenis === 'produk')
                                            {{ $item->stokBarang->stok ?? '-' }}
                                        @else
                                            {{ $item->jumlah ?? '-' }}
                                        @endif
                                    </td>
                                    <td>{{ $item->satuan ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm btnEdit" data-id="{{ $item->id }}">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.produk-jasa.destroy', $item->id) }}" method="POST" style="display:inline-block;">
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
            <form action="{{ route('admin.produk-jasa.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk/Jasa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.produk_jasa.partials.form')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                    <h5 class="modal-title">Edit Produk/Jasa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <input type="hidden" id="edit_stok_barang_id" name="stok_barang_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" id="edit_nama" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <select id="edit_jenis" name="jenis" class="form-control" required>
                            <option value="produk">Produk</option>
                            <option value="jasa">Jasa</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" id="edit_harga" name="harga" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok (kosongkan untuk jasa)</label>
                        <input type="number" id="edit_stok" name="stok" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <input type="text" id="edit_satuan" name="satuan" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
            const id = this.getAttribute('data-id');
            const url = `{{ url('admin/produk-jasa/data') }}/${id}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('edit_nama').value = data.nama ?? '';
                    document.getElementById('edit_jenis').value = data.jenis ?? '';
                    document.getElementById('edit_harga').value = data.harga ?? '';
                    document.getElementById('edit_stok').value = data.stok ?? '';
                    document.getElementById('edit_satuan').value = data.satuan ?? '';

                    document.getElementById('formEdit').setAttribute('action', `{{ url('admin/produk-jasa') }}/${id}`);

                    const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
                    modal.show();
                })
                .catch(err => console.error("Gagal ambil data:", err));
        });
    });
});

</script>
@endpush
@endsection
