<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    protected $table = 'logbooks';

    protected $fillable = [
        'tanggal',
        'kas_awal',
        'kas_akhir',
        'stok_kertas',
        'status_mesin',
        'status',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(LogbookDetail::class, 'logbook_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
