<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4 text-dark" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav align-items-center d-none d-md-flex">
    <div class="nav-item d-flex align-items-center gap-2">
      <span class="badge bg-label-success px-3 py-2 rounded-pill fw-semibold">
        <i class="bx bx-run me-1"></i> Operator Standby
      </span>
      <span class="text-muted text-sm fw-medium ms-2">
        <i class="bx bx-calendar me-1"></i> {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}
      </span>
    </div>
  </div>

  <ul class="navbar-nav flex-row align-items-center ms-auto gap-3">
    {{-- User Profile --}}
    <li class="nav-item dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
        <div class="d-none d-md-flex flex-column text-end me-1">
          <span class="fw-bold text-dark" style="font-size: 0.9rem; line-height: 1.2;">{{ Auth::user()->name ?? session('name', 'Operator') }}</span>
          <small class="text-muted text-capitalize" style="font-size: 0.75rem;">{{ Auth::user()->role ?? session('role', 'Operator') }}</small>
        </div>
        <div class="position-relative">
          @if(Auth::user() && Auth::user()->foto)
            <img src="{{ asset('storage/photos/' . Auth::user()->foto) }}" 
                 alt="Foto Profil" 
                 class="navbar-user-avatar">
          @else
            <img src="{{ asset('img/avatars/1.png') }}" 
                 alt="Default Foto" 
                 class="navbar-user-avatar">
          @endif
          <span class="avatar-online-dot"></span>
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-2">
        <li class="p-2 border-bottom mb-1">
          <div class="d-flex align-items-center gap-2">
            <div class="flex-grow-1">
              <span class="fw-bold d-block text-dark">{{ Auth::user()->name ?? session('name', 'Operator') }}</span>
              <small class="text-muted">{{ Auth::user()->email ?? 'operator@wistek.sch.id' }}</small>
            </div>
          </div>
        </li>
        <li>
          <a class="dropdown-item rounded-3 py-2" href="{{ route('operator.profile.edit') }}">
            <i class="bx bx-user me-2 text-primary"></i> Edit Profile
          </a>
        </li>
        <li>
          <a class="dropdown-item rounded-3 py-2" href="{{ route('operator.logbook.index') }}">
            <i class="bx bx-book-content me-2 text-info"></i> Logbook Shift
          </a>
        </li>
        <li><div class="dropdown-divider my-1"></div></li>
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="dropdown-item rounded-3 py-2 text-danger fw-semibold" type="submit">
              <i class="bx bx-power-off me-2"></i> Logout
            </button>
          </form>
        </li>
      </ul>
    </li>
  </ul>
</nav>
