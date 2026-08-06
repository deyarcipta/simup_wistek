<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logbook;
use App\Models\LogbookDetail;
use App\Models\ProdukJasa;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LogbookController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $logbook = Logbook::with(['details.shift', 'details.user', 'user'])->where('tanggal', $today)->first();
        $jumlahShiftSetting = \App\Models\Pengaturan::first()?->jumlah_shift ?? 2;

        // Cari ID layanan dari database
        $printId = ProdukJasa::where('nama', 'LIKE', '%PRINT HITAM%')->first()?->id ?? 2;
        $fotokopiId = ProdukJasa::where('nama', 'LIKE', '%FOTOCOPY%')->first()?->id ?? 9;
        $jilidId = ProdukJasa::where('nama', 'LIKE', '%JILID%')->first()?->id ?? 12;

        // Ambil tarif dinamis saat ini dari tabel produk_jasa
        $tarifPrint = ProdukJasa::find($printId)?->harga ?? 500;
        $tarifFotokopi = ProdukJasa::find($fotokopiId)?->harga ?? 500;
        $tarifJilid = ProdukJasa::find($jilidId)?->harga ?? 5000;

        $autoFill = [
            'print' => 0,
            'fotokopi' => 0,
            'jilid' => 0,
            'lain' => 0,
        ];

        $isDifferentOperator = false;
        $operatorName = '';
        $hasStartedShift2 = false;
        $isDifferentOperatorShift2 = false;
        $operatorNameShift2 = '';
        $hasStartedShift3 = false;
        $isDifferentOperatorShift3 = false;
        $operatorNameShift3 = '';

        if ($logbook) {
            if ($logbook->status === 'aktif') {
                if ($logbook->user_id && $logbook->user_id !== Auth::id()) {
                    $isDifferentOperator = true;
                    $operatorName = $logbook->user?->name ?? 'Operator lain';
                }
            } elseif ($logbook->status === 'shift_1_selesai') {
                $shift2Detail = $logbook->details->where('shift_id', 2)->first();
                if ($shift2Detail) {
                    $hasStartedShift2 = true;
                    if ($shift2Detail->user_id !== Auth::id()) {
                        $isDifferentOperatorShift2 = true;
                        $operatorNameShift2 = $shift2Detail->user?->name ?? 'Operator lain';
                    }
                }
            } elseif ($logbook->status === 'shift_2_selesai') {
                $shift3Detail = $logbook->details->where('shift_id', 3)->first();
                if ($shift3Detail) {
                    $hasStartedShift3 = true;
                    if ($shift3Detail->user_id !== Auth::id()) {
                        $isDifferentOperatorShift3 = true;
                        $operatorNameShift3 = $shift3Detail->user?->name ?? 'Operator lain';
                    }
                }
            }
        }

        // Hitung auto-fill jika dalam tahap pengisian shift
        if ($logbook) {
            if ($logbook->status === 'aktif' && !$isDifferentOperator) {
                // Shift 1: Dinamis dari logbook dibuat s.d. sekarang
                $start = $logbook->created_at;
                $end = Carbon::now();

                $autoFill['print'] = $this->getTransactionCount($printId, $start, $end);
                $autoFill['fotokopi'] = $this->getTransactionCount($fotokopiId, $start, $end);
                $autoFill['jilid'] = $this->getTransactionCount($jilidId, $start, $end);
                $autoFill['lain'] = $this->getOtherTransactionTotal($printId, $fotokopiId, $jilidId, $start, $end);
            } elseif ($logbook->status === 'shift_1_selesai' && $hasStartedShift2 && !$isDifferentOperatorShift2) {
                // Shift 2: Dinamis setelah Shift 1 diselesaikan s.d. sekarang
                $shift1Detail = $logbook->details->where('shift_id', 1)->first();
                $start = $shift1Detail ? $shift1Detail->created_at : $logbook->created_at;
                $end = Carbon::now();

                $autoFill['print'] = $this->getTransactionCount($printId, $start, $end);
                $autoFill['fotokopi'] = $this->getTransactionCount($fotokopiId, $start, $end);
                $autoFill['jilid'] = $this->getTransactionCount($jilidId, $start, $end);
                $autoFill['lain'] = $this->getOtherTransactionTotal($printId, $fotokopiId, $jilidId, $start, $end);
            } elseif ($logbook->status === 'shift_2_selesai' && $hasStartedShift3 && !$isDifferentOperatorShift3) {
                // Shift 3: Dinamis setelah Shift 2 diselesaikan s.d. sekarang
                $shift2Detail = $logbook->details->where('shift_id', 2)->first();
                $start = $shift2Detail ? $shift2Detail->created_at : $logbook->created_at;
                $end = Carbon::now();

                $autoFill['print'] = $this->getTransactionCount($printId, $start, $end);
                $autoFill['fotokopi'] = $this->getTransactionCount($fotokopiId, $start, $end);
                $autoFill['jilid'] = $this->getTransactionCount($jilidId, $start, $end);
                $autoFill['lain'] = $this->getOtherTransactionTotal($printId, $fotokopiId, $jilidId, $start, $end);
            }
        }

        return view('operator.logbook.index', compact(
            'logbook', 'tarifPrint', 'tarifFotokopi', 'tarifJilid', 'autoFill', 
            'isDifferentOperator', 'operatorName', 'hasStartedShift2', 
            'isDifferentOperatorShift2', 'operatorNameShift2',
            'hasStartedShift3', 'isDifferentOperatorShift3', 'operatorNameShift3',
            'jumlahShiftSetting'
        ));
    }

    public function startDay(Request $request)
    {
        $request->validate([
            'kas_awal' => 'required|numeric|min:0',
        ]);

        $today = Carbon::today();

        // Validasi double logbook di hari yang sama
        $exists = Logbook::where('tanggal', $today)->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Logbook hari ini sudah dibuat.');
        }

        Logbook::create([
            'tanggal' => $today,
            'kas_awal' => $request->kas_awal,
            'status' => 'aktif',
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Hari berhasil dimulai. Selamat bertugas di Shift 1 Pagi!');
    }

    public function submitShift1(Request $request)
    {
        $today = Carbon::today();
        $logbook = Logbook::where('tanggal', $today)->where('status', 'aktif')->firstOrFail();
        $jumlahShiftSetting = \App\Models\Pengaturan::first()?->jumlah_shift ?? 2;

        // Validasi operator: Hanya operator yang memulai hari operasional yang dapat menyelesaikan Shift 1
        if ($logbook->user_id && $logbook->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Hanya operator yang memulai hari operasional yang dapat menyelesaikan Shift 1.');
        }

        if ($jumlahShiftSetting == 1) {
            $request->validate([
                'stok_kertas' => 'required|in:Aman,Habis',
                'status_mesin' => 'nullable|string',
                'kas_akhir' => 'required|numeric|min:0',
            ]);
        }

        // Cari ID layanan
        $printId = ProdukJasa::where('nama', 'LIKE', '%PRINT HITAM%')->first()?->id ?? 2;
        $fotokopiId = ProdukJasa::where('nama', 'LIKE', '%FOTOCOPY%')->first()?->id ?? 9;
        $jilidId = ProdukJasa::where('nama', 'LIKE', '%JILID%')->first()?->id ?? 12;

        // Ambil tarif dinamis saat ini (untuk dikunci di detail logbook)
        $tarifPrint = ProdukJasa::find($printId)?->harga ?? 500;
        $tarifFotokopi = ProdukJasa::find($fotokopiId)?->harga ?? 500;
        $tarifJilid = ProdukJasa::find($jilidId)?->harga ?? 5000;

        // Hitung kuantitas langsung dari database (dari logbook dibuat s.d. sekarang)
        $start = $logbook->created_at;
        $end = Carbon::now();

        $jumlahPrint = $this->getTransactionCount($printId, $start, $end);
        $jumlahFotokopi = $this->getTransactionCount($fotokopiId, $start, $end);
        $jumlahJilid = $this->getTransactionCount($jilidId, $start, $end);
        $pendapatanLain = $this->getOtherTransactionTotal($printId, $fotokopiId, $jilidId, $start, $end);

        $totalUang = ($jumlahPrint * $tarifPrint) + 
                     ($jumlahFotokopi * $tarifFotokopi) + 
                     ($jumlahJilid * $tarifJilid) +
                     $pendapatanLain;

        DB::transaction(function () use ($logbook, $request, $tarifPrint, $tarifFotokopi, $tarifJilid, $totalUang, $pendapatanLain, $jumlahPrint, $jumlahFotokopi, $jumlahJilid, $jumlahShiftSetting) {
            // Simpan detail shift 1 (shift_id = 1)
            LogbookDetail::create([
                'logbook_id' => $logbook->id,
                'shift_id' => 1,
                'user_id' => Auth::id(),
                'waktu_mulai' => $logbook->created_at,
                'jumlah_print' => $jumlahPrint,
                'harga_print' => $tarifPrint,
                'jumlah_fotokopi' => $jumlahFotokopi,
                'harga_fotokopi' => $tarifFotokopi,
                'jumlah_jilid' => $jumlahJilid,
                'harga_jilid' => $tarifJilid,
                'total_uang' => $totalUang,
                'pendapatan_lain' => $pendapatanLain,
            ]);

            // Update status logbook
            if ($jumlahShiftSetting == 1) {
                $logbook->update([
                    'kas_akhir' => $request->kas_akhir,
                    'stok_kertas' => $request->stok_kertas,
                    'status_mesin' => $request->status_mesin,
                    'status' => 'tutup_up',
                ]);
            } else {
                $logbook->update([
                    'status' => 'shift_1_selesai',
                ]);
            }
        });

        if ($jumlahShiftSetting == 1) {
            return redirect()->back()->with('success', 'Hari operasional ditutup! Terima kasih atas laporan logbook Anda.');
        }
        return redirect()->back()->with('success', 'Shift 1 Pagi berhasil diselesaikan. Menunggu input Shift 2 Siang.');
    }

    public function startShift2(Request $request)
    {
        $today = Carbon::today();
        $logbook = Logbook::where('tanggal', $today)->where('status', 'shift_1_selesai')->firstOrFail();

        // Cari ID layanan
        $printId = ProdukJasa::where('nama', 'LIKE', '%PRINT HITAM%')->first()?->id ?? 2;
        $fotokopiId = ProdukJasa::where('nama', 'LIKE', '%FOTOCOPY%')->first()?->id ?? 9;
        $jilidId = ProdukJasa::where('nama', 'LIKE', '%JILID%')->first()?->id ?? 12;

        // Ambil tarif dinamis saat ini (untuk dikunci di detail logbook)
        $tarifPrint = ProdukJasa::find($printId)?->harga ?? 500;
        $tarifFotokopi = ProdukJasa::find($fotokopiId)?->harga ?? 500;
        $tarifJilid = ProdukJasa::find($jilidId)?->harga ?? 5000;

        // Cek apakah detail Shift 2 sudah dibuat
        $exists = LogbookDetail::where('logbook_id', $logbook->id)->where('shift_id', 2)->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Shift 2 sudah dimulai.');
        }

        // Buat record detail Shift 2 awal dengan user_id operator yang memulai
        LogbookDetail::create([
            'logbook_id' => $logbook->id,
            'shift_id' => 2,
            'user_id' => Auth::id(),
            'waktu_mulai' => Carbon::now(),
            'jumlah_print' => 0,
            'harga_print' => $tarifPrint,
            'jumlah_fotokopi' => 0,
            'harga_fotokopi' => $tarifFotokopi,
            'jumlah_jilid' => 0,
            'harga_jilid' => $tarifJilid,
            'total_uang' => 0,
            'pendapatan_lain' => 0,
        ]);

        return redirect()->back()->with('success', 'Shift 2 Siang berhasil dimulai. Selamat bertugas!');
    }

    public function submitShift2(Request $request)
    {
        $jumlahShiftSetting = \App\Models\Pengaturan::first()?->jumlah_shift ?? 2;

        if ($jumlahShiftSetting == 2) {
            $request->validate([
                'stok_kertas' => 'required|in:Aman,Habis',
                'status_mesin' => 'nullable|string',
                'kas_akhir' => 'required|numeric|min:0',
            ]);
        }

        $today = Carbon::today();
        $logbook = Logbook::where('tanggal', $today)->where('status', 'shift_1_selesai')->firstOrFail();

        // Validasi operator: Hanya operator yang memulai Shift 2 yang dapat menyelesaikan Shift 2
        $shift2Detail = LogbookDetail::where('logbook_id', $logbook->id)->where('shift_id', 2)->firstOrFail();
        if ($shift2Detail->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Hanya operator yang memulai Shift 2 yang dapat memproses Shift 2.');
        }

        // Cari ID layanan
        $printId = ProdukJasa::where('nama', 'LIKE', '%PRINT HITAM%')->first()?->id ?? 2;
        $fotokopiId = ProdukJasa::where('nama', 'LIKE', '%FOTOCOPY%')->first()?->id ?? 9;
        $jilidId = ProdukJasa::where('nama', 'LIKE', '%JILID%')->first()?->id ?? 12;

        // Ambil tarif dinamis saat ini (untuk dikunci di detail logbook)
        $tarifPrint = ProdukJasa::find($printId)?->harga ?? 500;
        $tarifFotokopi = ProdukJasa::find($fotokopiId)?->harga ?? 500;
        $tarifJilid = ProdukJasa::find($jilidId)?->harga ?? 5000;

        // Hitung kuantitas langsung dari database (dari Shift 1 diselesaikan s.d. sekarang)
        $shift1Detail = LogbookDetail::where('logbook_id', $logbook->id)->where('shift_id', 1)->first();
        $start = $shift1Detail ? $shift1Detail->created_at : $logbook->created_at;
        $end = Carbon::now();

        $jumlahPrint = $this->getTransactionCount($printId, $start, $end);
        $jumlahFotokopi = $this->getTransactionCount($fotokopiId, $start, $end);
        $jumlahJilid = $this->getTransactionCount($jilidId, $start, $end);
        $pendapatanLain = $this->getOtherTransactionTotal($printId, $fotokopiId, $jilidId, $start, $end);

        $totalUang = ($jumlahPrint * $tarifPrint) + 
                     ($jumlahFotokopi * $tarifFotokopi) + 
                     ($jumlahJilid * $tarifJilid) +
                     $pendapatanLain;

        DB::transaction(function () use ($logbook, $request, $tarifPrint, $tarifFotokopi, $tarifJilid, $totalUang, $pendapatanLain, $jumlahPrint, $jumlahFotokopi, $jumlahJilid, $shift2Detail, $jumlahShiftSetting) {
            // Update detail shift 2 yang sudah dibuat sebelumnya
            $shift2Detail->update([
                'jumlah_print' => $jumlahPrint,
                'harga_print' => $tarifPrint,
                'jumlah_fotokopi' => $jumlahFotokopi,
                'harga_fotokopi' => $tarifFotokopi,
                'jumlah_jilid' => $jumlahJilid,
                'harga_jilid' => $tarifJilid,
                'total_uang' => $totalUang,
                'pendapatan_lain' => $pendapatanLain,
            ]);

            // Update logbook dengan info penutupan
            if ($jumlahShiftSetting == 2) {
                $logbook->update([
                    'kas_akhir' => $request->kas_akhir,
                    'stok_kertas' => $request->stok_kertas,
                    'status_mesin' => $request->status_mesin,
                    'status' => 'tutup_up',
                ]);
            } else {
                $logbook->update([
                    'status' => 'shift_2_selesai',
                ]);
            }
        });

        if ($jumlahShiftSetting == 2) {
            return redirect()->back()->with('success', 'Hari operasional ditutup! Terima kasih atas laporan logbook Anda.');
        }
        return redirect()->back()->with('success', 'Shift 2 Siang berhasil diselesaikan. Menunggu input Shift 3 Sore.');
    }

    public function startShift3(Request $request)
    {
        $today = Carbon::today();
        $logbook = Logbook::where('tanggal', $today)->where('status', 'shift_2_selesai')->firstOrFail();

        // Cari ID layanan
        $printId = ProdukJasa::where('nama', 'LIKE', '%PRINT HITAM%')->first()?->id ?? 2;
        $fotokopiId = ProdukJasa::where('nama', 'LIKE', '%FOTOCOPY%')->first()?->id ?? 9;
        $jilidId = ProdukJasa::where('nama', 'LIKE', '%JILID%')->first()?->id ?? 12;

        // Ambil tarif dinamis saat ini
        $tarifPrint = ProdukJasa::find($printId)?->harga ?? 500;
        $tarifFotokopi = ProdukJasa::find($fotokopiId)?->harga ?? 500;
        $tarifJilid = ProdukJasa::find($jilidId)?->harga ?? 5000;

        // Cek apakah detail Shift 3 sudah dibuat
        $exists = LogbookDetail::where('logbook_id', $logbook->id)->where('shift_id', 3)->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Shift 3 sudah dimulai.');
        }

        // Buat record detail Shift 3 awal dengan user_id operator yang memulai
        LogbookDetail::create([
            'logbook_id' => $logbook->id,
            'shift_id' => 3,
            'user_id' => Auth::id(),
            'waktu_mulai' => Carbon::now(),
            'jumlah_print' => 0,
            'harga_print' => $tarifPrint,
            'jumlah_fotokopi' => 0,
            'harga_fotokopi' => $tarifFotokopi,
            'jumlah_jilid' => 0,
            'harga_jilid' => $tarifJilid,
            'total_uang' => 0,
            'pendapatan_lain' => 0,
        ]);

        return redirect()->back()->with('success', 'Shift 3 Sore berhasil dimulai. Selamat bertugas!');
    }

    public function submitShift3(Request $request)
    {
        $request->validate([
            'stok_kertas' => 'required|in:Aman,Habis',
            'status_mesin' => 'nullable|string',
            'kas_akhir' => 'required|numeric|min:0',
        ]);

        $today = Carbon::today();
        $logbook = Logbook::where('tanggal', $today)->where('status', 'shift_2_selesai')->firstOrFail();

        // Validasi operator: Hanya operator yang memulai Shift 3 yang dapat menutup UP
        $shift3Detail = LogbookDetail::where('logbook_id', $logbook->id)->where('shift_id', 3)->firstOrFail();
        if ($shift3Detail->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Hanya operator yang memulai Shift 3 yang dapat menutup hari operasional.');
        }

        // Cari ID layanan
        $printId = ProdukJasa::where('nama', 'LIKE', '%PRINT HITAM%')->first()?->id ?? 2;
        $fotokopiId = ProdukJasa::where('nama', 'LIKE', '%FOTOCOPY%')->first()?->id ?? 9;
        $jilidId = ProdukJasa::where('nama', 'LIKE', '%JILID%')->first()?->id ?? 12;

        // Ambil tarif dinamis saat ini (untuk dikunci di detail logbook)
        $tarifPrint = ProdukJasa::find($printId)?->harga ?? 500;
        $tarifFotokopi = ProdukJasa::find($fotokopiId)?->harga ?? 500;
        $tarifJilid = ProdukJasa::find($jilidId)?->harga ?? 5000;

        // Hitung kuantitas langsung dari database (dari Shift 2 diselesaikan s.d. sekarang)
        $shift2Detail = LogbookDetail::where('logbook_id', $logbook->id)->where('shift_id', 2)->first();
        $start = $shift2Detail ? $shift2Detail->created_at : $logbook->created_at;
        $end = Carbon::now();

        $jumlahPrint = $this->getTransactionCount($printId, $start, $end);
        $jumlahFotokopi = $this->getTransactionCount($fotokopiId, $start, $end);
        $jumlahJilid = $this->getTransactionCount($jilidId, $start, $end);
        $pendapatanLain = $this->getOtherTransactionTotal($printId, $fotokopiId, $jilidId, $start, $end);

        $totalUang = ($jumlahPrint * $tarifPrint) + 
                     ($jumlahFotokopi * $tarifFotokopi) + 
                     ($jumlahJilid * $tarifJilid) +
                     $pendapatanLain;

        DB::transaction(function () use ($logbook, $request, $tarifPrint, $tarifFotokopi, $tarifJilid, $totalUang, $pendapatanLain, $jumlahPrint, $jumlahFotokopi, $jumlahJilid, $shift3Detail) {
            // Update detail shift 3 yang sudah dibuat sebelumnya
            $shift3Detail->update([
                'jumlah_print' => $jumlahPrint,
                'harga_print' => $tarifPrint,
                'jumlah_fotokopi' => $jumlahFotokopi,
                'harga_fotokopi' => $tarifFotokopi,
                'jumlah_jilid' => $jumlahJilid,
                'harga_jilid' => $tarifJilid,
                'total_uang' => $totalUang,
                'pendapatan_lain' => $pendapatanLain,
            ]);

            // Update logbook dengan info penutupan
            $logbook->update([
                'kas_akhir' => $request->kas_akhir,
                'stok_kertas' => $request->stok_kertas,
                'status_mesin' => $request->status_mesin,
                'status' => 'tutup_up',
            ]);
        });

        return redirect()->back()->with('success', 'Hari operasional ditutup! Terima kasih atas laporan logbook Anda.');
    }

    private function getTransactionCount($productId, $start, $end)
    {
        return DB::table('transaksi_detail')
            ->join('transaksi', 'transaksi_detail.transaksi_id', '=', 'transaksi.id')
            ->join('users', 'transaksi.user_id', '=', 'users.id')
            ->where('users.role', '!=', 'admin')
            ->whereBetween('transaksi.created_at', [$start, $end])
            ->where('transaksi_detail.produk_jasa_id', $productId)
            ->sum('transaksi_detail.jumlah');
    }

    private function getOtherTransactionTotal($printId, $copyId, $jilidId, $start, $end)
    {
        return DB::table('transaksi_detail')
            ->join('transaksi', 'transaksi_detail.transaksi_id', '=', 'transaksi.id')
            ->join('users', 'transaksi.user_id', '=', 'users.id')
            ->where('users.role', '!=', 'admin')
            ->whereBetween('transaksi.created_at', [$start, $end])
            ->whereNotIn('transaksi_detail.produk_jasa_id', [$printId, $copyId, $jilidId])
            ->sum('transaksi_detail.subtotal');
    }
}
