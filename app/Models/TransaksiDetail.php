<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    protected $table = 'transaksi_detail';
    protected $fillable = ['transaksi_id', 'produk_jasa_id', 'jumlah', 'harga', 'subtotal'];

    public function produkJasa() {
        return $this->belongsTo(ProdukJasa::class, 'produk_jasa_id');
    }
}
