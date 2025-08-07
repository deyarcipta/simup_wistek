<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::first();
        return view('admin.pengaturan.index', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_aplikasi' => 'nullable|string|max:255',
            'nama_sekolah'  => 'nullable|string|max:255',
            'alamat'        => 'nullable|string|max:255',
            'telepon'       => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'logo'          => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $pengaturan = Pengaturan::first();

        if ($request->hasFile('logo')) {
            if ($pengaturan && $pengaturan->logo) {
                Storage::delete($pengaturan->logo);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
        } else {
            $logoPath = $pengaturan->logo ?? null;
        }

        if ($pengaturan) {
            $pengaturan->update([
                'nama_aplikasi' => $request->nama_aplikasi,
                'nama_sekolah'  => $request->nama_sekolah,
                'alamat'        => $request->alamat,
                'telepon'       => $request->telepon,
                'email'         => $request->email,
                'logo'          => $logoPath,
            ]);
        } else {
            Pengaturan::create([
                'nama_aplikasi' => $request->nama_aplikasi,
                'nama_sekolah'  => $request->nama_sekolah,
                'alamat'        => $request->alamat,
                'telepon'       => $request->telepon,
                'email'         => $request->email,
                'logo'          => $logoPath,
            ]);
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui');
    }
}
