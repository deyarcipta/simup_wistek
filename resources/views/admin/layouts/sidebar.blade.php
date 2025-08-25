<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <span class="app-brand-text demo menu-text fw-bolder">SIMUP WISTEK</span>
  </div>

  <ul class="menu-inner py-1">
    {{-- Dashboard --}}
    <li class="menu-item {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
      <a href="{{ url('/admin/dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home"></i>
        <div>Dashboard</div>
      </a>
    </li>

    {{-- Kelola User --}}
    <li class="menu-item {{ request()->is('admin/kelola-user*') ? 'active' : '' }}">
      <a href="{{ url('/admin/kelola-user') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user"></i>
        <div>Kelola User</div>
      </a>
    </li>

    {{-- Manajemen Data --}}
    <li class="menu-item {{ request()->is('admin/produk-jasa*') || request()->is('admin/stok-barang*') ? 'open active' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-data"></i>
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
        <i class="menu-icon tf-icons bx bx-cart"></i>
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
    <li class="menu-item {{ request()->is('admin/gaji-karyawan*') || request()->is('admin/pengeluaran-lain*') ? 'open active' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-money-withdraw"></i>
        <div>Pengeluaran</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('admin/gaji-karyawan*') ? 'active' : '' }}">
          <a href="{{ url('/admin/gaji-karyawan') }}" class="menu-link">
            <div>Gaji Karyawan</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('admin/pengeluaran-lain*') ? 'active' : '' }}">
          <a href="{{ url('/admin/pengeluaran-lain') }}" class="menu-link">
            <div>Pengeluaran Lain</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- Laporan --}}
    <li class="menu-item {{ request()->is('admin/laporan*') ? 'open active' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-bar-chart"></i>
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

    {{-- Kelola Member --}}
    <li class="menu-item {{ request()->is('admin/data-member*') || request()->is('admin/pencairan*') ? 'open active' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-id-card"></i>
            <div>Kelola Member</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('admin/data-member*') ? 'active' : '' }}">
            <a href="{{ url('/admin/data-member') }}" class="menu-link">
              <div>Kelola Member</div>
            </a>
          </li>
          <li class="menu-item {{ request()->is('admin/pencairan*') ? 'active' : '' }}">
            <a href="{{ url('/admin/pencairan') }}" class="menu-link">
              <div>Pencairan Saldo</div>
            </a>
          </li>
        </ul>
    </li>

    {{-- Pengaturan --}}
    <li class="menu-item {{ request()->is('admin/pengaturan*') ? 'active' : '' }}">
      <a href="{{ url('/admin/pengaturan') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-cog"></i>
        <div>Pengaturan</div>
      </a>
    </li>
  </ul>
</aside>
