@extends('admin.layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Pengaturan Umum</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Nama Aplikasi</label>
                <input type="text" name="nama_aplikasi" class="form-control" value="{{ old('nama_aplikasi', $pengaturan->nama_aplikasi ?? '') }}">
            </div>

            <div class="mb-3">
                <label>Nama Sekolah</label>
                <input type="text" name="nama_sekolah" class="form-control" value="{{ old('nama_sekolah', $pengaturan->nama_sekolah ?? '') }}">
            </div>

            <div class="mb-3">
                <label>Alamat</label>
                <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $pengaturan->alamat ?? '') }}">
            </div>

            <div class="mb-3">
                <label>Telepon</label>
                <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $pengaturan->telepon ?? '') }}">
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $pengaturan->email ?? '') }}">
            </div>

            <div class="mb-3">
                <label>Logo</label>
                <input type="file" name="logo" class="form-control">
                @if(!empty($pengaturan->logo))
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$pengaturan->logo) }}" alt="Logo" style="max-height: 80px;">
                    </div>
                @endif
            </div>

            <button class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection
