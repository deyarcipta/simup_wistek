<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme shadow-sm">
  <div class="app-brand demo d-flex align-items-center gap-2">
    <a href="{{ url('/operator/dashboard') }}" class="app-brand-link d-flex align-items-center gap-3 text-decoration-none">
      <div class="brand-logo-badge">
        @if(!empty($logoAplikasi))
          <img src="{{ asset('storage/' . $logoAplikasi) }}" alt="Logo">
        @else
          <i class="bx bx-intersect"></i>
        @endif
      </div>
      <div class="d-flex flex-column">
        <span class="brand-title">SIMUP <span class="text-primary">WISTEK</span></span>
        <small class="text-muted fw-semibold" style="font-size: 0.7rem;">Operator Portal</small>
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
    <li class="menu-item {{ request()->is('operator/dashboard*') ? 'active' : '' }}">
      <a href="{{ url('/operator/dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-grid-alt"></i>
        <div>Dashboard</div>
      </a>
    </li>

    {{-- OPERASIONAL HASIL POS --}}
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Operasional UP</span>
    </li>

    {{-- Produk & Jasa --}}
    <li class="menu-item {{ request()->is('operator/produk-jasa*') ? 'active' : '' }}">
      <a href="{{ url('/operator/produk-jasa') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-layer"></i>
        <div>Produk &amp; Jasa</div>
      </a>
    </li>

    {{-- Transaksi --}}
    <li class="menu-item {{ request()->is('operator/transaksi*') ? 'active' : '' }}">
      <a href="{{ url('/operator/transaksi') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-shopping-bag"></i>
        <div>Kasir Transaksi</div>
      </a>
    </li>

    {{-- Logbook Hari Ini --}}
    <li class="menu-item {{ request()->is('operator/logbook*') ? 'active' : '' }}">
      <a href="{{ route('operator.logbook.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-book-content"></i>
        <div>Logbook Shift Hari Ini</div>
      </a>
    </li>
  </ul>
</aside>
