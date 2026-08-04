<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logbook;
use App\Models\LogbookDetail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminLogbookController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        $logbooks = Logbook::with(['details.shift', 'details.user'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->appends(['bulan' => $bulan, 'tahun' => $tahun]);

        // Hitung total omzet bulan ini dari logbook_details
        $totalOmzet = LogbookDetail::whereHas('logbook', function ($query) use ($bulan, $tahun) {
            $query->whereMonth('tanggal', $bulan)
                  ->whereYear('tanggal', $tahun);
        })->sum('total_uang');

        // List tahun untuk filter (dari logbook tertua sampai tahun depan)
        $tahunTertua = Logbook::orderBy('tanggal', 'asc')->first()?->tanggal?->year ?? Carbon::now()->year;
        $listTahun = range($tahunTertua, Carbon::now()->year + 1);

        return view('admin.logbook.index', compact('logbooks', 'bulan', 'tahun', 'totalOmzet', 'listTahun'));
    }

    public function show($id)
    {
        $logbook = Logbook::with(['details.shift', 'details.user'])->findOrFail($id);
        
        $jumlahShiftSetting = \App\Models\Pengaturan::first()?->jumlah_shift ?? 2;
        
        $printId = \App\Models\ProdukJasa::where('nama', 'LIKE', '%PRINT HITAM%')->first()?->id ?? 2;
        $fotokopiId = \App\Models\ProdukJasa::where('nama', 'LIKE', '%FOTOCOPY%')->first()?->id ?? 9;
        $jilidId = \App\Models\ProdukJasa::where('nama', 'LIKE', '%JILID%')->first()?->id ?? 12;

        $tarifPrint = \App\Models\ProdukJasa::find($printId)?->harga ?? 500;
        $tarifFotokopi = \App\Models\ProdukJasa::find($fotokopiId)?->harga ?? 500;
        $tarifJilid = \App\Models\ProdukJasa::find($jilidId)?->harga ?? 5000;

        $s1 = $logbook->details->where('shift_id', 1)->first();
        $s2 = $logbook->details->where('shift_id', 2)->first();
        $s3 = $logbook->details->where('shift_id', 3)->first();

        // Time ranges
        $s1Start = $logbook->created_at;
        $s1End = $s1 ? $s1->created_at : Carbon::now();

        $s2Start = $s1 ? $s1->created_at : null;
        $s2End = $s2 ? $s2->created_at : Carbon::now();

        $s3Start = $s2 ? $s2->created_at : null;
        $s3End = $s3 ? $s3->created_at : Carbon::now();

        $getShiftData = function ($start, $end) use ($printId, $fotokopiId, $jilidId, $tarifPrint, $tarifFotokopi, $tarifJilid) {
            $transaksi = \App\Models\Transaksi::with(['details.produkJasa', 'user'])
                ->whereBetween('created_at', [$start, $end])
                ->get();

            $summary = [
                'jumlah_print' => 0,
                'harga_print' => $tarifPrint,
                'jumlah_fotokopi' => 0,
                'harga_fotokopi' => $tarifFotokopi,
                'jumlah_jilid' => 0,
                'harga_jilid' => $tarifJilid,
                'pendapatan_lain' => 0,
                'total_uang' => 0,
            ];

            foreach ($transaksi as $tx) {
                foreach ($tx->details as $detail) {
                    if ($detail->produk_jasa_id == $printId) {
                        $summary['jumlah_print'] += $detail->jumlah;
                        $summary['harga_print'] = $detail->harga;
                    } elseif ($detail->produk_jasa_id == $fotokopiId) {
                        $summary['jumlah_fotokopi'] += $detail->jumlah;
                        $summary['harga_fotokopi'] = $detail->harga;
                    } elseif ($detail->produk_jasa_id == $jilidId) {
                        $summary['jumlah_jilid'] += $detail->jumlah;
                        $summary['harga_jilid'] = $detail->harga;
                    } else {
                        $summary['pendapatan_lain'] += $detail->subtotal;
                    }
                }
                $summary['total_uang'] += $tx->total;
            }

            return [
                'transaksi' => $transaksi,
                'summary' => $summary,
            ];
        };

        $shift1Real = $getShiftData($s1Start, $s1End);

        $shift2Real = null;
        if ($s1) {
            $shift2Real = $getShiftData($s2Start, $s2End);
        }

        $shift3Real = null;
        if ($s2) {
            $shift3Real = $getShiftData($s3Start, $s3End);
        }

        return view('admin.logbook.show', compact('logbook', 'shift1Real', 'shift2Real', 'shift3Real', 'jumlahShiftSetting'));
    }

    public function exportPdf(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        $logbooks = Logbook::with(['details.shift', 'details.user'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');

        $pdf = Pdf::loadView('admin.logbook.pdf', compact('logbooks', 'bulan', 'tahun', 'namaBulan'))
            ->setPaper('A4', 'landscape');

        return $pdf->download("Laporan-Logbook-UP-{$namaBulan}-{$tahun}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        $logbooks = Logbook::with(['details.shift', 'details.user'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');
        $fileName = "Laporan-Logbook-UP-{$namaBulan}-{$tahun}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $jumlahShiftSetting = \App\Models\Pengaturan::first()?->jumlah_shift ?? 2;

        $columns = [
            'Tanggal', 
            'Kas Awal', 
            'Kas Akhir', 
            'Shift 1 Pagi - Print', 
            'Shift 1 Pagi - Fotokopi', 
            'Shift 1 Pagi - Jilid', 
            'Shift 1 Pagi - Total',
        ];

        if ($jumlahShiftSetting >= 2) {
            $columns[] = 'Shift 2 Siang - Print';
            $columns[] = 'Shift 2 Siang - Fotokopi';
            $columns[] = 'Shift 2 Siang - Jilid';
            $columns[] = 'Shift 2 Siang - Total';
        }

        if ($jumlahShiftSetting == 3) {
            $columns[] = 'Shift 3 Sore - Print';
            $columns[] = 'Shift 3 Sore - Fotokopi';
            $columns[] = 'Shift 3 Sore - Jilid';
            $columns[] = 'Shift 3 Sore - Total';
        }

        $columns[] = 'Total Omzet Harian';
        $columns[] = 'Stok Kertas';
        $columns[] = 'Status Mesin';

        $callback = function() use($logbooks, $columns, $jumlahShiftSetting) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logbooks as $logbook) {
                $shift1 = $logbook->details->where('shift_id', 1)->first();
                $shift2 = $logbook->details->where('shift_id', 2)->first();
                $shift3 = $logbook->details->where('shift_id', 3)->first();

                $totalOmzet = ($shift1?->total_uang ?? 0);
                if ($jumlahShiftSetting >= 2) {
                    $totalOmzet += ($shift2?->total_uang ?? 0);
                }
                if ($jumlahShiftSetting == 3) {
                    $totalOmzet += ($shift3?->total_uang ?? 0);
                }

                $row = [
                    $logbook->tanggal->format('Y-m-d'),
                    $logbook->kas_awal,
                    $logbook->kas_akhir ?? '-',
                    $shift1?->jumlah_print ?? 0,
                    $shift1?->jumlah_fotokopi ?? 0,
                    $shift1?->jumlah_jilid ?? 0,
                    $shift1?->total_uang ?? 0,
                ];

                if ($jumlahShiftSetting >= 2) {
                    $row[] = $shift2?->jumlah_print ?? 0;
                    $row[] = $shift2?->jumlah_fotokopi ?? 0;
                    $row[] = $shift2?->jumlah_jilid ?? 0;
                    $row[] = $shift2?->total_uang ?? 0;
                }

                if ($jumlahShiftSetting == 3) {
                    $row[] = $shift3?->jumlah_print ?? 0;
                    $row[] = $shift3?->jumlah_fotokopi ?? 0;
                    $row[] = $shift3?->jumlah_jilid ?? 0;
                    $row[] = $shift3?->total_uang ?? 0;
                }

                $row[] = $totalOmzet;
                $row[] = $logbook->stok_kertas ?? '-';
                $row[] = $logbook->status_mesin ?? '-';

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
