<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\ProdukJasa;
use App\Models\StokBarang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Member;
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
    
        $produkJasa = ProdukJasa::all();
    
        $transaksi = Transaksi::with(['details.produkJasa', 'user'])
            ->when($search, function ($query) use ($search) {
                $query->where('kode_transaksi', 'like', "%{$search}%")
                      ->orWhere('nama_pembeli', 'like', "%{$search}%");
            })
            ->latest() 
            ->paginate(20)
            ->appends(['search' => $search]);
    
        return view('admin.transaksi.index', compact('produkJasa', 'transaksi', 'search'));
    }


    public function store(Request $request)
    {   
        $request->validate([
            'produk_jasa_id' => 'required|exists:produk_jasa,id',
            'jumlah'         => 'required|integer|min:1',
            'nama_pembeli'   => 'nullable|string|max:255',
            'member_id'      => 'nullable|exists:members,id',
        ]);

        DB::transaction(function () use ($request) {
            $produk = ProdukJasa::findOrFail($request->produk_jasa_id);

            // Tentukan prefix kode transaksi
            $prefix = $produk->jenis === 'produk' ? 'PRX' : 'JRX';
            $kodeTransaksi = $prefix . '-' . strtoupper(Str::random(6));

            // Hitung total harga
            $total = $produk->harga * $request->jumlah;
            $userId = Auth::id(); // Ambil ID user yang sedang login.
            $memberId = $request->member_id;
            
            // Simpan transaksi (tambahkan user_id)
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'tanggal'        => now(),
                'nama_pembeli'   => $request->nama_pembeli,
                'total'          => $total,
                'user_id'        => $userId, // user yang login
                'member_id'      => $memberId,
            ]);

            // Simpan detail transaksi
            TransaksiDetail::create([
                'transaksi_id'    => $transaksi->id,
                'produk_jasa_id'  => $produk->id,
                'jumlah'          => $request->jumlah,
                'harga'           => $produk->harga,
                'subtotal'        => $total
            ]);

            // Kurangi stok jika jenis produk
            if ($produk->jenis === 'produk' && $produk->stok_barang_id) {
                $stokBarang = StokBarang::find($produk->stok_barang_id);
                if ($stokBarang) {
                    $stokBarang->stok = max(0, $stokBarang->stok - $request->jumlah);
                    $stokBarang->save();
                }
            }

            // Tambahkan saldo member sesuai ketentuan
            if ($memberId) {
                $member = Member::find($memberId);
                $persen = 0;
                if ($total < 30000) $persen = 0.1;
                elseif ($total <= 50000) $persen = 0.12;
                else $persen = 0.15;

                $member->saldo += $total * $persen;
                $member->save();
            }
        });

        return redirect()->back()->with('success', 'Transaksi berhasil disimpan.');
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

            // Kembalikan saldo member jika transaksi terkait member
            if ($transaksi->member_id) {
                $member = Member::find($transaksi->member_id);
                if ($member) {
                    $total = $transaksi->total;
                    $persen = 0;

                    // Hitung persentase sesuai total transaksi
                    if ($total < 30000) $persen = 0.1;       // 5%
                    elseif ($total <= 50000) $persen = 0.12;  // 7%
                    else $persen = 0.15;                        // 10%

                    // Kurangi saldo member
                    $member->saldo -= $total * $persen;
                    $member->save();
                }
            }
        }

        // Hapus transaksi (Laravel akan otomatis hapus details jika relasi onDelete cascade)
        $transaksi->delete();

        return redirect()->back()->with('success', 'Transaksi berhasil dihapus dan stok dikembalikan.');
    }
}

