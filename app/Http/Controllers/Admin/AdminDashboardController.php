<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\GajiKaryawan;
use App\Models\PengeluaranLain;
use App\Models\StokBarang;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $bulanSekarang = Carbon::now()->month;
        $tahunSekarang = Carbon::now()->year;

        $bulanLalu = Carbon::now()->subMonth()->month;
        $tahunBulanLalu = Carbon::now()->subMonth()->year;

        // =========================
        // Pendapatan
        // =========================
        $pendapatanBulanIni = Transaksi::whereMonth('tanggal', $bulanSekarang)
            ->whereYear('tanggal', $tahunSekarang)
            ->sum('total');

        $pendapatanBulanLalu = Transaksi::whereMonth('tanggal', $bulanLalu)
            ->whereYear('tanggal', $tahunBulanLalu)
            ->sum('total');

        if ($pendapatanBulanLalu > 0) {
            $persentasePendapatan = (($pendapatanBulanIni - $pendapatanBulanLalu) / $pendapatanBulanLalu) * 100;
        } else {
            $persentasePendapatan = 0;
        }

        // =========================
        // Pengeluaran
        // =========================
        $pengeluaranBulanIniGajiKaryawan = GajiKaryawan::whereMonth('tanggal', $bulanSekarang)
            ->whereYear('tanggal', $tahunSekarang)
            ->sum('total_gaji');

        $pengeluaranBulanLaluGajiKaryawan = GajiKaryawan::whereMonth('tanggal', $bulanLalu)
            ->whereYear('tanggal', $tahunBulanLalu)
            ->sum('total_gaji');

        $pengeluaranBulanIniPengeluaranLain = PengeluaranLain::whereMonth('tanggal', $bulanSekarang)
            ->whereYear('tanggal', $tahunSekarang)
            ->sum('total');

        $pengeluaranBulanLaluPengeluaranLain = PengeluaranLain::whereMonth('tanggal', $bulanLalu)
            ->whereYear('tanggal', $tahunBulanLalu)
            ->sum('total');

        $pengeluaranBulanIni = $pengeluaranBulanIniPengeluaranLain + $pengeluaranBulanIniGajiKaryawan;

        $pengeluaranBulanLalu = $pengeluaranBulanLaluPengeluaranLain + $pengeluaranBulanLaluGajiKaryawan;

        if ($pengeluaranBulanLalu > 0) {
            $persentasePengeluaran = (($pengeluaranBulanIni - $pengeluaranBulanLalu) / $pengeluaranBulanLalu) * 100;
        } else {
            $persentasePengeluaran = 0;
        }

        // =========================
        // Laba / Rugi
        // =========================
        $labaRugi = $pendapatanBulanIni - $pengeluaranBulanIni;

        // =========================
        // Total transaksi bulan ini
        // =========================
        $totalTransaksi = Transaksi::whereMonth('tanggal', $bulanSekarang)
            ->whereYear('tanggal', $tahunSekarang)
            ->count();

        // =========================
        // Stok menipis
        // =========================
        $stokMenipis = StokBarang::where('stok', '<=', 5)->get();

        // =========================
        // Transaksi terbaru
        // =========================
        $transaksiTerbaru = Transaksi::latest()->take(5)->get();

        // =========================
        // Grafik penjualan
        // =========================
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

        return view('admin.dashboard', compact(
            'pendapatanBulanIni',
            'pendapatanBulanLalu',
            'persentasePendapatan',
            'pengeluaranBulanIni',
            'pengeluaranBulanLalu',
            'persentasePengeluaran',
            'labaRugi',
            'totalTransaksi',
            'stokMenipis',
            'transaksiTerbaru',
            'bulanPenjualan',
            'dataPenjualan'
        ));
    }
}
