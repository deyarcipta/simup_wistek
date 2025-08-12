<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdukJasa;

class ProdukJasaController extends Controller
{
    public function index()
    {
        $produkJasa = ProdukJasa::with('stokBarang')->orderBy('created_at', 'desc')->get();
        return view('operator.produk_jasa.index', compact('produkJasa'));
    }

    public function getData($id)
    {
        $produkJasa = ProdukJasa::with('stokBarang')->findOrFail($id);
        return response()->json($produkJasa);
    }
}
