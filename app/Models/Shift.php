<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shifts';

    protected $fillable = [
        'nama_shift',
        'jam_mulai',
        'jam_selesai',
    ];
}
