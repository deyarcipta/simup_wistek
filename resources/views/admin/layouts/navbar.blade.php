<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme">
     <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>
  <ul class="navbar-nav flex-row align-items-center ms-auto">
    <li class="nav-item dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow" href="#" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
            @if(Auth::user() && Auth::user()->foto)
                <img src="{{ asset('storage/photos/' . Auth::user()->foto) }}" 
                    alt="Foto Profil"
                    style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
            @else
                <img src="{{ asset('img/avatars/1.png') }}" 
                    alt="Default Foto"
                    style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
            @endif
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" href="#">
            <div class="d-flex">
              <div class="flex-grow-1">
                <span class="fw-semibold d-block">{{ session('name') }}</span>
                <small class="text-muted">{{ session('role') }}</small>
              </div>
            </div>
          </a>
        </li>
        <li><div class="dropdown-divider"></div></li>
        <li>
          <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
            <i class="bx bx-user me-2"></i> Profile
          </a>
        </li>

        <li><div class="dropdown-divider"></div></li>
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="dropdown-item" type="submit">
              <i class="bx bx-power-off me-2"></i> Logout
            </button>
          </form>
        </li>
      </ul>
    </li>
  </ul>
</nav>
