@extends('admin.layouts.app')

@section('title', 'Profile Admin')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="fw-bold mb-3">Edit Profile</h4>
        <hr class="mb-4">

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
</div>
@endsection
