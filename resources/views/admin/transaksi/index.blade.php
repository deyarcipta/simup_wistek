@extends('admin.layouts.app')
@section('title', 'Transaksi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Transaksi</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('transaksi.download') }}" class="btn btn-success btn-sm">
                <i class="bx bx-download"></i> Download
            </a>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTransaksi">
                <i class="bx bx-plus"></i> Tambah Transaksi
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Pembeli</th>
                        <th>Total</th>
                        <th>Pembuat</th>
                        <th>Detail</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($transaksi as $trx)
                    <tr>
                        <td>{{ $trx->kode_transaksi }}</td>
                        <td>{{ $trx->tanggal }}</td>
                        <td>{{ $trx->nama_pembeli }}</td>
                        <td>Rp {{ number_format($trx->total,0,',','.') }}</td>
                        <td>{{ $trx->user->name ?? '-' }}</td>
                        <td>
                            <ul>
                                @foreach($trx->details as $d)
                                    <li>{{ $d->produkJasa->nama }} ({{ $d->jumlah }} x Rp {{ number_format($d->harga,0,',','.') }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <form action="{{ route('transaksi.destroy',$trx->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">Belum ada transaksi</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Transaksi -->
<div class="modal fade" id="modalTransaksi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('transaksi.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Pembeli</label>
                        <input type="text" name="nama_pembeli" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Produk / Jasa</label>
                        <select name="produk_jasa_id" id="produk_jasa_id" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            @foreach($produkJasa as $p)
                                <option value="{{ $p->id }}">{{ $p->nama }} (Rp {{ number_format($p->harga,0,',','.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" min="1" required>
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
