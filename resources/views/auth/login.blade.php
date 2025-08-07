<!doctype html>
<html lang="en" class="light-style layout-wide" data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | SIMUP WISTEK</title>

  @if(!empty($logoAplikasi))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $logoAplikasi) }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('default-logo.png') }}">
    @endif

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('vendor/css/core.css') }}" />
  <link rel="stylesheet" href="{{ asset('vendor/css/theme-default.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/demo.css') }}" />
  <link rel="stylesheet" href="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
  <link rel="stylesheet" href="{{ asset('vendor/css/pages/page-auth.css') }}" />

  <!-- Iconify -->
  <link rel="stylesheet" href="{{ asset('vendor/fonts/iconify-icons.css') }}" />

  <!-- JS -->
  <script src="{{ asset('vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('js/config.js') }}"></script>

  <style>
    body {
      background: linear-gradient(135deg, #d6e0f0, #fdfcfb);
    }

    .card {
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .app-brand-text {
      font-size: 1.5rem;
      font-weight: 700;
      color: #5d5fef;
    }

    .form-label {
      font-weight: 600;
    }
  </style>
</head>

<body>
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Login Card -->
        <div class="card p-4">
          <div class="card-body">
            <div class="app-brand justify-content-center mb-4">
              <span class="app-brand-text">SIMUP WISTEK</span>
            </div>

            <h4 class="mb-2 text-center">Welcome Back! 👋</h4>
            <p class="mb-4 text-center text-muted">Silakan masuk untuk memulai sesi Anda.</p>

            {{-- Error Message --}}
            @if ($errors->any())
              <div class="alert alert-danger text-sm">
                {{ $errors->first() }}
              </div>
            @endif

            <form id="formAuthentication" action="{{ url('/login') }}" method="POST">
              @csrf
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  name="email"
                  placeholder="you@example.com"
                  value="{{ old('email') }}"
                  autofocus
                  required />
              </div>

              <div class="mb-3 form-password-toggle">
                <label class="form-label" for="password">Password</label>
                <div class="input-group input-group-merge">
                  <input
                    type="password"
                    id="password"
                    class="form-control"
                    name="password"
                    placeholder="••••••••"
                    required />
                  <span class="input-group-text cursor-pointer" onclick="togglePassword()">
                    <i class="bx bx-hide" id="toggle-icon"></i>
                  </span>
                </div>
              </div>

              <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="remember-me" />
                  <label class="form-check-label" for="remember-me">Ingat Saya</label>
                </div>
              </div>

              <div class="mb-3">
                <button class="btn btn-primary d-grid w-100" type="submit">Masuk</button>
              </div>
            </form>

            <p class="text-center text-muted mt-3">
              &copy; {{ date('Y') }} SIMUP WISTEK
            </p>
          </div>
        </div>
        <!-- /Login Card -->
      </div>
    </div>
  </div>

  <!-- Core JS -->
  <script src="{{ asset('vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('vendor/js/menu.js') }}"></script>
  <script src="{{ asset('js/main.js') }}"></script>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.getElementById('toggle-icon');
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bx-hide');
        toggleIcon.classList.add('bx-show');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bx-show');
        toggleIcon.classList.add('bx-hide');
      }
    }
  </script>
</body>
</html>
