<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\ProdukJasa;
use App\Models\StokBarang;
use Illuminate\Http\Request;

class AdminProdukJasaController extends Controller
{
    public function index()
    {
        $produkJasa = ProdukJasa::with('stokBarang')->orderBy('created_at', 'desc')->get();
        return view('admin.produk_jasa.index', compact('produkJasa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:produk,jasa',
        ]);

        if ($request->jenis == 'produk') {
            $request->validate([
                'stok_barang_id' => 'required|exists:stok_barang,id',
            ]);

            $stokBarang = StokBarang::find($request->stok_barang_id);

            ProdukJasa::create([
                'nama'           => $stokBarang->nama_barang,
                'jenis'          => 'produk',
                'harga'          => $stokBarang->harga_jual,
                'satuan'         => $stokBarang->satuan,
                'stok_barang_id' => $stokBarang->id,
            ]);

        } else {
            $request->validate([
                'nama_jasa'   => 'required|string|max:255',
                'harga_jasa'  => 'required|numeric|min:0',
                'jumlah_jasa' => 'nullable|integer|min:1',
                'satuan_jasa' => 'nullable|string|max:255'
            ]);

            ProdukJasa::create([
                'nama'   => $request->nama_jasa,
                'jenis'  => 'jasa',
                'harga'  => $request->harga_jasa,
                'jumlah'   => $request->jumlah_jasa ?? null,
                'satuan' => $request->satuan_jasa ?? null,
                'stok_barang_id' => null,
            ]);
        }

        return redirect()->route('admin.produk-jasa.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $produkJasa = ProdukJasa::findOrFail($id);

        $request->validate([
            'jenis' => 'required|in:produk,jasa',
        ]);

        if ($request->jenis == 'produk') {
            if ($request->filled('stok_barang_id')) {
                $stokBarang = StokBarang::find($request->stok_barang_id);
        
                $produkJasa->update([
                    'nama'           => $stokBarang->nama_barang,
                    'jenis'          => 'produk',
                    'harga'          => $stokBarang->harga_jual,
                    'stok'           => null,
                    'satuan'         => $stokBarang->satuan,
                    'stok_barang_id' => $stokBarang->id,
                ]);
            } else {
                $produkJasa->update([
                    'nama'   => $request->nama,
                    'jenis'  => 'produk',
                    'harga'  => $request->harga,
                    'stok'   => null,
                    'satuan' => $request->satuan,
                ]);
            }
        }

        return redirect()->route('admin.produk-jasa.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $produkJasa = ProdukJasa::findOrFail($id);
        $produkJasa->delete();

        return redirect()->route('admin.produk-jasa.index')->with('success', 'Data berhasil dihapus.');
    }

    public function getData($id)
    {
        $produkJasa = ProdukJasa::with('stokBarang')->findOrFail($id);
        return response()->json($produkJasa);
    }
}
