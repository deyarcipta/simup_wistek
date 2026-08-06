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
        
        $produkJasaList = \App\Models\ProdukJasa::orderBy('id', 'asc')->get();

        $s1 = $logbook->details->where('shift_id', 1)->first();
        $s2 = $logbook->details->where('shift_id', 2)->first();
        $s3 = $logbook->details->where('shift_id', 3)->first();

        // Time ranges
        $s1Start = $logbook->created_at;
        $s1End = $s1 ? $s1->created_at : Carbon::now();

        $s2Start = $s1 ? $s1->created_at : null;
        $s2End = ($s2 && in_array($logbook->status, ['shift_2_selesai', 'tutup_up'])) 
            ? $s2->updated_at 
            : Carbon::now();

        $s3Start = ($s2 && in_array($logbook->status, ['shift_2_selesai', 'tutup_up'])) 
            ? $s2->updated_at 
            : null;
        $s3End = ($s3 && $logbook->status === 'tutup_up') 
            ? $s3->updated_at 
            : Carbon::now();

        $getShiftData = function ($start, $end) use ($produkJasaList) {
            if (!$start || !$end) {
                return [
                    'transaksi' => collect(),
                    'summary' => ['total_uang' => 0],
                    'productSummaries' => [],
                ];
            }

            $transaksi = \App\Models\Transaksi::with(['details.produkJasa', 'user'])
                ->whereHas('user', function ($query) {
                    $query->where('role', '!=', 'admin');
                })
                ->whereBetween('created_at', [$start, $end])
                ->get();

            $productSummaries = [];
            foreach ($produkJasaList as $product) {
                $productSummaries[$product->id] = [
                    'nama' => $product->nama,
                    'kuantitas' => 0,
                    'tarif' => $product->harga,
                    'subtotal' => 0,
                ];
            }

            $totalUang = 0;
            foreach ($transaksi as $tx) {
                foreach ($tx->details as $detail) {
                    $prodId = $detail->produk_jasa_id;
                    if (isset($productSummaries[$prodId])) {
                        $productSummaries[$prodId]['kuantitas'] += $detail->jumlah;
                        $productSummaries[$prodId]['tarif'] = $detail->harga;
                        $productSummaries[$prodId]['subtotal'] += $detail->subtotal;
                    }
                    $totalUang += $detail->subtotal;
                }
            }

            return [
                'transaksi' => $transaksi,
                'summary' => [
                    'total_uang' => $totalUang,
                ],
                'productSummaries' => $productSummaries,
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

        $produkJasaList = \App\Models\ProdukJasa::orderBy('id', 'asc')->get();

        return view('admin.logbook.show', compact('logbook', 'shift1Real', 'shift2Real', 'shift3Real', 'jumlahShiftSetting', 'produkJasaList'));
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

    public function kehadiran(Request $request)
    {
        $search = $request->get('search');
        $shiftFilter = $request->get('shift_id');

        $query = \App\Models\LogbookDetail::with(['logbook', 'user', 'shift'])
            ->whereHas('logbook')
            ->whereHas('user');

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($shiftFilter) {
            $query->where('shift_id', $shiftFilter);
        }

        $details = $query->orderBy('waktu_mulai', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->appends(['search' => $search, 'shift_id' => $shiftFilter]);

        // Query terpisah tanpa paginasi untuk rekap per operator
        $rekapQuery = \App\Models\LogbookDetail::with(['user', 'logbook'])
            ->whereHas('logbook')
            ->whereHas('user');

        if ($search) {
            $rekapQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($shiftFilter) {
            $rekapQuery->where('shift_id', $shiftFilter);
        }

        $allDetails = $rekapQuery->orderBy('waktu_mulai', 'desc')->get();

        // Kelompokkan per operator untuk tabel Rekap
        $rekapOperator = $allDetails->groupBy('user_id')->map(function ($items) {
            $user = $items->first()->user;
            
            // Dapatkan list tanggal menjaga yang unik
            $dates = $items->map(function ($item) {
                return $item->logbook ? $item->logbook->tanggal->format('d/m/Y') : '';
            })->filter()->unique()->implode(', ');

            return [
                'user' => $user,
                'jumlah_shift' => $items->count(),
                'tanggal_menjaga' => $dates,
            ];
        });

        $shifts = \App\Models\Shift::all();

        return view('admin.logbook.kehadiran', compact('details', 'rekapOperator', 'shifts', 'search', 'shiftFilter'));
    }

    public function downloadKehadiranPdf(Request $request)
    {
        $search = $request->get('search');
        $shiftFilter = $request->get('shift_id');

        $query = \App\Models\LogbookDetail::with(['logbook', 'user', 'shift'])
            ->whereHas('logbook')
            ->whereHas('user');

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($shiftFilter) {
            $query->where('shift_id', $shiftFilter);
        }

        $details = $query->orderBy('waktu_mulai', 'desc')->get();
        $namaShift = $shiftFilter ? (\App\Models\Shift::find($shiftFilter)?->nama_shift ?? 'Shift') : 'Semua Shift';

        $pdf = Pdf::loadView('admin.logbook.kehadiran_pdf', compact('details', 'namaShift'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("Laporan-Kehadiran-Operator-" . now()->format('YmdHis') . ".pdf");
    }

    public function downloadKehadiranExcel(Request $request)
    {
        $search = $request->get('search');
        $shiftFilter = $request->get('shift_id');

        $query = \App\Models\LogbookDetail::with(['logbook', 'user', 'shift'])
            ->whereHas('logbook')
            ->whereHas('user');

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($shiftFilter) {
            $query->where('shift_id', $shiftFilter);
        }

        $details = $query->orderBy('waktu_mulai', 'desc')->get();

        $fileName = "Laporan-Kehadiran-Operator-" . now()->format('YmdHis') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Tanggal', 'Nama Operator', 'Email', 'Shift', 'Jam Check-In', 'Jam Check-Out', 'Status Kehadiran'];

        $callback = function() use($details, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($details as $index => $detail) {
                $logbook = $detail->logbook;
                $lateness = $detail->getLatenessInfo();
                
                // Tentukan check-in
                $checkinTime = $detail->waktu_mulai ?? ($detail->shift_id == 1 ? $logbook->created_at : $detail->created_at);

                // Tentukan jam checkout
                $checkoutTime = null;
                $isCheckedOut = false;
                if ($detail->shift_id == 1) {
                    $checkoutTime = $detail->created_at;
                    $isCheckedOut = true;
                } elseif ($detail->shift_id == 2) {
                    if (in_array($logbook->status, ['shift_2_selesai', 'tutup_up'])) {
                        $checkoutTime = $detail->updated_at;
                        $isCheckedOut = true;
                    }
                } elseif ($detail->shift_id == 3) {
                    if ($logbook->status === 'tutup_up') {
                        $checkoutTime = $detail->updated_at;
                        $isCheckedOut = true;
                    }
                }

                fputcsv($file, [
                    $index + 1,
                    $logbook->tanggal->format('Y-m-d'),
                    $detail->user->name,
                    $detail->user->email,
                    $detail->shift->nama_shift,
                    $checkinTime ? $checkinTime->format('H:i') : '-',
                    ($isCheckedOut && $checkoutTime) ? $checkoutTime->format('H:i') : 'Sedang Bertugas',
                    $lateness['status_text'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
