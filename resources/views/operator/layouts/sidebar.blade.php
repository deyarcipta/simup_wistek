<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <span class="app-brand-text demo menu-text fw-bolder">SIMUP WISTEK</span>
  </div>
  <ul class="menu-inner py-1">
    <li class="menu-item {{ request()->is('operator/dashboard*') ? 'active' : '' }}">
      <a href="{{ url('/operator/dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home"></i>
        <div>Dashboard</div>
      </a>
    </li>
    <li class="menu-item {{ request()->is('operator/produk-jasa*') ? 'active' : '' }}">
      <a href="{{ url('/operator/produk-jasa') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-data"></i>
        <div>Produk & Jasa</div>
      </a>
    </li>
    <li class="menu-item {{ request()->is('operator/transaksi*') ? 'active' : '' }}">
      <a href="{{ url('/operator/transaksi') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-cart"></i>
        <div>Transaksi</div>
      </a>
    </li>
  </ul>
</aside>
