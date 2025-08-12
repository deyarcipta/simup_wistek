<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukJasa extends Model
{
    use HasFactory;

    protected $table = 'produk_jasa';

    protected $fillable = [
        'nama',
        'jenis',
        'harga',
        'jumlah',
        'satuan',
        'stok_barang_id',
    ];

    public function stokBarang()
    {
        return $this->belongsTo(StokBarang::class, 'stok_barang_id');
    }
}
