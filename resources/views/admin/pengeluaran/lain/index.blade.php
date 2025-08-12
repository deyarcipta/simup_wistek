@extends('admin.layouts.app')

@section('title', 'Pengeluaran Lain')

@section('content')
<div class="row">
    <div class="col-12">
        {{-- Card Utama --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Daftar Pengeluaran Lain</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="bx bx-plus"></i> Tambah Pengeluaran
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
                                <th>Keterangan</th>
                                <th>Total</th>
                                <th>Tanggal</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengeluaran as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                    <td>{{ $item->tanggal }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm btnEdit" data-id="{{ $item->id }}">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <form action="{{ route('pengeluaran-lain.destroy', $item->id) }}" method="POST" style="display:inline-block;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data</td>
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
            <form action="{{ route('pengeluaran-lain.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengeluaran Lain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Bayar Piutang (Opsional)</label>
                        <select name="piutang_id" class="form-select">
                            <option value="">-- Pilih jika untuk pembayaran piutang --</option>
                            @foreach ($piutangList as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_barang }} - {{ $p->kepada }} - Rp {{ number_format($p->sisa_nominal, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total</label>
                        <input type="number" name="total" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
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
                    <h5 class="modal-title">Edit Pengeluaran Lain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" id="edit_keterangan" name="keterangan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total</label>
                        <input type="number" id="edit_total" name="total" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" id="edit_tanggal" name="tanggal" class="form-control" required>
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
            const url = `{{ url('admin/pengeluaran-lain/data') }}/${id}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('edit_keterangan').value = data.keterangan ?? '';
                    document.getElementById('edit_total').value = data.total ?? '';
                    document.getElementById('edit_tanggal').value = data.tanggal ?? '';

                    document.getElementById('formEdit').setAttribute('action', `{{ url('admin/pengeluaran-lain') }}/${id}`);

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
