<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\GajiKaryawan;
use Illuminate\Http\Request;

class AdminGajiKaryawanController extends Controller
{
    public function index()
    {
        $gaji = GajiKaryawan::latest()->get();
        return view('admin.pengeluaran.gaji.index', compact('gaji'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'total_gaji' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
        ]);

        GajiKaryawan::create($request->all());
        return back()->with('success', 'Gaji karyawan berhasil ditambahkan');
    }

    public function getData($id)
    {
        $data = GajiKaryawan::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'total_gaji'    => 'required|numeric|min:0',
            'tanggal'       => 'required|date',
        ]);

        $gaji = GajiKaryawan::findOrFail($id);

        $gaji->update([
            'nama_karyawan' => $request->nama_karyawan,
            'total_gaji'    => $request->total_gaji,
            'tanggal'       => $request->tanggal,
        ]);

        return redirect()->route('gaji-karyawan.index')->with('success', 'Data gaji berhasil diperbarui.');
    }

    public function destroy($id)
    {
        GajiKaryawan::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}
