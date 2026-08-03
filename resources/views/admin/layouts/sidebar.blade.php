<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme shadow-sm">
  <div class="app-brand demo d-flex align-items-center gap-2">
    <a href="{{ url('/admin/dashboard') }}" class="app-brand-link d-flex align-items-center gap-3 text-decoration-none">
      <div class="brand-logo-badge">
        @if(!empty($logoAplikasi))
          <img src="{{ asset('storage/' . $logoAplikasi) }}" alt="Logo">
        @else
          <i class="bx bx-intersect"></i>
        @endif
      </div>
      <div class="d-flex flex-column">
        <span class="brand-title">SIMUP <span class="text-primary">WISTEK</span></span>
        <small class="text-muted fw-semibold" style="font-size: 0.7rem;">Unit Produksi System</small>
      </div>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-2">
    {{-- UTAMA --}}
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Utama</span>
    </li>

    {{-- Dashboard --}}
    <li class="menu-item {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
      <a href="{{ url('/admin/dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-grid-alt"></i>
        <div>Dashboard</div>
      </a>
    </li>

    {{-- Kelola User --}}
    <li class="menu-item {{ request()->is('admin/kelola-user*') ? 'active' : '' }}">
      <a href="{{ url('/admin/kelola-user') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user-check"></i>
        <div>Kelola User</div>
      </a>
    </li>

    {{-- Logbook UP --}}
    <li class="menu-item {{ request()->is('admin/logbook*') ? 'active' : '' }}">
      <a href="{{ route('admin.logbook.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-book-content"></i>
        <div>Logbook UP</div>
      </a>
    </li>

    {{-- MANAJEMEN DATA & TRANSAKSI --}}
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Operasional</span>
    </li>

    {{-- Manajemen Data --}}
    <li class="menu-item {{ request()->is('admin/produk-jasa*') || request()->is('admin/stok-barang*') ? 'open active' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-layer"></i>
        <div>Manajemen Data</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('admin/produk-jasa*') ? 'active' : '' }}">
          <a href="{{ url('/admin/produk-jasa') }}" class="menu-link">
            <div>Produk &amp; Jasa</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('admin/stok-barang*') ? 'active' : '' }}">
          <a href="{{ url('/admin/stok-barang') }}" class="menu-link">
            <div>Stok Barang</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- Transaksi --}}
    <li class="menu-item {{ request()->is('admin/transaksi*') || request()->is('admin/rekap-transaksi*') ? 'open active' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-shopping-bag"></i>
        <div>Transaksi</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('admin/transaksi*') ? 'active' : '' }}">
          <a href="{{ url('/admin/transaksi') }}" class="menu-link">
            <div>Transaksi Harian</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('admin/rekap-transaksi*') ? 'active' : '' }}">
          <a href="{{ url('/admin/rekap-transaksi') }}" class="menu-link">
            <div>Rekap Transaksi</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- Pengeluaran --}}
    <li class="menu-item {{ request()->is('admin/pengeluaran-lain*') ? 'active' : '' }}">
      <a href="{{ url('/admin/pengeluaran-lain') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-credit-card-front"></i>
        <div>Pengeluaran</div>
      </a>
    </li>

    {{-- KEUANGAN & LAPORAN --}}
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Laporan Keuangan</span>
    </li>

    {{-- Laporan --}}
    <li class="menu-item {{ request()->is('admin/laporan*') ? 'open active' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-pie-chart-alt-2"></i>
            <div>Laporan</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item {{ request()->is('admin/laporan/buku-besar*') ? 'active' : '' }}">
                <a href="{{ route('laporan.buku-besar') }}" class="menu-link">
                    <div>Buku Besar Keuangan</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('admin/laporan/shu*') ? 'active' : '' }}">
                <a href="{{ route('laporan.shu') }}" class="menu-link">
                    <div>Sisa Hasil Usaha (SHU)</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('admin/laporan/piutang*') ? 'active' : '' }}">
                <a href="{{ route('laporan.piutang') }}" class="menu-link">
                    <div>Piutang</div>
                </a>
            </li>
        </ul>
    </li>

    {{-- SISTEM --}}
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Pengaturan</span>
    </li>

    {{-- Pengaturan --}}
    <li class="menu-item {{ request()->is('admin/pengaturan*') ? 'active' : '' }}">
      <a href="{{ url('/admin/pengaturan') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-slider-alt"></i>
        <div>Pengaturan Sistem</div>
      </a>
    </li>
  </ul>
</aside>
