<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\PengeluaranLain;
use Illuminate\Http\Request;
use App\Models\Piutang;

class AdminPengeluaranLainController extends Controller
{
    public function index()
    {
        $pengeluaran = PengeluaranLain::all();
        $piutangList = Piutang::where('sisa_nominal', '>', 0)->get();

        return view('admin.pengeluaran.lain.index', compact('pengeluaran', 'piutangList'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'keterangan' => 'required|string',
            'total' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'piutang_id' => 'nullable|exists:piutang,id',
        ]);

        $pengeluaran = PengeluaranLain::create($request->only('keterangan', 'total', 'tanggal', 'piutang_id'));

        // Kurangi sisa_nominal jika pengeluaran untuk piutang
        if ($request->piutang_id) {
            $piutang = Piutang::find($request->piutang_id);
            $piutang->sisa_nominal -= $request->total;
            $piutang->save();
        }

        return redirect()->back()->with('success', 'Pengeluaran berhasil ditambahkan');
    }

    public function getData($id)
    {
        $data = PengeluaranLain::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string',
            'total' => 'required|numeric',
            'tanggal' => 'required|date',
        ]);

        $pengeluaran = PengeluaranLain::findOrFail($id);
        $pengeluaran->keterangan = $request->keterangan;
        $pengeluaran->total = $request->total;
        $pengeluaran->tanggal = $request->tanggal;
        $pengeluaran->save();

        return redirect()->route('pengeluaran-lain.index')->with('success', 'Pengeluaran berhasil diupdate.');
    }

    public function destroy($id)
    {
        $pengeluaran = PengeluaranLain::findOrFail($id);

        // Hapus juga pencairan jika ada
        if ($pengeluaran->pencairan) {
            // kembalikan saldo ke member
            $member = $pencairan->member;
            $member->saldo += $pencairan->jumlah;
            $member->save();

            // Hapus pencairan
            $pengeluaran->pencairan->delete();
        }

        // dd($pengeluaran->piutang);
        // Jika ada piutang
        if ($pengeluaran->piutang) {
            $piutang = $pengeluaran->piutang;

            // kembalikan sisa saldo piutang
            $piutang->sisa_nominal += $pengeluaran->total;
            $piutang->save();
        }

        $pengeluaran->delete();

        return back()->with('success', 'Data pengeluaran & pencairan terkait berhasil dihapus');
    }
}
