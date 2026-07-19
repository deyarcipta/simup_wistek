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
        'shu_pembagian',
    ];

    protected $casts = [
        'shu_pembagian' => 'array',
    ];

    public function getShuPembagianOrDefault()
    {
        return $this->shu_pembagian ?? [
            ['penerima' => 'Jurusan TKJ',   'persentase' => 40],
            ['penerima' => 'Unit Produksi', 'persentase' => 30],
            ['penerima' => 'Sekolah',       'persentase' => 20],
            ['penerima' => 'Honor Pegawai', 'persentase' => 10],
        ];
    }
}
