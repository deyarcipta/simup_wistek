<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokBarang extends Model
{
    protected $table = 'stok_barang';

    protected $fillable = [
        'nama_barang',
        'satuan',
        'stok',
        'harga_beli',
        'harga_jual',
    ];

    public function produkJasa()
    {
        return $this->hasMany(ProdukJasa::class, 'stok_barang_id');
    }

}
