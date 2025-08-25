<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <span class="app-brand-text demo menu-text fw-bolder">SIMUP WISTEK</span>
  </div>
  <ul class="menu-inner py-1">
    <li class="menu-item {{ request()->is('member/dashboard*') ? 'active' : '' }}">
      <a href="{{ url('/member/dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home"></i>
        <div>Dashboard</div>
      </a>
    </li>
  </ul>
</aside>
