<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\GajiKaryawan;
use App\Models\PengeluaranLain;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminLaporanController extends Controller
{
    private function getBukuBesarData($startDate, $endDate)
    {
        // Ambil pemasukan dari transaksi
        $pemasukan = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->select('tanggal', 'kode_transaksi', 'total')
            ->get()
            ->map(fn($item) => [
                'tanggal'    => Carbon::parse($item->tanggal)->format('Y-m-d'),
                'keterangan' => 'Pemasukan - ' . $item->kode_transaksi,
                'debit'      => $item->total,
                'kredit'     => 0
            ]);

        // Ambil pengeluaran gaji
        $gaji = GajiKaryawan::whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->map(fn($item) => [
                'tanggal'    => Carbon::parse($item->tanggal)->format('Y-m-d'),
                'keterangan' => 'Gaji Karyawan - ' . $item->nama_karyawan,
                'debit'      => 0,
                'kredit'     => $item->total_gaji
            ]);

        // Ambil pengeluaran lain
        $pengeluaran = PengeluaranLain::whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->map(fn($item) => [
                'tanggal'    => Carbon::parse($item->tanggal)->format('Y-m-d'),
                'keterangan' => 'Pengeluaran - ' . $item->keterangan,
                'debit'      => 0,
                'kredit'     => $item->total
            ]);

        // Gabungkan & urutkan
        $bukuBesar = $pemasukan->concat($gaji)->concat($pengeluaran)->sortBy('tanggal')->values();

        // Hitung saldo berjalan
        $saldo = 0;
        return $bukuBesar->map(function ($row) use (&$saldo) {
            $saldo += ($row['debit'] - $row['kredit']);
            $row['saldo'] = $saldo;
            return $row;
        });
    }

    public function bukuBesar(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate   = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        $bukuBesar = $this->getBukuBesarData($startDate, $endDate);

        return view('admin.laporan.buku_besar', compact('bukuBesar', 'startDate', 'endDate'));
    }

    public function downloadBukuBesar(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate   = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        $bukuBesar = $this->getBukuBesarData($startDate, $endDate);

        $pdf = Pdf::loadView('admin.laporan.buku_besar_pdf', compact('bukuBesar', 'startDate', 'endDate'))
            ->setPaper('A4', 'portrait');

        return $pdf->download("Buku-Besar-{$startDate}-sd-{$endDate}.pdf");
    }

    public function sisaHasilUsaha(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate   = $request->end_date ?? now()->endOfMonth()->toDateString();

        $totalPemasukan = Transaksi::whereBetween('tanggal', [$startDate, $endDate])->sum('total');
        $totalGaji = GajiKaryawan::whereBetween('tanggal', [$startDate, $endDate])->sum('total_gaji');
        $totalPengeluaranLain = PengeluaranLain::whereBetween('tanggal', [$startDate, $endDate])->sum('total');

        $totalPengeluaran = $totalGaji + $totalPengeluaranLain;
        $shu = $totalPemasukan - $totalPengeluaran;

        $pembagian = [
            ['penerima' => 'Jurusan TKJ',   'persentase' => 40, 'nominal' => $shu * 0.40],
            ['penerima' => 'Unit Produksi', 'persentase' => 30, 'nominal' => $shu * 0.30],
            ['penerima' => 'Sekolah',       'persentase' => 20, 'nominal' => $shu * 0.20],
            ['penerima' => 'Honor Pegawai', 'persentase' => 10, 'nominal' => $shu * 0.10],
        ];

        return view('admin.laporan.sisa_hasil_usaha', compact(
            'startDate', 'endDate', 'shu', 'pembagian', 'totalPemasukan', 'totalPengeluaran'
        ));
    }

    public function downloadSisaHasilUsaha(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate   = $request->end_date ?? now()->endOfMonth()->toDateString();

        $totalPemasukan = Transaksi::whereBetween('tanggal', [$startDate, $endDate])->sum('total');
        $totalGaji = GajiKaryawan::whereBetween('tanggal', [$startDate, $endDate])->sum('total_gaji');
        $totalPengeluaranLain = PengeluaranLain::whereBetween('tanggal', [$startDate, $endDate])->sum('total');

        $totalPengeluaran = $totalGaji + $totalPengeluaranLain;
        $shu = $totalPemasukan - $totalPengeluaran;

        $pembagian = [
            ['penerima' => 'Jurusan TKJ',   'persentase' => 40, 'nominal' => $shu * 0.40],
            ['penerima' => 'Unit Produksi', 'persentase' => 30, 'nominal' => $shu * 0.30],
            ['penerima' => 'Sekolah',       'persentase' => 20, 'nominal' => $shu * 0.20],
            ['penerima' => 'Honor Pegawai', 'persentase' => 10, 'nominal' => $shu * 0.10],
        ];

        $pdf = Pdf::loadView('admin.laporan.sisa_hasil_usaha_pdf', compact(
            'startDate', 'endDate', 'shu', 'pembagian', 'totalPemasukan', 'totalPengeluaran'
        ))->setPaper('A4', 'portrait');

        return $pdf->download("Laporan-SHU-{$startDate}-sd-{$endDate}.pdf");
    }
}
