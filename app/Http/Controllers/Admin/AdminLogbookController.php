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
            ->get();

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
        return view('admin.logbook.show', compact('logbook'));
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

        $columns = [
            'Tanggal', 
            'Kas Awal', 
            'Kas Akhir', 
            'Shift 1 Pagi - Print', 
            'Shift 1 Pagi - Fotokopi', 
            'Shift 1 Pagi - Jilid', 
            'Shift 1 Pagi - Total',
            'Shift 2 Siang - Print', 
            'Shift 2 Siang - Fotokopi', 
            'Shift 2 Siang - Jilid', 
            'Shift 2 Siang - Total',
            'Total Omzet Harian',
            'Stok Kertas',
            'Status Mesin'
        ];

        $callback = function() use($logbooks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logbooks as $logbook) {
                $shift1 = $logbook->details->where('shift_id', 1)->first();
                $shift2 = $logbook->details->where('shift_id', 2)->first();

                $totalOmzet = ($shift1?->total_uang ?? 0) + ($shift2?->total_uang ?? 0);

                fputcsv($file, [
                    $logbook->tanggal->format('Y-m-d'),
                    $logbook->kas_awal,
                    $logbook->kas_akhir ?? '-',
                    $shift1?->jumlah_print ?? 0,
                    $shift1?->jumlah_fotokopi ?? 0,
                    $shift1?->jumlah_jilid ?? 0,
                    $shift1?->total_uang ?? 0,
                    $shift2?->jumlah_print ?? 0,
                    $shift2?->jumlah_fotokopi ?? 0,
                    $shift2?->jumlah_jilid ?? 0,
                    $shift2?->total_uang ?? 0,
                    $totalOmzet,
                    $logbook->stok_kertas ?? '-',
                    $logbook->status_mesin ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
