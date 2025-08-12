<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\ProdukJasa;
use App\Models\StokBarang;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today();

        // Pendapatan hari ini
        $pendapatanHariIni = Transaksi::whereDate('tanggal', $hariIni)->sum('total');

        // Total transaksi hari ini
        $totalTransaksiHariIni = Transaksi::whereDate('tanggal', $hariIni)->count();

        // Total produk/jasa tersedia
        $totalProdukJasa = ProdukJasa::count();

        // Stok menipis (stok <= 5)
        $stokMenipis = StokBarang::where('stok', '<=', 5)->get();

        // Transaksi terbaru (5 terakhir)
        $transaksiTerbaru = Transaksi::latest()->take(5)->get();

        // Data grafik penjualan 6 bulan terakhir
        $bulanPenjualan = [];
        $dataPenjualan = [];

        for ($i = 5; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);
            $bulanPenjualan[] = $bulan->format('M Y');

            $totalBulan = Transaksi::whereMonth('tanggal', $bulan->month)
                ->whereYear('tanggal', $bulan->year)
                ->sum('total');

            $dataPenjualan[] = $totalBulan;
        }

        return view('operator.dashboard', compact(
            'pendapatanHariIni',
            'totalTransaksiHariIni',
            'totalProdukJasa',
            'stokMenipis',
            'transaksiTerbaru',
            'bulanPenjualan',
            'dataPenjualan'
        ));
    }
}
