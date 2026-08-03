@extends('operator.layouts.app')

@section('title', 'Produk & Jasa')

@section('content')
<div class="row">
    <div class="col-12">
        {{-- Card Utama --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Daftar Produk & Jasa</h5>
                {{-- <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="bx bx-plus"></i> Tambah Produk/Jasa
                </button> --}}
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
                                {{-- <th style="width: 120px;">Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($produkJasa as $item)
                                <tr>
                                    <td>{{ $produkJasa->firstItem() + $loop->index }}</td>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $produkJasa->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
