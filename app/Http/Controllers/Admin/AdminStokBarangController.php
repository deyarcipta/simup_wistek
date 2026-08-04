<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\StokBarang;
use App\Models\ProdukJasa;
use Illuminate\Http\Request;

class AdminStokBarangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $stokBarang = StokBarang::when($search, function ($query, $search) {
                            return $query->where('nama_barang', 'like', "%{$search}%")
                                         ->orWhere('kode_barang', 'like', "%{$search}%")
                                         ->orWhere('satuan', 'like', "%{$search}%");
                        })
                        ->latest()
                        ->paginate(10)
                        ->appends(['search' => $search]);
        return view('admin.stok_barang.index', compact('stokBarang', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
        ]);

        StokBarang::create($request->all());
        return redirect()->back()->with('success', 'Stok barang berhasil ditambahkan.');
    }

    public function getData($id)
    {
        $data = StokBarang::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
        ]);

        $stokBarang = StokBarang::findOrFail($id);
        $stokBarang->update($request->all());
        return redirect()->back()->with('success', 'Stok barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $stokBarang = StokBarang::findOrFail($id);
        produkJasa::where('stok_barang_id', $stokBarang->id)->delete(); // Hapus relasi produk jasa
        $stokBarang->delete();
        return redirect()->back()->with('success', 'Stok barang berhasil dihapus.');
    }
}
