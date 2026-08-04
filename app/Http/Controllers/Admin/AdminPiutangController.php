<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Piutang;
use App\Models\PengeluaranLain;

class AdminPiutangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $data = Piutang::when($search, function ($query, $search) {
                            return $query->where('kepada', 'like', "%{$search}%")
                                         ->orWhere('nama_barang', 'like', "%{$search}%");
                        })
                        ->latest('tanggal_peminjaman')
                        ->paginate(10)
                        ->appends(['search' => $search]);
        return view('admin.laporan.piutang.index', compact('data', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_peminjaman' => 'required|date',
            'nama_barang' => 'required|string',
            'jumlah_barang' => 'required|integer',
            'nominal' => 'required|numeric',
            'kepada' => 'required|string',
        ]);

        $validated['sisa_nominal'] = $validated['nominal'];

        Piutang::create($validated);

        return redirect()->route('laporan.piutang')->with('success', 'Data piutang berhasil disimpan.');
    }

    public function update(Request $request, Piutang $piutang)
    {
        $request->validate([
            'tanggal_peminjaman' => 'required|date',
            'nama_barang' => 'required|string',
            'jumlah_barang' => 'required|integer',
            'nominal' => 'required|numeric',
            'kepada' => 'required|string',
        ]);

        $piutang->update([
            'tanggal_peminjaman' => $request->tanggal_peminjaman,
            'nama_barang' => $request->nama_barang,
            'jumlah_barang' => $request->jumlah_barang,
            'nominal' => $request->nominal,
            'kepada' => $request->kepada,
        ]);

        return back()->with('success', 'Piutang berhasil diperbarui.');
    }

    public function destroy(Piutang $piutang)
    {
        $piutang->delete();
        return back()->with('success', 'Piutang berhasil dihapus.');
    }

}
