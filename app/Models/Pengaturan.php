<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = [
        'nama_aplikasi',
        'nama_sekolah',
        'alamat',
        'telepon',
        'email',
        'logo',
    ];
}
