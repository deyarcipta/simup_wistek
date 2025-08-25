<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\Transaksi;
use App\Models\PencairanSaldo;
use Carbon\Carbon;

class MemberDashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard untuk member
     */
    public function index()
    {
        $user = Auth::user(); // User yang login

        // Ambil data member sesuai user_id
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return redirect()->back()->with('error', 'Data member tidak ditemukan!');
        }

        $saldoTotal = $member->saldo ?? 0;

        // Hitung transaksi hari ini
        $jumlahTransaksi = Transaksi::where('member_id', $member->id)
            ->count();

        // Ambil 5 transaksi terbaru
        $transaksiTerbaru = Transaksi::where('member_id', $member->id)
            ->latest()
            ->take(5)
            ->get();
        
        $pencairanSaldoTerbaru = PencairanSaldo::where('member_id', $member->id)
            ->latest()
            ->take(5)
            ->get();

        return view('member.dashboard', compact(
            'saldoTotal',
            'jumlahTransaksi',
            'transaksiTerbaru',
            'pencairanSaldoTerbaru'
        ));
    }
}
