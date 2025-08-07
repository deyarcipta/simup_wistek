<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GajiKaryawan extends Model
{
    protected $table = 'gaji_karyawan';
    protected $fillable = ['nama_karyawan', 'total_gaji', 'tanggal'];
}
