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
            'shu_penerima'   => 'nullable|array',
            'shu_penerima.*' => 'required_with:shu_persentase.*|string|max:255',
            'shu_persentase' => 'nullable|array',
            'shu_persentase.*' => 'required_with:shu_penerima.*|numeric|min:0|max:100',
        ]);

        $penerimaList = $request->input('shu_penerima', []);
        $persentaseList = $request->input('shu_persentase', []);

        $shuPembagian = [];
        foreach ($penerimaList as $index => $penerima) {
            if (!empty($penerima) && isset($persentaseList[$index])) {
                $shuPembagian[] = [
                    'penerima'   => $penerima,
                    'persentase' => (float)$persentaseList[$index],
                ];
            }
        }

        if (count($shuPembagian) > 0) {
            $total = array_sum(array_column($shuPembagian, 'persentase'));
            if (abs($total - 100) > 0.0001) {
                return back()->withInput()->with('error', 'Total persentase pembagian SHU harus tepat 100% (saat ini: ' . $total . '%)');
            }
        } else {
            return back()->withInput()->with('error', 'Harus ada minimal 1 penerima SHU');
        }

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
                'shu_pembagian' => $shuPembagian,
            ]);
        } else {
            Pengaturan::create([
                'nama_aplikasi' => $request->nama_aplikasi,
                'nama_sekolah'  => $request->nama_sekolah,
                'alamat'        => $request->alamat,
                'telepon'       => $request->telepon,
                'email'         => $request->email,
                'logo'          => $logoPath,
                'shu_pembagian' => $shuPembagian,
            ]);
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui');
    }
}
