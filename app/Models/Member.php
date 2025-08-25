<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'user_id',
        'nama',
        'no_hp',
        'saldo',
    ];

    // relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // relasi ke transaksi (member_id di tabel transaksi)
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'member_id');
    }
}
