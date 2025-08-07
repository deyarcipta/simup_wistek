<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranLain extends Model
{
    use HasFactory;
    protected $table = 'pengeluaran_lain';
    protected $fillable = ['piutang_id','keterangan', 'total', 'tanggal'];
}
