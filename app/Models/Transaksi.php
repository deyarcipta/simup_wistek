<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = ['kode_transaksi', 'tanggal', 'nama_pembeli', 'total', 'user_id'];
    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function details() {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}