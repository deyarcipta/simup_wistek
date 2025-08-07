<?php
// app/Models/Piutang.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Piutang extends Model
{
    protected $table = 'piutang';

    protected $fillable = [
        'tanggal_peminjaman',
        'nama_barang',
        'jumlah_barang',
        'nominal',
        'kepada',
        'sisa_nominal',
    ];
}
