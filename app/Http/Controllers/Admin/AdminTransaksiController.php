<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\ProdukJasa;
use App\Models\StokBarang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
    
        $produkJasa = ProdukJasa::with('stokBarang')->get();
    
        $transaksi = Transaksi::with(['details.produkJasa', 'user'])
            ->when($search, function ($query) use ($search) {
                $query->where('kode_transaksi', 'like', "%{$search}%")
                      ->orWhere('nama_pembeli', 'like', "%{$search}%");
            })
            ->latest() 
            ->paginate(20)
            ->appends(['search' => $search]);

        $hasStartedLogbook = true; // Admin can always access POS cashier
    
        return view('admin.transaksi.index', compact('produkJasa', 'transaksi', 'search', 'hasStartedLogbook'));
    }


    public function store(Request $request)
    {   
        if ($request->has('cart') || $request->isJson()) {
            $cartData = $request->input('cart');
            if (is_string($cartData)) {
                $cartData = json_decode($cartData, true);
            }

            if (empty($cartData)) {
                return $request->expectsJson() || $request->ajax()
                    ? response()->json(['success' => false, 'message' => 'Keranjang kosong.'], 422)
                    : redirect()->back()->with('error', 'Keranjang kosong.');
            }

            DB::transaction(function () use ($cartData) {
                // Generate kode transaksi POS
                $kodeTransaksi = 'POS-' . strtoupper(Str::random(6));
                $userId = Auth::id();

                // Simpan transaksi utama terlebih dahulu dengan total 0
                $transaksi = Transaksi::create([
                    'kode_transaksi' => $kodeTransaksi,
                    'tanggal'        => now(),
                    'nama_pembeli'   => 'Umum',
                    'total'          => 0,
                    'user_id'        => $userId,
                ]);

                $total = 0;

                foreach ($cartData as $item) {
                    $produk = ProdukJasa::findOrFail($item['produk_jasa_id']);
                    $subtotal = $produk->harga * $item['jumlah'];
                    $total += $subtotal;

                    // Simpan detail transaksi
                    TransaksiDetail::create([
                        'transaksi_id'    => $transaksi->id,
                        'produk_jasa_id'  => $produk->id,
                        'jumlah'          => $item['jumlah'],
                        'harga'           => $produk->harga,
                        'subtotal'        => $subtotal
                    ]);

                    // Kurangi stok jika jenis produk
                    if ($produk->jenis === 'produk' && $produk->stok_barang_id) {
                        $stokBarang = StokBarang::find($produk->stok_barang_id);
                        if ($stokBarang) {
                            $stokBarang->stok = max(0, $stokBarang->stok - $item['jumlah']);
                            $stokBarang->save();
                        }
                    }
                }

                // Update total transaksi
                $transaksi->update(['total' => $total]);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi POS berhasil disimpan.'
                ]);
            }

            return redirect()->back()->with('success', 'Transaksi POS berhasil disimpan.');
        }

        return redirect()->back()->with('error', 'Payload transaksi tidak valid.');
    }

    public function rekap(Request $request)
    {
        $filter = $request->get('filter', 'mingguan');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Transaksi::query();

        if ($startDate && $endDate) {
            // Jika pilih range tanggal manual
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('tanggal', [$start, $end]);
        } else {
            // Filter otomatis
            if ($filter === 'mingguan') {
                $start = now()->startOfWeek();
                $end = now()->endOfWeek();
                $query->whereBetween('tanggal', [$start, $end]);
            } elseif ($filter === 'bulanan') {
                $query->whereYear('tanggal', now()->year)
                    ->whereMonth('tanggal', now()->month);
            } elseif ($filter === 'tahunan') {
                $query->whereYear('tanggal', now()->year);
            }
        }

        $rekap = $query->with('details.produkJasa')->latest()->get();

        // Ubah jadi Carbon biar bisa format di view
        $rekap->transform(function ($item) {
            $item->tanggal = Carbon::parse($item->tanggal);
            return $item;
        });

        return view('admin.transaksi.rekap', compact('rekap', 'filter', 'startDate', 'endDate'));
    }

    public function download()
    {
        $transaksi = Transaksi::with('details.produkJasa')->orderBy('tanggal', 'desc')->get();

        $pdf = Pdf::loadView('admin.transaksi.transaksi_pdf', compact('transaksi'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('Laporan-Transaksi-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadRekap(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate   = $request->end_date ?? now()->endOfMonth()->toDateString();

        $rekap = Transaksi::whereBetween('tanggal', [$startDate, $endDate])->get();

        $pdf = Pdf::loadView('admin.transaksi.rekap_transaksi_pdf', [
            'rekap'      => $rekap,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
            'tanggalCetak' => now()->format('d/m/Y H:i')
        ])->setPaper('A4', 'portrait');

        return $pdf->download('Rekap-Transaksi-' . $startDate . '-sd-' . $endDate . '.pdf');
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::with('details')->findOrFail($id);

        foreach ($transaksi->details as $detail) {
            // Ambil produk_jasa terkait
            $produk = ProdukJasa::find($detail->produk_jasa_id);

            if ($produk && $produk->jenis === 'produk') {
                // Jika produk terhubung ke stok_barang, kembalikan stoknya juga
                if ($produk->stok_barang_id) {
                    $stokBarang = StokBarang::find($produk->stok_barang_id);
                    if ($stokBarang) {
                        $stokBarang->stok += $detail->jumlah;
                        $stokBarang->save();
                    }
                }
            }
        }

        // Hapus transaksi (Laravel akan otomatis hapus details jika relasi onDelete cascade)
        $transaksi->delete();

        return redirect()->back()->with('success', 'Transaksi berhasil dihapus dan stok dikembalikan.');
    }
}

