<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdukJasa;
use App\Models\StokBarang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
    
        $produkJasa = ProdukJasa::all();
    
        $transaksi = Transaksi::with(['details.produkJasa', 'user'])
            ->when($search, function ($query) use ($search) {
                $query->where('kode_transaksi', 'like', "%{$search}%")
                      ->orWhere('nama_pembeli', 'like', "%{$search}%");
            })
            ->latest() 
            ->paginate(20)
            ->appends(['search' => $search]);
    
        return view('operator.transaksi.index', compact('produkJasa', 'transaksi', 'search'));
    }

    public function store(Request $request)
    {   
        $request->validate([
            'produk_jasa_id' => 'required|exists:produk_jasa,id',
            'jumlah'         => 'required|integer|min:1',
            'nama_pembeli'   => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $produk = ProdukJasa::findOrFail($request->produk_jasa_id);

            // Tentukan prefix kode transaksi
            $prefix = $produk->jenis === 'produk' ? 'PRX' : 'JRX';
            $kodeTransaksi = $prefix . '-' . strtoupper(Str::random(6));

            // Hitung total harga
            $total = $produk->harga * $request->jumlah;
            $userId = Auth::id(); // Ambil ID user yang sedang login.
            
            // Simpan transaksi (tambahkan user_id)
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'tanggal'        => now(),
                'nama_pembeli'   => $request->nama_pembeli,
                'total'          => $total,
                'user_id'        => $userId, // user yang login
            ]);

            // Simpan detail transaksi
            TransaksiDetail::create([
                'transaksi_id'    => $transaksi->id,
                'produk_jasa_id'  => $produk->id,
                'jumlah'          => $request->jumlah,
                'harga'           => $produk->harga,
                'subtotal'        => $total
            ]);

            // Kurangi stok jika jenis produk
            if ($produk->jenis === 'produk' && $produk->stok_barang_id) {
                $stokBarang = StokBarang::find($produk->stok_barang_id);
                if ($stokBarang) {
                    $stokBarang->stok = max(0, $stokBarang->stok - $request->jumlah);
                    $stokBarang->save();
                }
            }
        });

        return redirect()->back()->with('success', 'Transaksi berhasil disimpan.');
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::with('details')->findOrFail($id);

        foreach ($transaksi->details as $detail) {
            // Ambil produk_jasa terkait
            $produk = ProdukJasa::find($detail->produk_jasa_id);

            if ($produk && $produk->jenis === 'produk') {
                // Jika produk terhubung ke stok_barang, kembalikan stoknya juga
                if ($produk->stok_barang_id) {
                    $stokBarang = StokBarang::find($produk->stok_barang_id);
                    if ($stokBarang) {
                        $stokBarang->stok += $detail->jumlah;
                        $stokBarang->save();
                    }
                }
            }
        }

        // Hapus transaksi (Laravel akan otomatis hapus details jika relasi onDelete cascade)
        $transaksi->delete();

        return redirect()->back()->with('success', 'Transaksi berhasil dihapus dan stok dikembalikan.');
    }
}
