@extends('admin.layouts.app')

@section('title', 'Profile Admin')

@section('content')
<div class="container mt-4">
    <h4>Edit Profile</h4>
    <hr>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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
