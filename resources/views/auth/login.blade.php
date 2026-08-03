<!doctype html>
<html lang="id" class="dark-style layout-wide">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | SIMUP WISTEK</title>

  @if(!empty($logoAplikasi))
    <link rel="icon" type="image/png" href="{{ asset('storage/' . $logoAplikasi) }}">
  @else
    <link rel="icon" type="image/png" href="{{ asset('default-logo.png') }}">
  @endif

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Boxicons CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="{{ asset('vendor/css/core.css') }}" />
  <link rel="stylesheet" href="{{ asset('vendor/css/theme-default.css') }}" />

  <style>
    * {
      font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: radial-gradient(circle at 15% 15%, rgba(99, 102, 241, 0.25) 0%, transparent 45%),
                  radial-gradient(circle at 85% 85%, rgba(168, 85, 247, 0.2) 0%, transparent 45%),
                  linear-gradient(135deg, #0F172A 0%, #1E1B4B 50%, #0F172A 100%);
      background-attachment: fixed;
      margin: 0;
      padding: 1.5rem;
      color: #E2E8F0;
    }

    .login-container {
      width: 100%;
      max-width: 440px;
      margin: auto;
    }

    .glass-login-card {
      background: rgba(30, 41, 59, 0.75);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 24px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5),
                  0 0 40px rgba(99, 102, 241, 0.15);
      padding: 2.5rem 2rem;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .brand-logo-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      margin-bottom: 1.5rem;
    }

    .brand-icon-box {
      width: 50px;
      height: 50px;
      border-radius: 14px;
      background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #FFFFFF;
      font-size: 1.75rem;
      box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
    }

    .brand-gradient-text {
      font-size: 1.6rem;
      font-weight: 800;
      background: linear-gradient(135deg, #818CF8 0%, #C084FC 50%, #F472B6 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      letter-spacing: -0.5px;
    }

    .form-control-modern {
      background: rgba(15, 23, 42, 0.6) !important;
      border: 1px solid rgba(255, 255, 255, 0.15) !important;
      color: #F8FAFC !important;
      border-radius: 12px !important;
      padding: 0.75rem 1rem !important;
      font-size: 0.95rem;
      transition: all 0.25s ease;
    }

    .form-control-modern::placeholder {
      color: #64748B;
    }

    .form-control-modern:focus {
      background: rgba(15, 23, 42, 0.85) !important;
      border-color: #6366F1 !important;
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.25) !important;
    }

    .input-group-modern {
      position: relative;
    }

    .input-group-modern .input-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #94A3B8;
      font-size: 1.25rem;
      z-index: 10;
    }

    .input-group-modern .form-control-modern {
      padding-left: 44px !important;
    }

    .btn-login-gradient {
      background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
      border: none;
      color: #FFFFFF;
      font-weight: 700;
      font-size: 1rem;
      padding: 0.85rem 1.5rem;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(79, 70, 229, 0.35);
      transition: all 0.3s ease;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-login-gradient:hover {
      background: linear-gradient(135deg, #4338CA 0%, #6D28D9 100%);
      box-shadow: 0 12px 30px rgba(79, 70, 229, 0.5);
      transform: translateY(-2px);
      color: #FFFFFF;
    }

    .password-toggle-icon {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #94A3B8;
      font-size: 1.25rem;
      cursor: pointer;
      z-index: 10;
      transition: color 0.2s;
    }

    .password-toggle-icon:hover {
      color: #F8FAFC;
    }

    .alert-danger-custom {
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #FCA5A5;
      border-radius: 12px;
      padding: 0.75rem 1rem;
      font-size: 0.875rem;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="glass-login-card">
      
      <!-- Brand Logo -->
      <div class="brand-logo-wrapper">
        <div class="brand-icon-box">
          @if(!empty($logoAplikasi))
            <img src="{{ asset('storage/' . $logoAplikasi) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; padding: 4px;">
          @else
            <i class="bx bx-intersect"></i>
          @endif
        </div>
        <div class="d-flex flex-column">
          <span class="brand-gradient-text">{{ $namaAplikasi ?? 'SIMUP WISTEK' }}</span>
          <small class="text-white-50 fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Unit Produksi System</small>
        </div>
      </div>

      <!-- Welcome Header -->
      <div class="text-center mb-4">
        <h4 class="fw-bold text-white mb-1">Selamat Datang 👋</h4>
        <p class="text-white-50 small mb-0">Masuk ke sistem manajemen SIMUP Wistek</p>
      </div>

      {{-- Error Alert --}}
      @if ($errors->any())
        <div class="alert alert-danger-custom d-flex align-items-center gap-2 mb-4">
          <i class="bx bx-error-circle fs-5"></i>
          <div>{{ $errors->first() }}</div>
        </div>
      @endif

      <!-- Form Login -->
      <form id="formAuthentication" action="{{ url('/login') }}" method="POST">
        @csrf

        {{-- Field Email --}}
        <div class="mb-3">
          <label for="email" class="form-label text-white-50 small fw-semibold">Email Pengguna</label>
          <div class="input-group-modern">
            <i class="bx bx-envelope input-icon"></i>
            <input
              type="email"
              class="form-control form-control-modern"
              id="email"
              name="email"
              placeholder="nama@wistek.sch.id"
              value="{{ old('email') }}"
              autofocus
              required />
          </div>
        </div>

        {{-- Field Password --}}
        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label text-white-50 small fw-semibold" for="password">Password</label>
          </div>
          <div class="input-group-modern">
            <i class="bx bx-lock-alt input-icon"></i>
            <input
              type="password"
              id="password"
              class="form-control form-control-modern"
              name="password"
              placeholder="••••••••"
              required />
            <i class="bx bx-hide password-toggle-icon" id="toggle-icon" onclick="togglePassword()"></i>
          </div>
        </div>

        {{-- Remember Me --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="form-check">
            <input class="form-check-input bg-dark border-secondary" type="checkbox" id="remember-me" name="remember" />
            <label class="form-check-label text-white-50 small" for="remember-me">Ingat Sesi Saya</label>
          </div>
        </div>

        {{-- Submit Button --}}
        <button class="btn btn-login-gradient w-100 mb-3" type="submit">
          Masuk ke Sistem <i class="bx bx-right-arrow-alt fs-4"></i>
        </button>
      </form>

      <!-- Footer Info -->
      <div class="text-center mt-4 pt-2 border-top border-secondary border-opacity-25">
        <small class="text-white-50" style="font-size: 0.8rem;">
          &copy; {{ date('Y') }} SIMUP Wistek &bull; Unit Produksi Sekolah
        </small>
      </div>

    </div>
  </div>

  <!-- Core JS -->
  <script src="{{ asset('vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('vendor/js/bootstrap.js') }}"></script>

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
