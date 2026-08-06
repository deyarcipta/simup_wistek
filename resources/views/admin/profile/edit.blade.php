@extends('admin.layouts.app')

@section('title', 'Profile Admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white shadow">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h5 class="text-white-50 mb-1"><i class="bx bx-user-pin me-1"></i> Akun Pengguna</h5>
                    <h2 class="text-white mb-0 fw-bold">Edit Profil Administrator</h2>
                    <p class="mb-0 mt-1 text-white-50" style="font-size: 0.85rem; max-width: 600px;">
                        Perbarui informasi profil Anda seperti nama, alamat email, foto profil, serta ubah kata sandi / password akun.
                    </p>
                </div>
                <div class="d-none d-md-block text-white" style="font-size: 5rem; opacity: 0.25; line-height: 1;">
                    <i class="bx bx-user-pin"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container px-0">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
        </div>

        <div class="mb-3">
            <label>Password Baru</label>
            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin merubah password">
        </div>

        <div class="mb-3">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
        </div>

        <div class="mb-3">
            <label>Foto Profil</label><br>
            @if($user->foto)
                <img src="{{ asset('storage/photos/' . $user->foto) }}" alt="Foto Profil" class="rounded-circle mb-2" width="80" height="80">
            @else
                <img src="{{ asset('img/avatars/1.png') }}" alt="Foto Default" class="rounded-circle mb-2" width="80">
            @endif
            <input type="file" name="foto" class="form-control mt-2">
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
@endsection
