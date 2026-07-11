@extends('operator.layouts.app')
@section('title', 'Mesin Kasir & Transaksi')

@section('content')
@if(!$hasStartedLogbook)
<div class="row">
    <div class="col-12">
        <div class="card shadow border-0 text-center py-5">
            <div class="card-body">
                <div class="mb-4">
                    <i class="bx bx-lock-alt text-warning animate-bounce" style="font-size: 5rem;"></i>
                </div>
                <h3 class="fw-bold text-dark mb-2">Hari Operasional Belum Dimulai!</h3>
                <p class="text-muted mx-auto" style="max-width: 500px;">
                    Maaf, operator tidak dapat menambahkan transaksi sebelum memulai logbook harian terlebih dahulu. Silakan masuk ke menu "Logbook Hari Ini" untuk memulai shift.
                </p>
                <div class="mt-4">
                    <a href="{{ route('operator.logbook.index') }}" class="btn btn-warning text-dark fw-bold btn-lg shadow-sm">
                        <i class="bx bx-book-open me-1"></i> Buka Menu Logbook Hari Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-12">
        {{-- Navigation Tabs --}}
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-kasir-tab" data-bs-toggle="pill" data-bs-target="#pills-kasir" type="button" role="tab" aria-controls="pills-kasir" aria-selected="true">
                    <i class="bx bx-calculator me-1"></i> Mesin Kasir
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-riwayat-tab" data-bs-toggle="pill" data-bs-target="#pills-riwayat" type="button" role="tab" aria-controls="pills-riwayat" aria-selected="false">
                    <i class="bx bx-history me-1"></i> Riwayat Transaksi
                </button>
            </li>
        </ul>

        <div class="tab-content p-0" id="pills-tabContent">
            {{-- TAB 1: MESIN KASIR --}}
            <div class="tab-pane fade show active" id="pills-kasir" role="tabpanel" aria-labelledby="pills-kasir-tab">
                <div class="row">
                    {{-- ETALASE KIRI --}}
                    <div class="col-lg-8 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header pb-2 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                                <h5 class="card-title mb-0">Etalase Produk & Jasa</h5>
                                <div class="d-flex gap-2 w-100 w-sm-auto">
                                    <input type="text" id="search-etalase" class="form-control form-control-sm" placeholder="Cari layanan...">
                                </div>
                            </div>
                            
                            {{-- Filter Kategori --}}
                            <div class="px-4 pb-3">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-xs btn-outline-primary active filter-btn" data-filter="semua">Semua</button>
                                    <button class="btn btn-xs btn-outline-primary filter-btn" data-filter="jasa">Jasa</button>
                                    <button class="btn btn-xs btn-outline-primary filter-btn" data-filter="produk">Produk</button>
                                </div>
                            </div>

                            <div class="card-body pt-0">
                                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 etalase-grid" style="max-height: 580px; overflow-y: auto; padding: 5px;">
                                    @foreach($produkJasa as $item)
                                        @php
                                            $stok = $item->jenis === 'produk' ? ($item->stokBarang?->stok ?? 0) : null;
                                            $isOutOfStock = $item->jenis === 'produk' && $stok <= 0;
                                        @endphp
                                        <div class="col etalase-item" 
                                             data-id="{{ $item->id }}" 
                                             data-nama="{{ $item->nama }}" 
                                             data-harga="{{ $item->harga }}" 
                                             data-jenis="{{ $item->jenis }}"
                                             data-stok="{{ $stok ?? 99999 }}">
                                            <div class="card h-100 border rounded-3 p-3 text-center position-relative etalase-card shadow-sm cursor-pointer {{ $isOutOfStock ? 'opacity-50 bg-light' : '' }}" 
                                                 style="transition: all 0.2s;"
                                                 onclick="{{ !$isOutOfStock ? 'addToCart('.$item->id.')' : '' }}">
                                                
                                                {{-- Badge Jenis --}}
                                                <span class="badge position-absolute top-0 start-50 translate-middle-y {{ $item->jenis === 'jasa' ? 'bg-info' : 'bg-success' }}" style="font-size: 0.7rem;">
                                                    {{ strtoupper($item->jenis) }}
                                                </span>
                                                
                                                <div class="mt-2 mb-1 fw-bold text-dark text-truncate" title="{{ $item->nama }}" style="font-size: 0.95rem;">
                                                    {{ $item->nama }}
                                                </div>
                                                
                                                <div class="text-primary fw-semibold mb-2" style="font-size: 0.9rem;">
                                                    Rp {{ number_format($item->harga, 0, ',', '.') }}
                                                </div>
                                                
                                                <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                                    @if($item->jenis === 'produk')
                                                        <small class="text-muted" style="font-size: 0.75rem;">
                                                            Stok: <span class="fw-bold text-{{ $isOutOfStock ? 'danger' : 'success' }}">{{ $stok }} {{ $item->satuan ?? 'pcs' }}</span>
                                                        </small>
                                                    @else
                                                        <small class="text-muted" style="font-size: 0.75rem;">
                                                            Layanan Jasa
                                                        </small>
                                                    @endif
                                                    
                                                    @if($isOutOfStock)
                                                        <button class="btn btn-xs btn-secondary" disabled>Habis</button>
                                                    @else
                                                        <button class="btn btn-xs btn-primary"><i class="bx bx-plus"></i></button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

<<<<<<< HEAD
                    {{-- KERANJANG KANAN --}}
                    <div class="col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0 d-flex flex-column">
                            <div class="card-header pb-2 border-bottom">
                                <h5 class="card-title mb-0 d-flex align-items-center">
                                    <i class="bx bx-cart me-2 text-primary" style="font-size: 1.5rem;"></i> Keranjang Belanja
                                </h5>
                            </div>
                            
                            <div class="card-body flex-grow-1 overflow-y-auto py-2" id="cart-list" style="max-height: 400px; min-height: 250px;">
                                {{-- Ditampilkan via JS --}}
                                <div class="text-center text-muted my-5">
                                    <i class="bx bx-basket" style="font-size: 3rem;"></i>
                                    <p class="mt-2">Keranjang masih kosong.<br>Klik item di etalase untuk menambahkan.</p>
                                </div>
                            </div>

                            <div class="card-footer bg-light border-top mt-auto">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted fw-semibold">Total Pembayaran:</span>
                                    <span class="h4 mb-0 fw-bold text-primary" id="cart-total-display">Rp 0</span>
                                </div>
                                <button type="button" class="btn btn-primary w-100 btn-lg shadow-sm py-2" id="btn-checkout" onclick="checkoutCart()" disabled>
                                    <i class="bx bx-check-circle me-1"></i> Simpan &amp; Bayar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: RIWAYAT TRANSAKSI --}}
            <div class="tab-pane fade" id="pills-riwayat" role="tabpanel" aria-labelledby="pills-riwayat-tab">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Daftar Transaksi Terbaru</h5>
                    </div>
                    <div class="card-body">
                        {{-- Form Pencarian --}}
                        <div class="mb-3">
                            <form method="GET" action="{{ route('operator.transaksi.index') }}" class="d-flex gap-2">
                                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Cari kode transaksi atau pembeli...">
                                <button class="btn btn-secondary"><i class="bx bx-search"></i> Cari</button>
=======
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
>>>>>>> a15ff231cfb9940d0c52a73ea5d7ef585ab4bb2a
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Tanggal</th>
                                        <th>Pembeli</th>
                                        <th>Total</th>
                                        <th>Operator</th>
                                        <th>Rincian Item</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($transaksi as $trx)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $trx->kode_transaksi }}</td>
                                        <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                        <td><span class="badge bg-label-secondary">{{ $trx->nama_pembeli }}</span></td>
                                        <td class="fw-bold text-primary">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                                        <td>{{ $trx->user->name ?? '-' }}</td>
                                        <td>
                                            <ul class="mb-0 ps-3 text-muted" style="font-size: 0.85rem;">
                                                @foreach($trx->details as $d)
                                                    <li>{{ $d->produkJasa->nama }} ({{ $d->jumlah }} x Rp {{ number_format($d->harga, 0, ',', '.') }})</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>
                                            @if($trx->user_id === Auth::id() && $trx->created_at->isToday())
                                                <form action="{{ route('operator.transaksi.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Kuantitas stok barang akan dikembalikan.')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-icon btn-outline-danger btn-sm"><i class="bx bx-trash"></i></button>
                                                </form>
                                            @else
                                                <span class="text-muted text-nowrap" style="font-size: 0.85rem;" title="Hanya pembuat transaksi di hari yang sama yang dapat menghapus"><i class="bx bx-lock-alt"></i> Terkunci</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada data transaksi untuk pencarian ini.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-3 d-flex justify-content-center">
                            {{ $transaksi->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- CSS Custom untuk Efek Hover Etalase --}}
<style>
    .etalase-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important;
        border-color: #566a7f !important;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.25rem;
    }
</style>

{{-- Script Logika POS Cart --}}
<script>
    let cart = [];

    // Filter kategori etalase
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filterValue = this.getAttribute('data-filter');
            document.querySelectorAll('.etalase-item').forEach(item => {
                const itemJenis = item.getAttribute('data-jenis');
                if (filterValue === 'semua' || itemJenis === filterValue) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Fitur pencarian etalase
    document.getElementById('search-etalase').addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        document.querySelectorAll('.etalase-item').forEach(item => {
            const name = item.getAttribute('data-nama').toLowerCase();
            const activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
            const itemJenis = item.getAttribute('data-jenis');
            
            const matchesSearch = name.includes(query);
            const matchesFilter = activeFilter === 'semua' || itemJenis === activeFilter;
            
            if (matchesSearch && matchesFilter) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Tambah item ke Cart
    function addToCart(id) {
        const itemElement = document.querySelector(`.etalase-item[data-id="${id}"]`);
        if (!itemElement) return;

        const name = itemElement.getAttribute('data-nama');
        const price = parseFloat(itemElement.getAttribute('data-harga'));
        const type = itemElement.getAttribute('data-jenis');
        const stock = parseInt(itemElement.getAttribute('data-stok'));

        // Cari apakah item sudah ada di cart
        const existingItem = cart.find(item => item.id === id);
        if (existingItem) {
            if (type === 'produk' && existingItem.qty >= stock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Terbatas',
                    text: `Jumlah tidak boleh melebihi stok yang tersedia (${stock} pcs).`
                });
                return;
            }
            existingItem.qty++;
        } else {
            cart.push({
                id: id,
                name: name,
                price: price,
                qty: 1,
                type: type,
                stock: stock
            });
        }
        
        renderCart();
    }

    // Ubah kuantitas di cart
    function updateQty(id, change) {
        const item = cart.find(item => item.id === id);
        if (!item) return;

        const newQty = item.qty + change;
        if (newQty <= 0) {
            removeFromCart(id);
            return;
        }

        if (item.type === 'produk' && newQty > item.stock) {
            Swal.fire({
                icon: 'warning',
                title: 'Stok Terbatas',
                text: `Jumlah tidak boleh melebihi stok yang tersedia (${item.stock} pcs).`
            });
            return;
        }

        item.qty = newQty;
        renderCart();
    }

    // Hapus item dari cart
    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        renderCart();
    }

    // Render HTML Keranjang
    function renderCart() {
        const cartList = document.getElementById('cart-list');
        const btnCheckout = document.getElementById('btn-checkout');
        const totalDisplay = document.getElementById('cart-total-display');

        if (cart.length === 0) {
            cartList.innerHTML = `
                <div class="text-center text-muted my-5">
                    <i class="bx bx-basket" style="font-size: 3rem;"></i>
                    <p class="mt-2">Keranjang masih kosong.<br>Klik item di etalase untuk menambahkan.</p>
                </div>
            `;
            btnCheckout.disabled = true;
            totalDisplay.innerText = 'Rp 0';
            return;
        }

        let total = 0;
        let html = '<div class="list-group list-group-flush">';

        cart.forEach(item => {
            const subtotal = item.price * item.qty;
            total += subtotal;

            html += `
                <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom">
                    <div style="max-width: 60%;">
                        <div class="fw-bold text-dark text-truncate">${item.name}</div>
                        <small class="text-muted">Rp ${formatRupiah(item.price)}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-xs btn-outline-secondary px-2" onclick="updateQty(${item.id}, -1)">-</button>
                        <span class="fw-bold text-dark" style="min-width: 20px; text-align: center;">${item.qty}</span>
                        <button class="btn btn-xs btn-outline-secondary px-2" onclick="updateQty(${item.id}, 1)">+</button>
                        <button class="btn btn-xs btn-outline-danger border-0 ms-2" onclick="removeFromCart(${item.id})">
                            <i class="bx bx-trash" style="font-size: 1.1rem;"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        cartList.innerHTML = html;
        totalDisplay.innerText = 'Rp ' + formatRupiah(total);
        btnCheckout.disabled = false;
    }

    // Fungsi Format Rupiah
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    // Kirim AJAX checkout
    function checkoutCart() {
        if (cart.length === 0) return;

        const btnCheckout = document.getElementById('btn-checkout');
        btnCheckout.disabled = true;
        btnCheckout.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...';

        // Persiapkan payload cart
        const cartPayload = cart.map(item => ({
            produk_jasa_id: item.id,
            jumlah: item.qty
        }));

        fetch('{{ route("operator.transaksi.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                cart: cartPayload
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Terjadi kesalahan sistem.');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Transaksi POS berhasil disimpan!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Reset Keranjang
                    cart = [];
                    renderCart();
                    // Refresh Halaman (atau load data via Ajax untuk list)
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Transaksi gagal disimpan.');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Transaksi Gagal',
                text: error.message
            });
            btnCheckout.disabled = false;
            btnCheckout.innerHTML = '<i class="bx bx-check-circle me-1"></i> Simpan &amp; Bayar';
        });
    }
</script>
@endsection
