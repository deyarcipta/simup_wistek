<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\PengeluaranLain;
use App\Models\PencairanSaldo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPencairanSaldoController extends Controller
{
    public function index()
    {
        $members = Member::orderBy('nama')->get();
        $riwayat = PencairanSaldo::with('member')->latest()->paginate(10);

        return view('admin.pencairan_saldo.index', compact('members', 'riwayat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'jumlah' => 'required|numeric|min:1',
        ]);

        $member = Member::findOrFail($request->member_id);

        if ($request->jumlah > $member->saldo) {
            return redirect()->back()->with('error', 'Jumlah pencairan melebihi saldo yang tersedia!');
        }

        // Kurangi saldo
        $member->saldo -= $request->jumlah;
        $member->save();

        $userId = Auth::id();

        // Catat pencairan saldo
        $pencairan = PencairanSaldo::create([
            'member_id' => $member->id,
            'jumlah'    => $request->jumlah,
            'user_id'   => $userId,
            'keterangan'=> $request->keterangan ?? null,
        ]);

        // Catat juga ke pengeluaran_lain
        PengeluaranLain::create([
            'tanggal'       => now(),
            'keterangan'    => 'Pencairan saldo ke ' . $member->nama,
            'total'         => $request->jumlah,
            'member_id'     => $member->id,
            'pencairan_id'  => $pencairan->id, // ini sudah benar karena $pencairan sudah ada
        ]);

        return redirect()->back()->with('success', 'Saldo berhasil dicairkan.');
    }

    public function destroy($id)
    {
        $pencairan = PencairanSaldo::findOrFail($id);
        // dd($pencairan->id);

        // Kembalikan saldo ke member
        $member = $pencairan->member;
        $member->saldo += $pencairan->jumlah;
        $member->save();

        // Hapus juga data terkait di pengeluaran_lain
        PengeluaranLain::where('pencairan_id', $pencairan->id)->delete();

        // Hapus pencairan
        $pencairan->delete();

        return redirect()->back()->with('success', 'Data pencairan dihapus & saldo dikembalikan.');
    }
}
