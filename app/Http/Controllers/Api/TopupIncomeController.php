<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\ProdukJasa;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use Illuminate\Http\Request;

class TopupIncomeController extends Controller
{
    /**
     * Handle incoming webhook for Wistek Topup income transaction
     */
    public function store(Request $request)
    {
        $secretHeader = $request->header('X-Wistek-Secret');
        $pengaturan = Pengaturan::first();
        $expectedSecret = $pengaturan?->wistek_webhook_secret ?: config('services.wistek_topup.secret', 'wistek_simup_secret_key_2026');

        if (empty($secretHeader) || $secretHeader !== $expectedSecret) {
            return response()->json(['success' => false, 'message' => 'Unauthorized secret header'], 401);
        }

        $request->validate([
            'kode_transaksi' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        $invoice = trim($request->kode_transaksi);

        // Anti-Duplication Check
        $existing = Transaksi::where('kode_transaksi', $invoice)->first();
        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Already synced',
                'transaksi_id' => $existing->id,
            ]);
        }

        // Get or Create Master Product/Service Item
        $produkJasa = ProdukJasa::firstOrCreate(
            ['nama' => 'Layanan Top-Up Game Wistek'],
            [
                'jenis' => 'jasa',
                'harga' => 0,
                'satuan' => 'transaksi',
            ]
        );

        // Get default admin user id
        $user = User::first();
        $userId = $user ? $user->id : 1;

        $namaPembeli = $request->nama_pembeli ?? 'Pelanggan Topup Wistek';
        if ($request->payment_method) {
            $namaPembeli .= " ({$request->payment_method})";
        }

        // Create Transaksi
        $transaksi = Transaksi::create([
            'kode_transaksi' => $invoice,
            'tanggal' => $request->tanggal ?? now(),
            'nama_pembeli' => $namaPembeli,
            'total' => $request->total,
            'user_id' => $userId,
        ]);

        // Create Transaksi Detail
        TransaksiDetail::create([
            'transaksi_id' => $transaksi->id,
            'produk_jasa_id' => $produkJasa->id,
            'jumlah' => 1,
            'harga' => $request->total,
            'subtotal' => $request->total,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Income transaction logged successfully to SIMUP',
            'transaksi_id' => $transaksi->id,
        ]);
    }
}