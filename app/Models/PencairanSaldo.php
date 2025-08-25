<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PencairanSaldo extends Model
{
    use HasFactory;

    protected $table = 'pencairan_saldo';

    protected $fillable = [
        'member_id', 'user_id', 'jumlah'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengeluaran()
    {
        return $this->hasMany(PengeluaranLain::class, 'pencairan_id');
    }
}